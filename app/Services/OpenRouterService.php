<?php

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class OpenRouterService
{
    private string $apiKey;
    private string $baseUrl;

    public const DEFAULT_MODEL = 'openai/gpt-4o-mini';
    public const VISION_MODEL = 'openai/gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Récupérer la liste des modèles disponibles
     */
    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            try {
                $response = Http::withToken($this->apiKey)->get("{$this->baseUrl}/models");

                return collect($response->json('data', []))
                    ->filter(fn($model) => isset($model['id'], $model['name']))
                    ->sortBy('name')
                    ->map(fn(array $model): array => [
                        'id' => $model['id'],
                        'name' => $model['name'],
                    ])
                    ->values()
                    ->take(50)
                    ->toArray();
            } catch (\Exception $e) {
                return [
                    ['id' => 'openai/gpt-4o-mini', 'name' => 'GPT-4o Mini'],
                    ['id' => 'openai/gpt-4o', 'name' => 'GPT-4o'],
                    ['id' => 'anthropic/claude-3-haiku', 'name' => 'Claude 3 Haiku'],
                ];
            }
        });
    }

    /**
     * Construire le prompt système pour le GameMaster
     */
    public function buildSystemPrompt($user): array
    {
        $basePrompt = <<<PROMPT
Tu es un Maître de Jeu (MJ) expert pour jeux de rôle. Tu crées des aventures immersives et interactives.

## Ton rôle :
- Décrire les environnements de manière vivante et atmosphérique
- Incarner les PNJ avec des personnalités distinctes
- Proposer des choix intéressants au joueur
- Gérer les règles de manière flexible et fun
- Maintenir la tension narrative

## Style :
- Utilise le markdown pour formater tes réponses (gras, italique, listes)
- Décris les scènes au présent
- Pose des questions pour impliquer le joueur
- Suggère des jets de dés quand c'est approprié

## Outils disponibles :
Tu peux utiliser ces commandes spéciales dans tes réponses :
- `/roll NdX` - Lance des dés (ex: /roll 1d20, /roll 2d6+3)
- `/calc expression` - Fait un calcul (ex: /calc 15+7)
- `/datetime` - Affiche la date et l'heure

Quand un joueur demande un jet de dés ou quand la situation l'exige, utilise la commande /roll.

PROMPT;

        if ($user && $user->instructions) {
            $basePrompt .= "\n\n## Instructions personnalisées du joueur :\n" . $user->instructions;
        }

        return [
            'role' => 'system',
            'content' => $basePrompt,
        ];
    }

    /**
     * Stream un message en temps réel vers la sortie (approche du professeur)
     * Output le contenu texte directement (compatible avec useStream de Laravel)
     */
    public function streamToOutput(
        array $messages,
        string $model = self::DEFAULT_MODEL,
        bool $thinkingEnabled = false
    ): void {
        // Si le mode thinking est activé, ajouter une instruction
        if ($thinkingEnabled) {
            $thinkingInstruction = [
                'role' => 'system',
                'content' => "Avant de répondre, réfléchis étape par étape. 
                Commence TOUJOURS ta réponse par ta réflexion entre [REASONING] et [/REASONING].
                Après [/REASONING], donne ta réponse finale au joueur."
            ];
            array_splice($messages, 1, 0, [$thinkingInstruction]);
        }

        $response = Http::withToken($this->apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'stream' => true,
            ]);

        if ($response->failed()) {
            echo "[ERROR] " . ($response->json('error.message') ?? 'HTTP Error');
            $this->flush();
            return;
        }

        foreach ($this->parseSSEStream($response->toPsrResponse()->getBody()) as $event) {
            if ($event['type'] === 'error') {
                echo "[ERROR] " . $event['data'];
                $this->flush();
                return;
            }

            if ($event['type'] === 'content' && $event['data']) {
                echo $event['data'];
                $this->flush();
            }

            if ($event['type'] === 'reasoning' && $event['data']) {
                echo "[REASONING]" . $event['data'] . "[/REASONING]";
                $this->flush();
            }
        }
    }

/**
     * Stream un message et retourne un Generator (pour utilisation avec yield)
     */
    public function streamMessage(array $messages, string $model = self::DEFAULT_MODEL, bool $thinkingEnabled = false): \Generator
    {
        // Si le mode thinking est activé, ajouter une instruction
        if ($thinkingEnabled) {
            $thinkingInstruction = [
                'role' => 'system',
                'content' => "Avant de répondre, réfléchis étape par étape. 
                Commence TOUJOURS ta réponse par ta réflexion entre [REASONING] et [/REASONING].
                Après [/REASONING], donne ta réponse finale au joueur."
            ];
            array_splice($messages, 1, 0, [$thinkingInstruction]);
        }

        $response = Http::withToken($this->apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $messages,
                'stream' => true,
            ]);

        if ($response->failed()) {
            yield "[ERROR] " . ($response->json('error.message') ?? 'HTTP Error');
            return;
        }

        foreach ($this->parseSSEStream($response->toPsrResponse()->getBody()) as $event) {
            if ($event['type'] === 'error') {
                yield "[ERROR] " . $event['data'];
                return;
            }

            if ($event['type'] === 'content' && $event['data']) {
                yield $event['data'];
            }

            if ($event['type'] === 'reasoning' && $event['data']) {
                yield "[REASONING]" . $event['data'] . "[/REASONING]";
            }
        }
    }

    /**
     * Flush la sortie immédiatement
     */
    private function flush(): void
    {
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }

    /**
     * Parse un stream SSE et yield les événements
     */
    private function parseSSEStream(StreamInterface $body): Generator
    {
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($event = $this->parseSSELine($line)) {
                    yield $event;
                }
            }
        }
    }

    /**
     * Parse une ligne SSE
     */
    private function parseSSELine(string $line): ?array
    {
        if ($line === '' || str_starts_with($line, ':')) {
            return null;
        }

        if (!str_starts_with($line, 'data: ')) {
            return null;
        }

        $data = substr($line, 6);

        if ($data === '[DONE]') {
            return ['type' => 'done', 'data' => null];
        }

        return $this->parseJSON($data);
    }

    /**
     * Parse le JSON d'un chunk SSE
     */
    private function parseJSON(string $json): ?array
    {
        try {
            $parsed = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (isset($parsed['error'])) {
                return ['type' => 'error', 'data' => $parsed['error']['message'] ?? 'Unknown error'];
            }

            $delta = $parsed['choices'][0]['delta'] ?? [];

            if (!empty($delta['content'])) {
                return ['type' => 'content', 'data' => $delta['content']];
            }

            if (!empty($delta['reasoning'])) {
                return ['type' => 'reasoning', 'data' => $delta['reasoning']];
            }

            if (!empty($delta['reasoning_content'])) {
                return ['type' => 'reasoning', 'data' => $delta['reasoning_content']];
            }

            return null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Générer un titre pour la conversation
     */
    public function generateTitle(string $firstMessage): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])->timeout(30)->post($this->baseUrl . '/chat/completions', [
                'model' => 'openai/gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Génère un titre court (max 6 mots) pour cette conversation de jeu de rôle. Réponds uniquement avec le titre, sans ponctuation finale.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $firstMessage,
                    ],
                ],
                'max_tokens' => 30,
            ]);

            if ($response->successful()) {
                $title = $response->json('choices.0.message.content', 'Nouvelle aventure');
                return trim($title, " \n\r\t\v\0\"'");
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }

        return 'Nouvelle aventure';
    }

    /**
     * Estimer le nombre de tokens dans un texte
     */
    public static function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Estimer le coût basé sur le modèle et les tokens
     */
    public static function estimateCost(int $inputTokens, int $outputTokens, string $model): float
    {
        $pricing = [
            'openai/gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'openai/gpt-4o' => ['input' => 2.50, 'output' => 10.00],
            'default' => ['input' => 0.50, 'output' => 1.50],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['default'];

        $inputCost = ($inputTokens / 1000000) * $modelPricing['input'];
        $outputCost = ($outputTokens / 1000000) * $modelPricing['output'];

        return $inputCost + $outputCost;
    }

    /**
     * Construire un message utilisateur avec une image
     */
    public static function buildMessageWithImage(string $text, ?string $imageBase64): array
    {
        if (!$imageBase64) {
            return [
                'role' => 'user',
                'content' => $text,
            ];
        }

        return [
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => $text],
                ['type' => 'image_url', 'image_url' => ['url' => $imageBase64]],
            ],
        ];
    }
}