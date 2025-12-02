<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Generator;

class OpenRouterService
{
    public const DEFAULT_MODEL = 'openai/gpt-4o-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/models');

            if ($response->failed()) {
                return [];
            }

            return collect($response->json('data', []))
                ->filter(fn (array $model): bool => 
                    in_array('text', $model['architecture']['output_modalities'] ?? [])
                )
                ->sortBy('name')
                ->map(fn (array $model): array => [
                    'id' => $model['id'],
                    'name' => $model['name'],
                    'description' => $model['description'] ?? '',
                    'context_length' => $model['context_length'] ?? 0,
                ])
                ->values()
                ->toArray();
        });
    }

    public function sendMessage(array $messages, ?string $model = null, float $temperature = 0.8): string
    {
        $model = $model ?? self::DEFAULT_MODEL;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])
            ->timeout(120)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            throw new \RuntimeException("Erreur API OpenRouter: {$error}");
        }

        return $response->json('choices.0.message.content', '');
    }

    public function streamMessage(array $messages, ?string $model = null, float $temperature = 0.8): Generator
    {
        $model = $model ?? self::DEFAULT_MODEL;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => config('app.name'),
        ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'stream' => true,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Erreur de connexion à OpenRouter');
        }

        $body = $response->getBody();
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(1024);
            
            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);
                
                if (str_starts_with($line, 'data: ')) {
                    $data = substr($line, 6);
                    
                    if ($data === '[DONE]') {
                        return;
                    }
                    
                    $json = json_decode($data, true);
                    if (isset($json['choices'][0]['delta']['content'])) {
                        yield $json['choices'][0]['delta']['content'];
                    }
                }
            }
        }
    }

    public function generateTitle(string $firstMessage): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'Tu dois générer un titre court (maximum 6 mots) pour une conversation de jeu de rôle. Réponds UNIQUEMENT avec le titre, sans guillemets ni ponctuation finale.'
            ],
            [
                'role' => 'user',
                'content' => "Génère un titre pour cette conversation qui commence par : \"{$firstMessage}\""
            ]
        ];

        try {
            $title = $this->sendMessage($messages, 'openai/gpt-4o-mini', 0.7);
            $title = trim($title, "\"'");
            return mb_strlen($title) > 100 ? mb_substr($title, 0, 97) . '...' : $title;
        } catch (\Exception $e) {
            return 'Aventure du ' . now()->format('d/m/Y');
        }
    }

    public function buildSystemPrompt(?User $user = null): array
    {
        $date = now()->locale('fr')->isoFormat('dddd D MMMM YYYY');
        
        $prompt = "🎲 Tu es GameMaster, un Maître de Jeu expert et passionné.

PERSONNALITÉ :
- Tu es un narrateur immersif qui crée des ambiances captivantes
- Tu utilises un vocabulaire riche et des descriptions évocatrices
- Tu t'adaptes au style de jeu du joueur (heroic fantasy, horreur, SF, etc.)
- Tu es juste dans tes décisions mais tu favorises toujours le fun
- Tu utilises des émojis thématiques avec parcimonie (🎲⚔️🛡️🗡️🏰🐉✨)

CAPACITÉS :
- Création de quêtes et scénarios sur mesure
- Génération de PNJ mémorables avec personnalités distinctes
- Description de lieux, ambiances et situations
- Gestion de combats et résolution de conflits

RÈGLES :
- Quand le joueur fait une action risquée, suggère un jet de dé approprié
- Utilise le format **texte** pour les éléments importants
- Propose toujours 2-3 options au joueur pour faire avancer l'histoire

Date actuelle : {$date}";

        if ($user && $user->instructions) {
            $prompt .= "\n\nINSTRUCTIONS DU JOUEUR :\n" . $user->instructions;
        }

        return [
            'role' => 'system',
            'content' => $prompt,
        ];
    }
}