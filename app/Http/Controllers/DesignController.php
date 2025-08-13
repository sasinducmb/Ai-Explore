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
        $marks = 0; // Add marks calculation

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
                $marks = 6;
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
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt is well-suited for the secret story image! Found keywords: ' . implode(', ', $uniqueKeywords);
                        $currentQuestion = 3; // Move to Question 3
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
                        $marks = 6;
                        $resultMessage = 'Correct! Your prompt accurately describes the changes needed! Found transformations: ' . implode(', ', $foundTransformations);
                        $currentQuestion = 4;
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
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

                    // Updated required transformations for the camping image
                    $requiredTransformations = [
                        'add a red hat to the boy roasting marshmallows',
                        'change the campfire to a barbecue grill',
                        'replace the tent with a small cabin',
                        'make the scene during daytime with a sunny sky',
                        'remove one of the trees in the background',
                        'add a dog sitting near the fire',
                        'change the girl’s sweater to yellow',
                        'add marshmallows to the woman’s stick',
                        'replace the logs with camping chairs',
                        'add a lantern hanging near the tent'
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
                        $currentQuestion = 5;
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
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

                    // Updated required transformations for the board game image
                    $requiredTransformations = [
                        'add sunglasses to the boy',
                        'change the teddy bear to a robot',
                        'replace the yellow car with a bicycle',
                        'add a hat to the woman',
                        'change the board game to a chessboard',
                        'remove the mug from the table',
                        'add a cat sitting on the table',
                        'change the red truck to a green bus',
                        'add balloons in the background',
                        'replace the bookshelf with a window view'
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
                        $currentQuestion = 6;
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
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

                    // Updated required transformations for the man helping elderly woman image
                    $requiredTransformations = [
                        'add a walking stick to the man',
                        'change the bag color to red',
                        'replace the stairs with a ramp',
                        'add a cat walking beside them',
                        'change the woman’s dress to blue',
                        'add flowers along the side of the stairs',
                        'replace the man’s shoes with sandals',
                        'add a backpack to the man',
                        'make the background a park scene',
                        'add a sun in the sky'
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
                        $currentQuestion = 7;
                    } else {
                        // Calculate partial marks
                        $partialScore = 0;
                        if (count($foundTransformations) >= 1) $partialScore += 3;
                        if (strlen(trim($prompt)) > 20) $partialScore += 2;
                        $marks = min($partialScore, 5);

                        $missing = array_diff($requiredTransformations, $foundTransformations);
                        $resultMessage = 'Your prompt needs improvement. Try to include more transformations like: ' . implode(', ', array_slice($missing, 0, 2)) . '. Found transformations: ' . (empty($foundTransformations) ? 'none' : implode(', ', $foundTransformations));
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

                    // Updated required transformations for the family at the beach image
                    $requiredTransformations = [
                        'add a beach ball near the family',
                        'change the father’s shirt to red',
                        'replace the sea with a swimming pool',
                        'add sunglasses to the mother',
                        'change the child’s shorts to blue',
                        'add a sandcastle in the background',
                        'replace the clouds with a clear sky',
                        'add a surfboard leaning on the sand',
                        'make the waves bigger',
                        'add a dolphin jumping in the background'
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
                        $currentQuestion = 8;
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
        session()->forget('current_question');
        return view('design-results');
    }
}
