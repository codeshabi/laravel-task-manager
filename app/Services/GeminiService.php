<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeminiService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function analyzeTask(string $title, string $description = null): array
    {
        $prompt = "Analyze this task and provide insights:\nTitle: {$title}";
        if ($description) {
            $prompt .= "\nDescription: {$description}";
        }
        $prompt .= "\n\nProvide analysis on complexity, estimated time, and suggestions.";

        return $this->makeRequest($prompt);
    }

    public function generateDescription(string $title, array $documents = []): string
    {
        $prompt = "Generate a concise, professional task description for: '{$title}'. ";
        $prompt .= "Focus on what needs to be done, key objectives, and deliverables. ";
        $prompt .= "Keep it between 2-4 sentences. Do not include analysis or time estimates.";
        
        if (!empty($documents)) {
            $docNames = array_column($documents, 'name');
            $prompt .= " Reference materials: " . implode(', ', $docNames) . ".";
        }

        $response = $this->makeRequest($prompt);
        return $response['text'] ?? 'Auto-generated description not available.';
    }

    private function makeRequest(string $prompt): array
    {
        if (empty($this->apiKey)) {
            return [
                'text' => 'API key not configured',
                'analysis' => 'Analysis unavailable - API key missing'
            ];
        }

        try {
            $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 200
                    ]
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean up the response text
            $text = trim($text);
            $text = preg_replace('/^(Description:|Task Description:)/i', '', $text);
            $text = trim($text);

            return [
                'text' => $text,
                'analysis' => $text
            ];
        } catch (\Exception $e) {
            \Log::error('Gemini API Error: ' . $e->getMessage());
            return [
                'text' => 'Auto-generation temporarily unavailable',
                'analysis' => 'Analysis unavailable: ' . $e->getMessage()
            ];
        }
    }
}