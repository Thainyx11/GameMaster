<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\OpenRouterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(
        private OpenRouterService $openRouterService
    ) {}

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
                    ->select('id', 'role', 'content', 'created_at')
                    ->get();
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

    public function sendMessage(Request $request): StreamedResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:10000',
        ]);

        $user = Auth::user();
        
        $conversation = Conversation::where('id', $request->conversation_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => Message::ROLE_USER,
            'content' => $request->message,
        ]);

        $conversation->touch();

        $systemPrompt = $this->openRouterService->buildSystemPrompt($user);
        $apiMessages = [
            $systemPrompt,
            ...$conversation->getMessagesForApi(),
        ];

        return new StreamedResponse(function () use ($conversation, $apiMessages, $userMessage) {
            if (ob_get_level()) {
                ob_end_clean();
            }

            $fullResponse = '';
            $isFirstMessage = $conversation->messages()->count() === 1;

            try {
                foreach ($this->openRouterService->streamMessage($apiMessages, $conversation->model) as $token) {
                    $fullResponse .= $token;
                    
                    echo "data: " . json_encode(['token' => $token]) . "\n\n";
                    
                    if (ob_get_level()) {
                        ob_flush();
                    }
                    flush();
                }

                $assistantMessage = Message::create([
                    'conversation_id' => $conversation->id,
                    'role' => Message::ROLE_ASSISTANT,
                    'content' => $fullResponse,
                ]);

                if ($isFirstMessage) {
                    $title = $this->openRouterService->generateTitle($userMessage->content);
                    $conversation->update(['title' => $title]);
                    
                    echo "data: " . json_encode([
                        'title' => $title,
                        'conversation_id' => $conversation->id,
                    ]) . "\n\n";
                    flush();
                }

                echo "data: " . json_encode(['done' => true, 'message_id' => $assistantMessage->id]) . "\n\n";
                flush();

            } catch (\Exception $e) {
                echo "data: " . json_encode(['error' => $e->getMessage()]) . "\n\n";
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function deleteConversation(Conversation $conversation): JsonResponse
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }

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
}