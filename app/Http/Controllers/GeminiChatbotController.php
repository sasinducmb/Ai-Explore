<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiChatbotController extends Controller
{
    public function chatSimple(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            Log::error('Gemini API key is missing in .env');
            return response()->json([
                'error' => 'Gemini API key is not configured'
            ], 500);
        }

        $model = 'gemini-1.5-flash'; // Supported model for v1 API
        $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

        // System prompt focused exclusively on AI tools with strict filtering
        $systemPrompt = "You are an AI assistant STRICTLY LIMITED to discussing AI tools only. You can ONLY answer questions about: ChatGPT, Bard/Gemini, Claude, Copilot, AI image generators (DALL-E, Midjourney, Stable Diffusion), AI writing tools, AI coding tools, voice assistants (Siri, Alexa, Google Assistant), and how these AI tools work.

IMPORTANT RULES:
- If the question is about ANYTHING other than AI tools (math, science, history, games, homework, personal advice, general knowledge, etc.), you must respond EXACTLY: 'I can only help with questions about AI tools like ChatGPT, Bard, or AI image generators. Please ask me about AI tools instead!'
- Do NOT provide any information, explanations, or help for non-AI tool topics
- Do NOT generate any prompts or content for non-AI tool questions
- Keep AI tool answers simple and engaging (50-60 words max)
- Use *text like this* for emphasis.";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt],
                        ['text' => $request->message]
                    ]
                ]
            ]
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new \Exception('cURL error: ' . curl_error($ch));
            }

            curl_close($ch);

            $responseData = json_decode($response, true);
            Log::info('Gemini API Response: ' . print_r($responseData, true));

            if ($httpCode !== 200) {
                $error = $responseData['error']['message'] ?? 'Unknown error from Gemini API';
                throw new \Exception("Gemini API error: {$error}");
            }

            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                return response()->json([
                    'response' => $responseData['candidates'][0]['content']['parts'][0]['text']
                ]);
            }

            throw new \Exception('Unexpected response format from Gemini');

        } catch (\Exception $e) {
            Log::error('Gemini Simple API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Sorry, something went wrong! Try asking a fun question about math, science, or history!'
            ], 500);
        }
    }

    public function listModels()
    {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            Log::error('Gemini API key is missing in .env');
            return response()->json([
                'error' => 'Gemini API key is not configured'
            ], 500);
        }

        $url = "https://generativelanguage.googleapis.com/v1/models?key={$apiKey}";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new \Exception('cURL error: ' . curl_error($ch));
            }

            curl_close($ch);

            $responseData = json_decode($response, true);
            Log::info('Gemini ListModels Response: ' . print_r($responseData, true));

            if ($httpCode !== 200) {
                $error = $responseData['error']['message'] ?? 'Unknown error from Gemini API';
                throw new \Exception("Gemini API error: {$error}");
            }

            return response()->json([
                'models' => $responseData['models'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini ListModels API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to list models: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        return view('chatbot.gemini-chatbot');
    }
}
