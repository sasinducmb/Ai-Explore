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

        // System prompt to ensure educational, kid-friendly responses
        $systemPrompt = "You are a friendly AI assistant for kids aged 8-12, focused on *AI literacy*. Give simple, engaging answers (50-60 words) about *artificial intelligence* (e.g., what AI is, how it works) and *math*, *science*, *history*, or *language arts*. Use *text like this* for emphasis, separate paragraphs with blank lines. Avoid complex words. Redirect non-educational questions to *AI* or school topics.";        $data = [
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
