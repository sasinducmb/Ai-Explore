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
        $question = (int) $request->input('question');
        $action = $request->input('action', 'submit'); // Default to 'submit'
        $isCorrect = false;
        $resultMessage = null;
        $currentQuestion = $question;
        $prompt = $request->input('prompt', '');

        $TOTAL_QUESTIONS = 10;

        if ($action === 'next') {
            if ($question < $TOTAL_QUESTIONS) {
                $currentQuestion = $question + 1;
            } else {
                $currentQuestion = $TOTAL_QUESTIONS;
            }
        } elseif ($action === 'finish') {
            return redirect()->route('design.results');
        } else {
            if ($question == 1) {
                $isCorrect = true;
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
            } elseif ($question == 4) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 4: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a blue balloon', 'remove the cat', 'make the grass green'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 5;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 4;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 4;
                }
            } elseif ($question == 5) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 5: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a rainbow', 'remove the clouds', 'make the sun bigger'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 6;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 5;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 5;
                }
            } elseif ($question == 6) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 6: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a yellow bird', 'remove the fence', 'make the tree taller'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 7;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 6;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 6;
                }
            } elseif ($question == 7) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 7: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a green car', 'remove the house', 'make the road wider'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 8;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 7;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 7;
                }
            } elseif ($question == 8) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 8: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a purple flower', 'remove the river', 'make the mountain higher'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        // Stay on 8 or redirect to results
                        $currentQuestion = 8;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 8;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 8;
                }
            } elseif ($question == 9) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 9: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a brown dog', 'remove the bridge', 'make the sky cloudy'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 10;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 9;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 9;
                }
            } elseif ($question == 10) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);
                Log::info('Submitted prompt for Question 10: ' . $prompt);
                try {
                    $promptLower = strtolower($prompt);
                    $requiredTransformations = ['add a pink balloon', 'remove the boat', 'make the grass yellow'];
                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, $transformation) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }
                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 10;
                    } else {
                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 10;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 10;
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
