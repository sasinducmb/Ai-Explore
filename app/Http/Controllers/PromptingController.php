<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromptingController extends Controller
{
    public function show()
    {
        return view('prompting.prompting', ['currentQuestion' => 1]);
    }

    public function submit(Request $request)
    {
        $question = $request->input('question');
        $isCorrect = false;
        $resultMessage = null;
        $currentQuestion = $question;

        if ($question == 1) {
            // Question 1: MCQ about Google's prompting tool
            $request->validate([
                'answer' => 'required|string|in:Grok,Bard,Copilot,ChatGPT,Claude',
            ]);
            $isCorrect = $request->answer === 'Bard';
            $resultMessage = $isCorrect ? 'Correct! Great job!' : 'Oops, that\'s incorrect. Try again!';
            $currentQuestion = $isCorrect ? 2 : 1;
        } elseif ($question == 2) {
            // Question 2: Reverse Prompt Builder with simple keyword analysis
            $request->validate([
                'answer' => 'required|string|max:5000',
            ]);

            // Log the submitted prompt for debugging
            Log::info('Submitted prompt for Question 2: ' . $request->answer);

            try {
                // Simple keyword-based analysis
                $requiredKeywords = [
                    'advantages', 'disadvantages', 'benefits', 'drawbacks',
                    'pollution', 'environment', 'charging', 'electric',
                    'vehicle', 'car', 'ev', 'pros', 'cons'
                ];

                $foundKeywords = [];
                $textLower = strtolower($request->answer);

                foreach ($requiredKeywords as $keyword) {
                    if (strpos($textLower, $keyword) !== false) {
                        $foundKeywords[] = $keyword;
                    }
                }

                // Check for question patterns
                $hasQuestionPattern = (
                    strpos($textLower, 'what') !== false ||
                    strpos($textLower, 'explain') !== false ||
                    strpos($textLower, 'tell') !== false ||
                    strpos($textLower, 'describe') !== false ||
                    strpos($textLower, '?') !== false
                );

                $uniqueKeywords = array_unique($foundKeywords);
                $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                if ($isCorrect) {
                    $resultMessage = 'Correct! Your prompt is good! Found relevant keywords: ' . implode(', ', $uniqueKeywords);
                    $currentQuestion = 3; // Progress to Question 3
                } else {
                    $suggestions = [];
                    if (count($uniqueKeywords) < 2) {
                        $suggestions[] = 'include more relevant keywords like advantages, disadvantages, pollution, or charging';
                    }
                    if (!$hasQuestionPattern) {
                        $suggestions[] = 'make it more like a question or request';
                    }
                    $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                    $currentQuestion = 2; // Stay on Question 2
                }
            } catch (\Exception $e) {
                Log::error('Unexpected error: ' . $e->getMessage());
                $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                $isCorrect = false;
                $currentQuestion = 2;
            }
        } elseif ($question == 3) {
            // Question 3: Build a Super Question with AI
            $request->validate([
                'topic' => 'required|string|in:animals,ocean,robot,computers',
                'answer' => 'required|string|max:5000',
            ]);

            Log::info('Submitted prompt for Question 3: ' . $request->answer . ' | Topic: ' . $request->topic);

            try {
                $textLower = strtolower($request->answer);
                $selectedTopic = $request->input('topic');
                $clarityKeywords = ['explain', 'describe', 'what', 'how', 'why', 'details', 'example', 'specific'];
                $specificityIndicators = ['specific', 'example', 'details', 'type', 'kind', 'particular'];
                $topicKeywords = [
                    'animals' => ['wildlife', 'species', 'habitat', 'behavior', 'migration', 'adaptation'],
                    'ocean' => ['marine', 'sea', 'coral', 'ecosystem', 'fish', 'waves'],
                    'robot' => ['robotics', 'automation', 'programming', 'machine', 'artificial', 'intelligence'],
                    'computers' => ['hardware', 'software', 'programming', 'processor', 'network', 'data']
                ];

                $hasTopic = strpos($textLower, $selectedTopic) !== false;

                $relevantKeywords = $topicKeywords[$selectedTopic];
                $foundTopicKeywords = [];
                foreach ($relevantKeywords as $keyword) {
                    if (strpos($textLower, $keyword) !== false) {
                        $foundTopicKeywords[] = $keyword;
                    }
                }
                $hasEnoughTopicKeywords = count($foundTopicKeywords) >= 2;

                $clarityCount = 0;
                $specificityCount = 0;
                foreach ($clarityKeywords as $keyword) {
                    if (strpos($textLower, $keyword) !== false) {
                        $clarityCount++;
                    }
                }
                foreach ($specificityIndicators as $indicator) {
                    if (strpos($textLower, $indicator) !== false) {
                        $specificityCount++;
                    }
                }

                $hasQuestionPattern = (
                    strpos($textLower, 'what') !== false ||
                    strpos($textLower, 'how') !== false ||
                    strpos($textLower, 'why') !== false ||
                    strpos($textLower, 'explain') !== false ||
                    strpos($textLower, 'describe') !== false ||
                    strpos($textLower, '?') !== false
                );

                $isCorrect = $hasTopic && $hasEnoughTopicKeywords && $clarityCount >= 2 && $specificityCount >= 1 && $hasQuestionPattern;

                if ($isCorrect) {
                    $resultMessage = "Correct! Your improved prompt is clear, detailed, and relevant to '$selectedTopic'! Found topic keywords: " . implode(', ', $foundTopicKeywords);
                    $currentQuestion = 3;
                } else {
                    $suggestions = [];
                    if (!$hasTopic) {
                        $suggestions[] = "include the selected topic '$selectedTopic' in your prompt";
                    }
                    if (!$hasEnoughTopicKeywords) {
                        $suggestions[] = "include more relevant keywords like " . implode(', ', array_slice($relevantKeywords, 0, 3));
                    }
                    if ($clarityCount < 2) {
                        $suggestions[] = 'use clearer terms like explain, describe, or how';
                    }
                    if ($specificityCount < 1) {
                        $suggestions[] = 'add specific details or examples';
                    }
                    if (!$hasQuestionPattern) {
                        $suggestions[] = 'phrase it as a question';
                    }
                    $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found topic keywords: ' . (empty($foundTopicKeywords) ? 'none' : implode(', ', $foundTopicKeywords));
                    $currentQuestion = 3; // Stay on Question 3
                }
            } catch (\Exception $e) {
                // Handle unexpected errors
                Log::error('Unexpected error: ' . $e->getMessage());
                $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                $isCorrect = false;
                $currentQuestion = 3;
            }
        }

        return view('prompting.prompting', [
            'showPopup' => true,
            'isCorrect' => $isCorrect,
            'resultMessage' => $resultMessage,
            'currentQuestion' => $currentQuestion,
            'selectedTopic' => $request->input('topic', null),
        ]);
    }
}
