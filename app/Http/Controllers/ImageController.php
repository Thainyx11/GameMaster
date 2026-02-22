<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImageController extends Controller
{
    /**
     * Générer une image via OpenRouter (modèle de génération d'images)
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'style' => 'nullable|string|in:fantasy,horror,cyberpunk',
        ]);

        $style = $request->input('style', 'fantasy');
        $prompt = $request->input('prompt');

        // Enrichir le prompt avec le style choisi
        $stylePrompts = [
            'fantasy' => 'Style: epic fantasy art, magical, medieval, dramatic lighting. ',
            'horror' => 'Style: dark horror art, lovecraftian, eerie atmosphere, shadows. ',
            'cyberpunk' => 'Style: cyberpunk neon art, futuristic city, high-tech, glowing lights. ',
        ];

        $fullPrompt = ($stylePrompts[$style] ?? '') . $prompt;

        try {
            $apiKey = config('services.openrouter.api_key');
            $baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');

            // Utiliser un modèle de génération d'images via OpenRouter
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(60)->post($baseUrl . '/chat/completions', [
                'model' => 'openai/gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant qui génère des descriptions d\'images détaillées pour des jeux de rôle. Quand on te donne une description, tu la reformules de manière très détaillée et visuelle en anglais, optimisée pour la génération d\'images IA. Réponds UNIQUEMENT avec la description en anglais, rien d\'autre.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $fullPrompt,
                    ],
                ],
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $enhancedPrompt = $response->json('choices.0.message.content', $fullPrompt);

                // Appeler l'API de génération d'images
                $imageResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->timeout(120)->post($baseUrl . '/images/generations', [
                    'model' => 'openai/dall-e-3',
                    'prompt' => $enhancedPrompt,
                    'n' => 1,
                    'size' => '1024x1024',
                ]);

                if ($imageResponse->successful()) {
                    $imageUrl = $imageResponse->json('data.0.url')
                        ?? $imageResponse->json('data.0.b64_json');

                    if ($imageUrl) {
                        // Si c'est du base64, convertir en data URL
                        if (!str_starts_with($imageUrl, 'http')) {
                            $imageUrl = 'data:image/png;base64,' . $imageUrl;
                        }

                        return response()->json([
                            'success' => true,
                            'url' => $imageUrl,
                        ]);
                    }
                }

                // Fallback : retourner une erreur claire
                return response()->json([
                    'success' => false,
                    'error' => 'Le modèle de génération d\'images n\'est pas disponible. Vérifiez que votre compte OpenRouter a accès à DALL-E 3.',
                ], 422);
            }

            return response()->json([
                'success' => false,
                'error' => 'Erreur API : ' . ($response->json('error.message') ?? 'Erreur inconnue'),
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur : ' . $e->getMessage(),
            ], 500);
        }
    }
}
