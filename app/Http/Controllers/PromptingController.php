<?php

namespace App\Http\Controllers;

use Google\Cloud\Language\V2\Client\LanguageServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Language\V1\Document;
use Google\Cloud\Language\V1\Document\Type;
use App\Models\PromptingResult;
use Carbon\Carbon;

class PromptingController extends Controller
{
    protected $languageClient;

    public function __construct()
    {
        // Initialize Google Cloud NLP client
        try {
            $this->languageClient = new LanguageServiceClient();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Google Cloud NLP client: ' . $e->getMessage());
            $this->languageClient = null; // Fallback to keyword-based logic if initialization fails
        }
    }

    public function show()
    {
        // Initialize session and database record if not exists
        $sessionId = session()->getId();
        $result = $this->getOrCreateResult($sessionId);

        // Store start time in session if not exists
        if (!session()->has('game_start_time')) {
            session(['game_start_time' => now()]);
        }

        return view('prompting.prompting', [
            'currentQuestion' => 1,
            'result' => $result
        ]);
    }

    /**
     * Get or create a prompting result record
     */
    private function getOrCreateResult($sessionId)
    {
        $result = PromptingResult::bySession($sessionId)->first();

        if (!$result) {
            $result = PromptingResult::create([
                'session_id' => $sessionId,
                'name' => Auth::check() ? Auth::user()->name : null, // Save logged-in user's name
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'total_possible_marks' => 60, // For 3 questions
            ]);
        }

        return $result;
    }

    public function submit(Request $request)
    {
        $question = $request->input('question');
        $action = $request->input('action', 'submit');
        $isCorrect = false;
        $resultMessage = null;
        $imoji = null;
        $currentQuestion = $question;
        $selectedTopic = $request->input('topic', null);
        $sessionId = session()->getId();

        // Initialize or retrieve result
        $result = $this->getOrCreateResult($sessionId);

        if ($action === 'next') {
            // Move to the next question without validation
            if ($question == 1) {
                $currentQuestion = 2;
            } elseif ($question == 2) {
                $currentQuestion = 3;
            } elseif ($question == 3) {
                $currentQuestion = 3; // Stay on Question 3
            }
        } elseif ($action === 'finish') {
            // Handle finish action with final marks calculation
            if ($question == 3) {
                $request->validate([
                    'topic' => 'required|string|in:animals,ocean,robot,computers',
                    'answer' => 'required|string|max:5000',
                ]);

                // Process Question 3 to get the score
                $analysisResult = $this->processQuestion3($request);
                $isCorrect = $analysisResult['isCorrect'];
                $marks = $this->calculateMarks(3, $isCorrect, $request->answer, $request->topic);

                // Save final question result
                $result = $this->saveQuestionResult(
                    $sessionId,
                    3,
                    $request->answer,
                    $isCorrect,
                    $marks,
                    $analysisResult['analysis'],
                    $request->topic
                );

                // Mark as completed and calculate completion time
                $gameStartTime = session('game_start_time', now());
                $completionTime = now()->diffInSeconds($gameStartTime);

                $result->update([
                    'completed' => true,
                    'completion_time_seconds' => $completionTime,
                ]);

                // Store final data in session
                session([
                    'final_marks' => $result->total_marks,
                    'total_possible' => $result->total_possible_marks,
                    'percentage' => $result->percentage,
                    'grade' => $result->grade,
                    'completion_time' => $completionTime,
                ]);

                // Comprehensive logging
                Log::info('=== FINAL MARKS CALCULATION ===');
                Log::info('Session ID: ' . $sessionId);
                Log::info('User Name: ' . ($result->name ?? 'Guest')); // Log user name
                Log::info('Question 1 Marks: ' . ($result->question_1_marks ?? 0) . '/10');
                Log::info('Question 2 Marks: ' . ($result->question_2_marks ?? 0) . '/20');
                Log::info('Question 3 Marks: ' . ($result->question_3_marks ?? 0) . '/30');
                Log::info('Total Marks: ' . $result->total_marks . '/60');
                Log::info('Percentage: ' . $result->percentage . '%');
                Log::info('Grade: ' . $result->grade);
                Log::info('Completion Time: ' . $result->formatted_completion_time);
                Log::info('================================');

                return redirect()->route('prompting.results');
            }
        } else {
            // Handle submit action with validation and always save
            if ($question == 1) {
                $request->validate([
                    'answer' => 'required|string|in:Grok,Bard,Copilot,ChatGPT,Claude',
                ]);
                $isCorrect = $request->answer === 'Bard';
                $resultMessage = $isCorrect ? 'Correct! Great job!' : 'Answer saved. Moving to the next question.';
                $imoji = $isCorrect ? 'happy' : 'moderate';
                $marks = $this->calculateMarks(1, $isCorrect);

                // Save Question 1 result
                $this->saveQuestionResult($sessionId, 1, $request->answer, $isCorrect, $marks);
                Log::info('Question 1 Marks: ' . $marks . '/10');
                $currentQuestion = 2; // Always move to Question 2

            } elseif ($question == 2) {
                $request->validate([
                    'answer' => 'required|string|max:5000',
                ]);

                Log::info('Submitted prompt for Question 2: ' . $request->answer);

                $analysisResult = $this->processQuestion2($request);
                $isCorrect = $analysisResult['isCorrect'];
                $marks = $this->calculateMarks(2, $isCorrect, $request->answer);

                // Save Question 2 result
                $this->saveQuestionResult($sessionId, 2, $request->answer, $isCorrect, $marks, $analysisResult['analysis']);

                $resultMessage = $isCorrect ? 'Correct! Your prompt is relevant and well-structured!' : 'Answer saved. Moving to the next question.';
                $imoji = $isCorrect ? 'happy' : 'moderate';
                $currentQuestion = 3; // Always move to Question 3

                Log::info('Question 2 Marks: ' . $marks . '/20');

            } elseif ($question == 3) {
                $request->validate([
                    'topic' => 'required|string|in:animals,ocean,robot,computers',
                    'answer' => 'required|string|max:5000',
                ]);

                Log::info('Submitted prompt for Question 3: ' . $request->answer . ' | Topic: ' . $request->topic);

                $analysisResult = $this->processQuestion3($request);
                $isCorrect = $analysisResult['isCorrect'];
                $marks = $this->calculateMarks(3, $isCorrect, $request->answer, $request->topic);

                // Save Question 3 result
                $this->saveQuestionResult($sessionId, 3, $request->answer, $isCorrect, $marks, $analysisResult['analysis'], $request->topic);

                $resultMessage = $isCorrect ? "Correct! Your improved prompt is clear, detailed, and relevant to '{$request->topic}'!" : 'Answer saved. You can improve your prompt or finish.';
                $imoji = $isCorrect ? 'happy' : 'moderate';
                $currentQuestion = 3; // Stay on Question 3

                Log::info('Question 3 Marks: ' . $marks . '/30');
            }
        }

        return view('prompting.prompting', [
            'showPopup' => $action === 'submit',
            'isCorrect' => $isCorrect,
            'imoji' => $imoji,
            'resultMessage' => $resultMessage,
            'currentQuestion' => $currentQuestion,
            'selectedTopic' => $selectedTopic,
            'result' => $result,
        ]);
    }
    /**
     * Calculate marks for each question
     */
    private function calculateMarks($questionNumber, $isCorrect, $answer = null, $topic = null)
    {
        $marks = 0;

        switch ($questionNumber) {
            case 1:
                // Question 1: Simple MCQ - 10 marks for correct answer
                $marks = $isCorrect ? 10 : 0;
                break;

            case 2:
                // Question 2: Reverse prompt builder - up to 20 marks
                if ($isCorrect) {
                    $marks = 20;
                } else {
                    // Partial marks based on answer quality
                    $textLower = strtolower($answer);
                    $partialScore = 0;

                    // Check for question words (5 marks)
                    if (strpos($textLower, 'what') !== false ||
                        strpos($textLower, 'explain') !== false ||
                        strpos($textLower, 'tell') !== false ||
                        strpos($textLower, 'describe') !== false ||
                        strpos($textLower, '?') !== false) {
                        $partialScore += 5;
                    }

                    // Check for relevant keywords (up to 10 marks)
                    $keywords = ['electric', 'vehicle', 'car', 'ev', 'advantages', 'disadvantages', 'benefits', 'pollution', 'environment', 'charging'];
                    $foundKeywords = 0;
                    foreach ($keywords as $keyword) {
                        if (strpos($textLower, $keyword) !== false) {
                            $foundKeywords++;
                        }
                    }
                    $partialScore += min($foundKeywords * 2, 10);

                    // Minimum effort bonus (3 marks for trying)
                    if (strlen(trim($answer)) > 20) {
                        $partialScore += 3;
                    }

                    $marks = min($partialScore, 18); // Max 18 for partial, 20 for full correct
                }
                break;

            case 3:
                // Question 3: Super question builder - up to 30 marks
                if ($isCorrect) {
                    $marks = 30;
                } else {
                    // Detailed partial scoring
                    $textLower = strtolower($answer);
                    $partialScore = 0;

                    // Topic inclusion (5 marks)
                    if (strpos($textLower, strtolower($topic)) !== false) {
                        $partialScore += 5;
                    }

                    // Question format (5 marks)
                    if (strpos($textLower, 'what') !== false ||
                        strpos($textLower, 'how') !== false ||
                        strpos($textLower, 'why') !== false ||
                        strpos($textLower, 'explain') !== false ||
                        strpos($textLower, 'describe') !== false ||
                        strpos($textLower, '?') !== false) {
                        $partialScore += 5;
                    }

                    // Clarity keywords (up to 8 marks)
                    $clarityKeywords = ['explain', 'describe', 'what', 'how', 'why', 'details', 'example', 'specific'];
                    $clarityCount = 0;
                    foreach ($clarityKeywords as $keyword) {
                        if (strpos($textLower, $keyword) !== false) {
                            $clarityCount++;
                        }
                    }
                    $partialScore += min($clarityCount * 2, 8);

                    // Topic-specific keywords (up to 8 marks)
                    $topicKeywords = [
                        'animals' => ['wildlife', 'species', 'habitat', 'behavior', 'migration', 'adaptation'],
                        'ocean' => ['marine', 'sea', 'coral', 'ecosystem', 'fish', 'waves'],
                        'robot' => ['robotics', 'automation', 'programming', 'machine', 'artificial', 'intelligence'],
                        'computers' => ['hardware', 'software', 'programming', 'processor', 'network', 'data']
                    ];

                    if (isset($topicKeywords[$topic])) {
                        $topicScore = 0;
                        foreach ($topicKeywords[$topic] as $keyword) {
                            if (strpos($textLower, $keyword) !== false) {
                                $topicScore++;
                            }
                        }
                        $partialScore += min($topicScore * 2, 8);
                    }

                    // Length and effort bonus (4 marks)
                    if (strlen(trim($answer)) > 50) {
                        $partialScore += 2;
                    }
                    if (strlen(trim($answer)) > 100) {
                        $partialScore += 2;
                    }

                    $marks = min($partialScore, 28); // Max 28 for partial, 30 for full correct
                }
                break;
        }

        return $marks;
    }

    /**
     * Save question result to database
     */
    private function saveQuestionResult($sessionId, $questionNumber, $answer, $isCorrect, $marks, $analysis = null, $topic = null)
    {
        $result = $this->getOrCreateResult($sessionId);

        $data = [
            "question_{$questionNumber}_answer" => $answer,
            "question_{$questionNumber}_correct" => $isCorrect,
            "question_{$questionNumber}_marks" => $marks,
            "question_{$questionNumber}_completed_at" => now(),
        ];

        if ($analysis) {
            $data["question_{$questionNumber}_analysis"] = $analysis;
        }

        if ($topic) {
            $data["question_{$questionNumber}_topic"] = $topic;
        }

        $result->update($data);
        $result->updateTotals();

        return $result;
    }


    private function processQuestion2($request)
    {
        try {
            if ($this->languageClient) {
                $document = new Document();
                $document->setContent($request->answer)->setType(Type::PLAIN_TEXT);

                // Entity analysis
                $entityResponse = $this->languageClient->analyzeEntities($document);
                $entities = $entityResponse->getEntities();

                $relevantEntities = ['electric vehicle', 'pollution', 'environment', 'charging', 'car', 'ev'];
                $foundEntities = [];
                foreach ($entities as $entity) {
                    if (in_array(strtolower($entity->getName()), $relevantEntities)) {
                        $foundEntities[] = $entity->getName();
                    }
                }

                // Sentiment analysis
                $sentimentResponse = $this->languageClient->analyzeSentiment($document);
                $sentiment = $sentimentResponse->getDocumentSentiment()->getScore();

                // Syntax analysis for question pattern
                $syntaxResponse = $this->languageClient->analyzeSyntax($document);
                $tokens = $syntaxResponse->getTokens();
                $hasQuestionPattern = false;
                foreach ($tokens as $token) {
                    $text = strtolower($token->getText()->getContent());
                    if (in_array($text, ['what', 'how', 'why', 'explain', 'describe']) || $text === '?') {
                        $hasQuestionPattern = true;
                        break;
                    }
                }

                $isCorrect = count(array_unique($foundEntities)) >= 2 && $hasQuestionPattern && $sentiment >= -0.1;

                $analysis = [
                    'method' => 'google_nlp',
                    'found_entities' => array_unique($foundEntities),
                    'sentiment_score' => $sentiment,
                    'has_question_pattern' => $hasQuestionPattern,
                    'entity_count' => count(array_unique($foundEntities)),
                ];

                if ($isCorrect) {
                    $message = 'Correct! Your prompt is relevant and well-structured! Found entities: ' . implode(', ', array_unique($foundEntities));
                } else {
                    $suggestions = [];
                    if (count(array_unique($foundEntities)) < 2) {
                        $suggestions[] = 'include more relevant terms like electric vehicle, pollution, or charging';
                    }
                    if (!$hasQuestionPattern) {
                        $suggestions[] = 'phrase it as a question or request (e.g., use "what", "how", or "explain")';
                    }
                    if ($sentiment < -0.1) {
                        $suggestions[] = 'use a neutral or positive tone';
                    }
                    $message = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found entities: ' . (empty($foundEntities) ? 'none' : implode(', ', array_unique($foundEntities)));
                }
            } else {
                // Fallback to keyword-based logic
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

                $hasQuestionPattern = false;
                if (strpos($textLower, 'what') !== false ||
                    strpos($textLower, 'explain') !== false ||
                    strpos($textLower, 'tell') !== false ||
                    strpos($textLower, 'describe') !== false ||
                    strpos($textLower, '?') !== false) {
                    $hasQuestionPattern = true;
                }

                $uniqueKeywords = array_unique($foundKeywords);
                $isCorrect = count($uniqueKeywords) >= 2 && $hasQuestionPattern;

                $analysis = [
                    'method' => 'keyword_based',
                    'found_keywords' => $uniqueKeywords,
                    'has_question_pattern' => $hasQuestionPattern,
                    'keyword_count' => count($uniqueKeywords),
                ];

                if ($isCorrect) {
                    $message = 'Correct! Your prompt is good! Found relevant keywords: ' . implode(', ', $uniqueKeywords);
                } else {
                    $suggestions = [];
                    if (count($uniqueKeywords) < 2) {
                        $suggestions[] = 'include more relevant keywords like advantages, disadvantages, pollution, or charging';
                    }
                    if (!$hasQuestionPattern) {
                        $suggestions[] = 'make it more like a question or request';
                    }
                    $message = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                }
            }

            return [
                'isCorrect' => $isCorrect,
                'message' => $message,
                'analysis' => $analysis,
            ];

        } catch (\Exception $e) {
            Log::error('Unexpected error in Question 2: ' . $e->getMessage());
            return [
                'isCorrect' => false,
                'message' => 'An error occurred while analyzing your prompt. Please try again.',
                'analysis' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Process Question 3 with detailed analysis
     */
    private function processQuestion3($request)
    {
        try {
            if ($this->languageClient) {
                $document = new Document();
                $document->setContent($request->answer)->setType(Type::PLAIN_TEXT);

                // Entity analysis
                $entityResponse = $this->languageClient->analyzeEntities($document);
                $entities = $entityResponse->getEntities();

                $topicKeywords = [
                    'animals' => ['wildlife', 'species', 'habitat', 'behavior', 'migration', 'adaptation'],
                    'ocean' => ['marine', 'sea', 'coral', 'ecosystem', 'fish', 'waves'],
                    'robot' => ['robotics', 'automation', 'programming', 'machine', 'artificial', 'intelligence'],
                    'computers' => ['hardware', 'software', 'programming', 'processor', 'network', 'data']
                ];

                $selectedTopic = $request->input('topic');
                $relevantEntities = $topicKeywords[$selectedTopic];
                $foundEntities = [];
                foreach ($entities as $entity) {
                    if (in_array(strtolower($entity->getName()), $relevantEntities) || strtolower($entity->getName()) === $selectedTopic) {
                        $foundEntities[] = $entity->getName();
                    }
                }

                // Syntax analysis
                $syntaxResponse = $this->languageClient->analyzeSyntax($document);
                $tokens = $syntaxResponse->getTokens();

                $clarityCount = 0;
                $specificityCount = 0;
                $hasQuestionPattern = false;
                $clarityKeywords = ['explain', 'describe', 'what', 'how', 'why', 'details', 'example', 'specific'];
                $specificityIndicators = ['specific', 'example', 'details', 'type', 'kind', 'particular'];

                foreach ($tokens as $token) {
                    $text = strtolower($token->getText()->getContent());
                    if (in_array($text, $clarityKeywords)) {
                        $clarityCount++;
                    }
                    if (in_array($text, $specificityIndicators)) {
                        $specificityCount++;
                    }
                    if (in_array($text, ['what', 'how', 'why', 'explain', 'describe']) || $text === '?') {
                        $hasQuestionPattern = true;
                    }
                }

                $hasTopic = in_array(strtolower($selectedTopic), array_map('strtolower', $foundEntities));
                $hasEnoughTopicKeywords = count(array_unique($foundEntities)) >= 2;

                $isCorrect = $hasTopic && $hasEnoughTopicKeywords && $clarityCount >= 2 && $specificityCount >= 1 && $hasQuestionPattern;

                $analysis = [
                    'method' => 'google_nlp',
                    'selected_topic' => $selectedTopic,
                    'found_entities' => array_unique($foundEntities),
                    'has_topic' => $hasTopic,
                    'has_enough_topic_keywords' => $hasEnoughTopicKeywords,
                    'clarity_count' => $clarityCount,
                    'specificity_count' => $specificityCount,
                    'has_question_pattern' => $hasQuestionPattern,
                ];

                if ($isCorrect) {
                    $message = "Correct! Your improved prompt is clear, detailed, and relevant to '$selectedTopic'! Found entities: " . implode(', ', array_unique($foundEntities));
                } else {
                    $suggestions = [];
                    if (!$hasTopic) {
                        $suggestions[] = "include the selected topic '$selectedTopic' in your prompt";
                    }
                    if (!$hasEnoughTopicKeywords) {
                        $suggestions[] = "include more relevant terms like " . implode(', ', array_slice($relevantEntities, 0, 3));
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
                    $message = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found entities: ' . (empty($foundEntities) ? 'none' : implode(', ', array_unique($foundEntities)));
                }
            } else {
                // Fallback to original keyword-based logic
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
                $hasQuestionPattern = false;
                if (strpos($textLower, 'what') !== false ||
                    strpos($textLower, 'how') !== false ||
                    strpos($textLower, 'why') !== false ||
                    strpos($textLower, 'explain') !== false ||
                    strpos($textLower, 'describe') !== false ||
                    strpos($textLower, '?') !== false) {
                    $hasQuestionPattern = true;
                }

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

                $isCorrect = $hasTopic && $hasEnoughTopicKeywords && $clarityCount >= 2 && $specificityCount >= 1 && $hasQuestionPattern;

                $analysis = [
                    'method' => 'keyword_based',
                    'selected_topic' => $selectedTopic,
                    'found_topic_keywords' => $foundTopicKeywords,
                    'has_topic' => $hasTopic,
                    'has_enough_topic_keywords' => $hasEnoughTopicKeywords,
                    'clarity_count' => $clarityCount,
                    'specificity_count' => $specificityCount,
                    'has_question_pattern' => $hasQuestionPattern,
                ];

                if ($isCorrect) {
                    $message = "Correct! Your improved prompt is clear, detailed, and relevant to '$selectedTopic'! Found topic keywords: " . implode(', ', $foundTopicKeywords);
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
                    $message = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found topic keywords: ' . (empty($foundTopicKeywords) ? 'none' : implode(', ', $foundTopicKeywords));
                }
            }

            return [
                'isCorrect' => $isCorrect,
                'message' => $message,
                'analysis' => $analysis,
            ];

        } catch (\Exception $e) {
            Log::error('Unexpected error in Question 3: ' . $e->getMessage());
            return [
                'isCorrect' => false,
                'message' => 'An error occurred while analyzing your prompt. Please try again.',
                'analysis' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function results()
    {
        $sessionId = session()->getId();
        $result = PromptingResult::bySession($sessionId)->first();

        if (!$result) {
            return redirect()->route('prompting.show')->with('error', 'No results found. Please complete the prompting tool first.');
        }

        return view('prompting.results', [
            'result' => $result,
            'statistics' => PromptingResult::getStatistics(),
        ]);
    }

    public function showResult($sessionId)
    {
        $result = PromptingResult::where('session_id', $sessionId)->firstOrFail();
        return view('admin.results.show', compact('result'));
    }

    public function __destruct()
    {
        // Close the Google Cloud NLP client connection
        if ($this->languageClient) {
            try {
                $this->languageClient->close();
            } catch (\Exception $e) {
                Log::error('Failed to close Google Cloud NLP client: ' . $e->getMessage());
            }
        }
    }
}
