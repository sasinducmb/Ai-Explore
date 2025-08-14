<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $marks = 0; // Add marks calculation

        $TOTAL_QUESTIONS = 10;

        if ($action === 'next') {
            if ($question < $TOTAL_QUESTIONS) {
                $currentQuestion = $question + 1;
            } else {
                $currentQuestion = $TOTAL_QUESTIONS;
            }
        } elseif ($action === 'finish') {
            // Calculate completion time and final results
            $gameStartTime = session('game_start_time', now());
            $completionTime = now()->diffInSeconds($gameStartTime);

            // Calculate total marks from session or default values
            $totalMarks = session('total_design_marks', 0);
            $totalPossible = 60; // 6 marks per question × 10 questions

            // Save to database
            $this->saveToDatabase($totalMarks, $totalPossible, $completionTime);

            session([
                'final_marks' => $totalMarks,
                'total_possible' => $totalPossible,
                'completion_time' => $completionTime,
            ]);

            session()->flash('success', 'Design challenge completed successfully!');
            return redirect()->route('design.results');
        } else {
            if ($question == 1) {
                $isCorrect = true;
                $marks = 6;
                $resultMessage = 'Great job on your drawing! Move to the next question.';
                $currentQuestion = 2;

                // Store marks in session
                $this->updateSessionMarks($marks);

                // Set start time if not exists
                if (!session()->has('game_start_time')) {
                    session(['game_start_time' => now()]);
                }
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
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the secret story image! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 3; // Move to Question 3
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($uniqueKeywords) >= 1) $partialScore += 2;
                        if ($hasQuestionPattern) $partialScore += 2;
                        if (strlen(trim($prompt)) > 10) $partialScore += 1;
                        $marks = min($partialScore, 5);

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
                    $marks = 0;
                    $currentQuestion = 2;
                }
            } elseif ($question == 3) {
                $request->validate([
                    'prompt' => 'required|string|max:500',
                ], [
                    'prompt.required' => 'A prompt is required.',
                ]);

                Log::info('Submitted prompt for Question 3: ' . $prompt);

                try {
                    $promptLower = strtolower($prompt);
                    $requiredKeywords = ['camping', 'family', 'fire', 'tent', 'outdoor', 'nature', 'campfire', 'roasting', 'marshmallow', 'adventure', 'forest', 'night', 'evening', 'woods', 'vacation'];
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
                        strpos($promptLower, 'image') !== false ||
                        strpos($promptLower, 'make') !== false ||
                        strpos($promptLower, 'show') !== false ||
                        strpos($promptLower, 'design') !== false
                    );

                    $uniqueKeywords = array_unique($foundKeywords);
                    $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the camping scene! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 4;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($uniqueKeywords) >= 1) $partialScore += 2;
                        if ($hasQuestionPattern) $partialScore += 2;
                        if (strlen(trim($prompt)) > 10) $partialScore += 1;
                        $marks = min($partialScore, 5);

                        $suggestions = [];
                        if (count($uniqueKeywords) < 2) {
                            $missing = array_diff($requiredKeywords, $foundKeywords);
                            $suggestions[] = 'include more keywords like ' . implode(', ', array_slice($missing, 0, 3));
                        }
                        if (!$hasQuestionPattern) {
                            $suggestions[] = 'use action words like create, generate, draw, make, or image';
                        }
                        $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                        $currentQuestion = 3;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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
                    $requiredKeywords = ['family', 'game', 'table', 'living', 'board', 'play', 'together', 'fun', 'activity', 'entertainment', 'room', 'indoor', 'children', 'parents', 'home', 'leisure', 'bonding'];
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
                        strpos($promptLower, 'image') !== false ||
                        strpos($promptLower, 'make') !== false ||
                        strpos($promptLower, 'show') !== false ||
                        strpos($promptLower, 'design') !== false
                    );

                    $uniqueKeywords = array_unique($foundKeywords);
                    $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the family game scene! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 5;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($uniqueKeywords) >= 1) $partialScore += 2;
                        if ($hasQuestionPattern) $partialScore += 2;
                        if (strlen(trim($prompt)) > 10) $partialScore += 1;
                        $marks = min($partialScore, 5);

                        $suggestions = [];
                        if (count($uniqueKeywords) < 2) {
                            $missing = array_diff($requiredKeywords, $foundKeywords);
                            $suggestions[] = 'include more keywords like ' . implode(', ', array_slice($missing, 0, 3));
                        }
                        if (!$hasQuestionPattern) {
                            $suggestions[] = 'use action words like create, generate, draw, make, or image';
                        }
                        $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                        $currentQuestion = 4;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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
                    $requiredKeywords = ['helping', 'elderly', 'stairs', 'kindness', 'assistance', 'support', 'care', 'old', 'senior', 'aid', 'compassion', 'respect', 'gentle', 'walking', 'climbing', 'steps', 'guidance', 'service'];
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
                        strpos($promptLower, 'image') !== false ||
                        strpos($promptLower, 'make') !== false ||
                        strpos($promptLower, 'show') !== false ||
                        strpos($promptLower, 'design') !== false
                    );

                    $uniqueKeywords = array_unique($foundKeywords);
                    $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the helping scene! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 6;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($uniqueKeywords) >= 1) $partialScore += 2;
                        if ($hasQuestionPattern) $partialScore += 2;
                        if (strlen(trim($prompt)) > 10) $partialScore += 1;
                        $marks = min($partialScore, 5);

                        $suggestions = [];
                        if (count($uniqueKeywords) < 2) {
                            $missing = array_diff($requiredKeywords, $foundKeywords);
                            $suggestions[] = 'include more keywords like ' . implode(', ', array_slice($missing, 0, 3));
                        }
                        if (!$hasQuestionPattern) {
                            $suggestions[] = 'use action words like create, generate, draw, make, or image';
                        }
                        $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                        $currentQuestion = 5;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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
                    $requiredKeywords = ['helping', 'elderly', 'stairs', 'kindness', 'assistance', 'support', 'care', 'old', 'senior', 'aid', 'compassion', 'respect', 'gentle', 'walking', 'climbing', 'steps', 'guidance', 'service'];
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
                        strpos($promptLower, 'image') !== false ||
                        strpos($promptLower, 'make') !== false ||
                        strpos($promptLower, 'show') !== false ||
                        strpos($promptLower, 'design') !== false
                    );

                    $uniqueKeywords = array_unique($foundKeywords);
                    $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the beach family scene! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 7;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($uniqueKeywords) >= 1) $partialScore += 2;
                        if ($hasQuestionPattern) $partialScore += 2;
                        if (strlen(trim($prompt)) > 10) $partialScore += 1;
                        $marks = min($partialScore, 5);

                        $suggestions = [];
                        if (count($uniqueKeywords) < 2) {
                            $missing = array_diff($requiredKeywords, $foundKeywords);
                            $suggestions[] = 'include more keywords like ' . implode(', ', array_slice($missing, 0, 3));
                        }
                        if (!$hasQuestionPattern) {
                            $suggestions[] = 'use action words like create, generate, draw, make, or image';
                        }
                        $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                        $currentQuestion = 6;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 8;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 7;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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

                    // Updated required transformations for the kite-flying beach scene
                    $requiredTransformations = [
                        'add a starfish on the sand',
                        'change the kite color to green and purple',
                        'replace the seashell with a crab',
                        'remove the boat from the background',
                        'add sunglasses to the girl',
                        'change the dog’s collar to red',
                        'replace the tree with a palm tree with more coconuts',
                        'add an extra cloud in the sky',
                        'change the water color to a darker blue',
                        'add a beach ball next to the dog'
                    ];

                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, strtolower($transformation)) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }

                    $isCorrect = count($foundTransformations) >= 2;

                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        // Stay on 8 or redirect to results
                        $currentQuestion = 8;
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 8;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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

                    // Updated required transformations for the messy-bedroom scene
                    $requiredTransformations = [
                        'add a buzzing fly near the boy',
                        'straighten the wall picture frame',
                        'close the wardrobe doors',
                        'remove the yellow sock from the bedframe',
                        'replace the badminton racket with a football',
                        'remove the backpack next to the bed',
                        'change the boy’s shirt to blue',
                        'add a nightstand beside the bed',
                        'replace the red green ball with a basketball',
                        'add a window on the wall'
                    ];

                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, strtolower($transformation)) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }

                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 10;
                        $this->updateSessionMarks($marks);
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 9;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
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

                    // Updated required transformations for the pirate and parrot scene
                    $requiredTransformations = [
                        'change the parrot’s head color to yellow',
                        'remove the cloud on the right side',
                        'add stripes to the pirate’s pants',
                        'change the treasure chest size to smaller',
                        'remove the boat’s skull flag',
                        'add a seashell on the sand',
                        'change the pirate’s headscarf color to blue',
                        'replace the map with a compass',
                        'add a palm tree in the background',
                        'make the parrot wear a pirate hat'
                    ];

                    $foundTransformations = [];
                    foreach ($requiredTransformations as $transformation) {
                        if (strpos($promptLower, strtolower($transformation)) !== false) {
                            $foundTransformations[] = $transformation;
                        }
                    }

                    $isCorrect = count($foundTransformations) >= 2;
                    if ($isCorrect) {
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 10; // stays at 10 or moves depending on your flow
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
                        $currentQuestion = 10;
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $marks = 0;
                    $currentQuestion = 10;
                }
            }
        }

        // Store current question in session
        session()->put('current_question', $currentQuestion);

        // Check if this is an AJAX request and return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'isCorrect' => $isCorrect,
                'marks' => $marks,
                'message' => $resultMessage,
                'question' => $question,
                'currentQuestion' => $currentQuestion
            ]);
        }

        return view('design.design-tools', [
            'showPopup' => $action === 'submit',
            'isCorrect' => $isCorrect,
            'resultMessage' => $resultMessage,
            'currentQuestion' => $currentQuestion,
            'prompt' => $prompt,
            'marks' => $marks,
        ]);
    }

    public function results()
    {
        // Clear session question data but keep final results
        session()->forget(['current_question', 'game_start_time']);

        return view('design.design-results');
    }

    /**
     * Update session marks total
     */
    private function updateSessionMarks($newMarks)
    {
        $currentTotal = session('total_design_marks', 0);
        session(['total_design_marks' => $currentTotal + $newMarks]);
    }

    /**
     * Save results to database
     */
    private function saveToDatabase($totalMarks, $totalPossible, $completionTime)
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('design_answers')) {
                $percentage = ($totalMarks / $totalPossible) * 100;
                $grade = $this->calculateGrade($percentage);

                DB::table('design_answers')->insert([
                    'name' => Auth::check() ? Auth::user()->name : null,
                    'session_id' => session()->getId(),
                    'total_marks' => $totalMarks,
                    'total_possible_marks' => $totalPossible,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'completion_time_seconds' => $completionTime,
                    'completed' => true,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error saving design results to database: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}
