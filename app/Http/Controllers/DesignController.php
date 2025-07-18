<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DesignController extends Controller
{
    public function show()
    {
        $currentQuestion = session()->get('current_question', 1); // Default to Question 1
        return view('design.design-tools', compact('currentQuestion'));
    }

    public function submit(Request $request)
    {
        $question = $request->input('question');
        $action = $request->input('action', 'submit'); // Default to 'submit'
        $isCorrect = false;
        $resultMessage = null;
        $currentQuestion = $question;
        $prompt = $request->input('prompt', '');

        if ($action === 'next') {
            // Move to the next question without validation
            if ($question == 1) {
                $currentQuestion = 2;
            } elseif ($question == 2) {
                $currentQuestion = 3;
            } elseif ($question == 3) {
                $currentQuestion = 3; // Stay on last question or redirect
            }
        } elseif ($action === 'finish') {
            // Handle finish action (e.g., redirect to a results page)
            return redirect()->route('design.results');
        } else {
            // Handle submit action with validation
            if ($question == 1) {
                // Question 1: Draw with AI! (No input to validate, just proceed)
                $isCorrect = true; // Assume completion is correct for now
                $resultMessage = 'Great job on your drawing! Move to the next question.';
                $currentQuestion = 2;
            } elseif ($question == 2) {
                // Question 2: AI Picture from Clues with single prompt
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);

                Log::info('Submitted prompt for Question 2: ' . $prompt);

                try {
                    $promptLower = strtolower($prompt);
                    $requiredKeywords = ['fox', 'rock', 'thinking', 'sunset'];
                    $foundKeywords = [];
                    foreach ($requiredKeywords as $keyword) {
                        if (strpos($promptLower, $keyword) !== false) {
                            $foundKeywords[] = $keyword;
                        }
                    }

                    $hasQuestionPattern = (
                        strpos($promptLower, 'create') !== false ||
                        strpos($promptLower, 'generate') !== false ||
                        strpos($promptLower, 'draw') !== false ||
                        strpos($promptLower, 'image') !== false
                    );

                    $uniqueKeywords = array_unique($foundKeywords);
                    $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt is well-suited for the secret story image! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 3; // Move to Question 3
                    } else {
                        $suggestions = [];
                        if (count($uniqueKeywords) < 2) {
                            $missing = array_diff($requiredKeywords, $foundKeywords);
                            $suggestions[] = 'include more keywords like ' . implode(', ', array_slice($missing, 0, 2));
                        }
                        if (!$hasQuestionPattern) {
                            $suggestions[] = 'use action words like create, generate, draw, or image';
                        }
                        $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                        $currentQuestion = 2;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 2;
                }
            } elseif ($question == 3) {
                // Question 3: AI Image Transformation Challenge
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);

                Log::info('Submitted prompt for Question 3: ' . $prompt);

                try {
                    $promptLower = strtolower($prompt);
                    // Example required transformations (modify based on actual Image A and Image B differences)
                    $requiredTransformations = ['add a red hat', 'remove the tree', 'make the sky sunny'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }

                    $isCorrect = count($foundTransformations) >= 2; // Require at least 2 correct transformations

                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 3; // Stay on Question 3 or redirect to results
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 3;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 3;
                }
            }
        }

        // Store current question in session
        session()->put('current_question', $currentQuestion);

        return view('design.design-tools', [
            'showPopup' => $action === 'submit',
            'isCorrect' => $isCorrect,
            'resultMessage' => $resultMessage,
            'currentQuestion' => $currentQuestion,
            'prompt' => $prompt,
        ]);
    }

    public function results()
    {
        // Reset session or clear specific data if needed
        session()->forget('current_question');
        return view('design-results'); // Create this view for results
    }
}
