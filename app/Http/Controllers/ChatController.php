<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(
        private OpenRouterService $openRouterService
    ) {}

    /* =========================================================
     |  AFFICHAGE DU CHAT (INERTIA NORMAL)
     ========================================================= */
    public function index(?int $conversationId = null): Response
    {
        $user = Auth::user();

        $conversations = $user->conversations()
            ->select('id', 'title', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $currentConversation = null;
        $messages = [];

        if ($conversationId) {
            $currentConversation = Conversation::where('id', $conversationId)
                ->where('user_id', $user->id)
                ->first();

            if ($currentConversation) {
                $messages = $currentConversation->messages()
                    ->select('id', 'role', 'content', 'image_path', 'created_at')
                    ->get()
                    ->map(function ($message) {
                        $message->image_url = $message->image_path
                            ? asset('storage/' . $message->image_path)
                            : null;
                        return $message;
                    });
            }
        }

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'currentConversation' => $currentConversation,
            'messages' => $messages,
            'models' => $this->openRouterService->getModels(),
            'defaultModel' => OpenRouterService::DEFAULT_MODEL,
        ]);
    }

    /* =========================================================
     |  CRÉATION DE CONVERSATION
     ========================================================= */
    public function createConversation(Request $request): JsonResponse
    {
        $request->validate([
            'model' => 'required|string',
        ]);

        $conversation = Conversation::create([
            'user_id' => Auth::id(),
            'title' => 'Nouvelle aventure',
            'model' => $request->model,
        ]);

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    /* =========================================================
     |  STREAM MESSAGE (AUCUN INERTIA / AUCUN HTML)
     ========================================================= */
    public function sendMessage(Request $request): StreamedResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:10000',
            'thinking_enabled' => 'nullable|boolean',
            'image_path' => 'nullable|string',
        ]);

        $user = Auth::user();

        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Message utilisateur
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => Message::ROLE_USER,
            'content' => $request->message,
            'image_path' => $request->input('image_path'),
        ]);

        $conversation->touch();

        // Préparer messages API
        $systemPrompt = $this->openRouterService->buildSystemPrompt($user);
        $apiMessages = [$systemPrompt];

        foreach ($conversation->messages()->orderBy('created_at')->get() as $msg) {
            $apiMessages[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        $model = $conversation->model;
        $thinkingEnabled = $request->boolean('thinking_enabled', false);

        return response()->stream(function () use (
            $apiMessages,
            $model,
            $thinkingEnabled
        ) {
            foreach (
                $this->openRouterService->streamMessage(
                    $apiMessages,
                    $model,
                    $thinkingEnabled
                ) as $chunk
            ) {
                echo $chunk;

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }

            // FIN DE STREAM
            echo "\n[DONE]";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            // ⛔ STOP TOTAL (empêche Inertia / Alpine)
            exit;

        }, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /* =========================================================
     |  SUPPRESSION CONVERSATION
     ========================================================= */
    public function deleteConversation(Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /* =========================================================
     |  CHANGEMENT DE MODÈLE
     ========================================================= */
    public function updateModel(Request $request, Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'model' => 'required|string',
        ]);

        $conversation->update(['model' => $request->model]);

        return response()->json(['success' => true]);
    }

    /* =========================================================
     |  EXPORT CONVERSATION
     ========================================================= */
    public function exportConversation(Conversation $conversation, string $format)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $messages = $conversation->messages()->orderBy('created_at')->get();

        if ($format === 'json') {
            return response()->json([
                'title' => $conversation->title,
                'model' => $conversation->model,
                'messages' => $messages,
            ]);
        }

        $markdown = "# {$conversation->title}\n\n";

        foreach ($messages as $message) {
            $markdown .= "**{$message->role}**\n\n{$message->content}\n\n---\n\n";
        }

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown',
            'Content-Disposition' => 'attachment; filename="' . Str::slug($conversation->title) . '.md"',
        ]);
    }
}
