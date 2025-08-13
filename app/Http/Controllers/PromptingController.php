<?php

namespace App\Http\Controllers;

use Google\Cloud\Language\V2\Client\LanguageServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Language\V1\Document;
use Google\Cloud\Language\V1\Document\Type;
use App\Models\PromptingAnswer;
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
        $result = PromptingAnswer::where('session_id', $sessionId)->first();

        if (!$result) {
            $result = PromptingAnswer::create([
                'session_id' => $sessionId,
                'name' => Auth::check() ? Auth::user()->name : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'total_possible_marks' => 30, // Updated for 5 questions
            ]);
        }

        return $result;
    }

    public function submit(Request $request)
    {
        $sessionId = session()->getId();
        $action = $request->input('action', 'submit');
        $question = $request->input('question', 1);

        // Handle the finish action from the form
        if ($action === 'finish') {
            Log::info('Received finish action with data', $request->all());

            // Calculate completion time
            $gameStartTime = session('game_start_time', now());
            $completionTime = now()->diffInSeconds($gameStartTime);

            $result = $this->getOrCreateResult($sessionId);

            // Calculate totals from already saved individual questions
            $totalMarks = 0;
            for ($i = 1; $i <= 5; $i++) {
                $totalMarks += $result->{"question_{$i}_marks"} ?? 0;
            }

            $percentage = ($totalMarks / $result->total_possible_marks) * 100;
            $grade = $this->calculateGrade($percentage);

            // Update final results
            $result->update([
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'grade' => $grade,
                'completion_time_seconds' => $completionTime,
                'completed' => true,
            ]);

            Log::info('Successfully completed quiz for session: ' . $sessionId);

            session([
                'final_marks' => $totalMarks,
                'total_possible' => $result->total_possible_marks,
                'completion_time' => $completionTime,
            ]);

            session()->flash('success', 'Your answers have been submitted successfully!');

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('prompting.results')
                ]);
            }

            return redirect()->route('prompting.results');
        }

        // Handle individual question submissions
        $selectedTopic = $request->input('topic', null);
        $result = $this->getOrCreateResult($sessionId);
        $isCorrect = false;
        $marks = 0;
        $resultMessage = '';
        $analysis = null;

        if ($question == 1) {
            $request->validate([
                'answer' => 'required|string|in:Grok,Bard,Copilot,ChatGPT,Claude',
            ]);
            $isCorrect = $request->input('answer') === 'Bard';
            $marks = $this->calculateMarks(1, $isCorrect);

            if ($isCorrect) {
                $resultMessage = 'Excellent! Bard is indeed Google\'s AI prompting tool. You got it right!';
            } else {
                $resultMessage = 'Good try! The correct answer is Bard - Google\'s conversational AI assistant. You\'ll learn more as we continue!';
            }

            $this->saveQuestionResult($sessionId, 1, $request->input('answer'), $isCorrect, $marks);
            Log::info('Question 1 Marks: ' . $marks . '/6');

        } elseif ($question >= 2 && $question <= 5) {
            $request->validate([
                'answer' => 'required|string|max:5000',
            ]);

            Log::info("Submitted prompt for Question {$question}: " . $request->input('answer'));

            if ($question == 2) {
                // Weather prompt improvement
                $analysisResult = $this->processWeatherPrompt($request);
            } elseif ($question == 3) {
                // Recipe prompt improvement
                $analysisResult = $this->processRecipePrompt($request);
            } elseif ($question == 4) {
                // Computer prompt improvement
                $analysisResult = $this->processComputerPrompt($request);
            } else {
                // Role-play prompt
                $analysisResult = $this->processRolePlayPrompt($request);
            }

            $isCorrect = $analysisResult['isCorrect'];
            $marks = $this->calculateMarks($question, $isCorrect, $request->input('answer'));
            $analysis = $analysisResult['analysis'] ?? null;
            $resultMessage = $this->generateFeedbackMessage($marks, $analysisResult['message']);

            $this->saveQuestionResult($sessionId, $question, $request->input('answer'), $isCorrect, $marks, $analysis);
            Log::info("Question {$question} Marks: {$marks}/6");
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            Log::info('Returning AJAX response with marks: ' . $marks); // Debug log
            return response()->json([
                'success' => true,
                'isCorrect' => $isCorrect,
                'marks' => $marks,
                'message' => $resultMessage,
                'question' => $question
            ]);
        }

        // For non-AJAX requests, return the view (fallback)
        return view('prompting.prompting', [
            'showPopup' => false,
            'isCorrect' => $isCorrect,
            'resultMessage' => $resultMessage,
            'currentQuestion' => min($question + 1, 5),
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
                // Question 1: Simple MCQ - 6 marks for correct answer
                $marks = $isCorrect ? 6 : 0;
                break;

            case 2:
            case 3:
            case 4:
            case 5:
                // Questions 2-5: Each worth 6 marks
                if ($isCorrect) {
                    $marks = 6;
                } else {
                    // Partial marks based on answer quality
                    $textLower = strtolower($answer);
                    $partialScore = 0;

                    // Check for question words (2 marks)
                    if (strpos($textLower, 'what') !== false ||
                        strpos($textLower, 'explain') !== false ||
                        strpos($textLower, 'tell') !== false ||
                        strpos($textLower, 'describe') !== false ||
                        strpos($textLower, '?') !== false) {
                        $partialScore += 2;
                    }

                    // Check for relevant keywords (up to 3 marks)
                    $keywords = ['electric', 'vehicle', 'car', 'ev', 'advantages', 'disadvantages', 'benefits', 'pollution', 'environment', 'charging'];
                    $foundKeywords = 0;
                    foreach ($keywords as $keyword) {
                        if (strpos($textLower, $keyword) !== false) {
                            $foundKeywords++;
                        }
                    }
                    $partialScore += min($foundKeywords, 3);

                    // Minimum effort bonus (1 mark for trying)
                    if (strlen(trim($answer)) > 20) {
                        $partialScore += 1;
                    }

                    $marks = min($partialScore, 5); // Max 5 for partial, 6 for full correct
                }
                break;
        }

        return $marks;
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

        if ($analysis !== null) {
            $data["question_{$questionNumber}_analysis"] = $analysis;
        }

        $result->update($data);

        // Update totals
        $totalMarks = 0;
        for ($i = 1; $i <= 5; $i++) {
            $totalMarks += $result->{"question_{$i}_marks"} ?? 0;
        }

        $percentage = ($totalMarks / $result->total_possible_marks) * 100;
        $grade = $this->calculateGrade($percentage);

        $result->update([
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'grade' => $grade,
        ]);

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
        $result = PromptingAnswer::where('session_id', $sessionId)->first();

        if (!$result) {
            return redirect()->route('prompting.show')->with('error', 'No results found. Please complete the prompting tool first.');
        }

        return view('prompting.results', [
            'result' => $result,
            'statistics' => PromptingAnswer::getStatistics(),
        ]);
    }

    public function showResult($sessionId)
    {
        $result = PromptingAnswer::where('session_id', $sessionId)->firstOrFail();
        return view('admin.results.show', compact('result'));
    }

    /**
     * Restart the prompting quiz
     */
    public function restart(Request $request)
    {
        $sessionId = session()->getId();

        // Find existing result
        $result = PromptingAnswer::where('session_id', $sessionId)->first();

        if ($result) {
            // Reset all answers and progress
            $result->update([
                'question_1_answer' => null,
                'question_1_correct' => false,
                'question_1_marks' => 0,
                'question_1_completed_at' => null,
                'question_2_answer' => null,
                'question_2_correct' => false,
                'question_2_marks' => 0,
                'question_2_analysis' => null,
                'question_2_completed_at' => null,
                'question_3_answer' => null,
                'question_3_correct' => false,
                'question_3_marks' => 0,
                'question_3_analysis' => null,
                'question_3_completed_at' => null,
                'question_4_answer' => null,
                'question_4_correct' => false,
                'question_4_marks' => 0,
                'question_4_analysis' => null,
                'question_4_completed_at' => null,
                'question_5_answer' => null,
                'question_5_correct' => false,
                'question_5_marks' => 0,
                'question_5_analysis' => null,
                'question_5_completed_at' => null,
                'total_marks' => 0,
                'percentage' => 0,
                'grade' => null,
                'completion_time_seconds' => null,
                'completed' => false,
            ]);
        }

        // Reset session data
        session()->forget(['game_start_time', 'final_marks', 'total_possible', 'completion_time']);
        session(['game_start_time' => now()]);

        session()->flash('success', 'Quiz has been restarted. Good luck!');

        return redirect()->route('prompting.show');
    }

    public function __destruct()
    {
        // Close the Google Cloud NLP client connection
        if ($this->languageClient) {
            // If the client has a close() method, call it here. Otherwise, just unset.
             $this->languageClient->close(); // Uncomment if available
            // No try-catch needed since nothing is being executed that throws
        }
    }

    /**
     * Generate feedback message based on marks
     */
    private function generateFeedbackMessage($marks, $baseMessage)
    {
        if ($marks == 6) {
            return "🎉 Perfect! " . $baseMessage;
        } elseif ($marks >= 4) {
            return "👍 Great job! " . $baseMessage;
        } elseif ($marks >= 2) {
            return "🤔 Good effort! " . $baseMessage;
        } else {
            return "💡 Keep trying! " . $baseMessage;
        }
    }

    /**
     * Process weather prompt improvement
     */
    private function processWeatherPrompt($request)
    {
        $prompt = strtolower($request->input('answer'));

        $hasLocation = (
            strpos($prompt, 'london') !== false ||
            strpos($prompt, 'tokyo') !== false ||
            strpos($prompt, 'new york') !== false ||
            strpos($prompt, 'paris') !== false ||
            strpos($prompt, 'city') !== false ||
            strpos($prompt, 'location') !== false ||
            strpos($prompt, 'in') !== false
        );

        $hasTimeframe = (
            strpos($prompt, 'today') !== false ||
            strpos($prompt, 'tomorrow') !== false ||
            strpos($prompt, 'week') !== false ||
            strpos($prompt, 'monday') !== false ||
            strpos($prompt, 'weekend') !== false ||
            strpos($prompt, 'time') !== false
        );

        $hasSpecifics = (
            strpos($prompt, 'temperature') !== false ||
            strpos($prompt, 'rain') !== false ||
            strpos($prompt, 'sunny') !== false ||
            strpos($prompt, 'cloudy') !== false ||
            strpos($prompt, 'condition') !== false
        );

        $isCorrect = $hasLocation && $hasTimeframe && $hasSpecifics;

        if ($isCorrect) {
            $message = "Your prompt is much more specific and useful! You included location, time, and weather details.";
        } else {
            $missing = [];
            if (!$hasLocation) $missing[] = "a specific location";
            if (!$hasTimeframe) $missing[] = "a time period";
            if (!$hasSpecifics) $missing[] = "specific weather conditions";
            $message = "Your prompt needs: " . implode(', ', $missing) . " to be more helpful.";
        }

        return [
            'isCorrect' => $isCorrect,
            'message' => $message,
            'analysis' => compact('hasLocation', 'hasTimeframe', 'hasSpecifics')
        ];
    }

    /**
     * Process recipe prompt improvement
     */
    private function processRecipePrompt($request)
    {
        $prompt = strtolower($request->input('answer'));

        $hasPastaType = (
            strpos($prompt, 'spaghetti') !== false ||
            strpos($prompt, 'penne') !== false ||
            strpos($prompt, 'linguine') !== false ||
            strpos($prompt, 'fettuccine') !== false ||
            strpos($prompt, 'type') !== false
        );

        $hasDietary = (
            strpos($prompt, 'vegetarian') !== false ||
            strpos($prompt, 'vegan') !== false ||
            strpos($prompt, 'gluten-free') !== false ||
            strpos($prompt, 'healthy') !== false ||
            strpos($prompt, 'diet') !== false
        );

        $hasServing = (
            strpos($prompt, '2 people') !== false ||
            strpos($prompt, '4 people') !== false ||
            strpos($prompt, 'family') !== false ||
            strpos($prompt, 'serving') !== false ||
            strpos($prompt, 'portion') !== false
        );

        $hasSkillLevel = (
            strpos($prompt, 'beginner') !== false ||
            strpos($prompt, 'easy') !== false ||
            strpos($prompt, 'simple') !== false ||
            strpos($prompt, 'quick') !== false ||
            strpos($prompt, 'minutes') !== false
        );

        $isCorrect = $hasPastaType && $hasDietary && $hasServing && $hasSkillLevel;

        if ($isCorrect) {
            $message = "Excellent! Your recipe prompt is detailed and specific - perfect for getting helpful cooking instructions!";
        } else {
            $missing = [];
            if (!$hasPastaType) $missing[] = "pasta type";
            if (!$hasDietary) $missing[] = "dietary requirements";
            if (!$hasServing) $missing[] = "serving size";
            if (!$hasSkillLevel) $missing[] = "skill level or time";
            $message = "Your prompt could include: " . implode(', ', $missing) . " for better recipe results.";
        }

        return [
            'isCorrect' => $isCorrect,
            'message' => $message,
            'analysis' => compact('hasPastaType', 'hasDietary', 'hasServing', 'hasSkillLevel')
        ];
    }

    /**
     * Process computer prompt improvement
     */
    private function processComputerPrompt($request)
    {
        $prompt = strtolower($request->input('answer'));

        $hasSpecificAspect = (
            strpos($prompt, 'memory') !== false ||
            strpos($prompt, 'processor') !== false ||
            strpos($prompt, 'cpu') !== false ||
            strpos($prompt, 'storage') !== false ||
            strpos($prompt, 'graphics') !== false ||
            strpos($prompt, 'internet') !== false ||
            strpos($prompt, 'game') !== false
        );

        $hasContext = (
            strpos($prompt, 'when') !== false ||
            strpos($prompt, 'example') !== false ||
            strpos($prompt, 'real world') !== false ||
            strpos($prompt, 'daily life') !== false ||
            strpos($prompt, 'use') !== false
        );

        $hasEngagement = (
            strpos($prompt, 'kid') !== false ||
            strpos($prompt, 'child') !== false ||
            strpos($prompt, 'simple') !== false ||
            strpos($prompt, 'easy') !== false ||
            strpos($prompt, 'explain like') !== false ||
            strpos($prompt, 'fun') !== false
        );

        $hasDetails = (
            strpos($prompt, 'step') !== false ||
            strpos($prompt, 'how') !== false ||
            strpos($prompt, 'why') !== false ||
            strpos($prompt, 'what') !== false ||
            strpos($prompt, 'compare') !== false
        );

        $isCorrect = $hasSpecificAspect && $hasContext && $hasEngagement && $hasDetails;

        if ($isCorrect) {
            $message = "Outstanding! Your advanced prompt is specific, engaging, and perfect for young learners!";
        } else {
            $missing = [];
            if (!$hasSpecificAspect) $missing[] = "specific computer aspect";
            if (!$hasContext) $missing[] = "real-world context";
            if (!$hasEngagement) $missing[] = "age-appropriate language";
            if (!$hasDetails) $missing[] = "detailed questions";
            $message = "To improve your advanced prompt, add: " . implode(', ', $missing) . ".";
        }

        return [
            'isCorrect' => $isCorrect,
            'message' => $message,
            'analysis' => compact('hasSpecificAspect', 'hasContext', 'hasEngagement', 'hasDetails')
        ];
    }

    /**
     * Process role-play prompt
     */
    private function processRolePlayPrompt($request)
    {
        $prompt = strtolower($request->input('answer'));

        $hasRole = (
            strpos($prompt, 'chef') !== false ||
            strpos($prompt, 'teacher') !== false ||
            strpos($prompt, 'scientist') !== false ||
            strpos($prompt, 'doctor') !== false ||
            strpos($prompt, 'artist') !== false ||
            strpos($prompt, 'as a') !== false ||
            strpos($prompt, 'you are') !== false
        );

        $hasRequest = (
            strpos($prompt, 'teach') !== false ||
            strpos($prompt, 'explain') !== false ||
            strpos($prompt, 'show') !== false ||
            strpos($prompt, 'help') !== false ||
            strpos($prompt, 'tell') !== false ||
            strpos($prompt, 'how') !== false
        );

        $hasSpecific = (
            strpos($prompt, 'recipe') !== false ||
            strpos($prompt, 'healthy') !== false ||
            strpos($prompt, 'experiment') !== false ||
            strpos($prompt, 'math') !== false ||
            strpos($prompt, 'science') !== false ||
            strpos($prompt, 'art') !== false ||
            strpos($prompt, 'cooking') !== false
        );

        $isCorrect = $hasRole && $hasRequest && $hasSpecific;

        if ($isCorrect) {
            $message = "Perfect role-play prompt! You clearly defined the AI's role and made a specific request!";
        } else {
            $missing = [];
            if (!$hasRole) $missing[] = "clear role definition";
            if (!$hasRequest) $missing[] = "specific request";
            if (!$hasSpecific) $missing[] = "detailed topic";
            $message = "Your role-play prompt needs: " . implode(', ', $missing) . " to be more effective.";
        }

        return [
            'isCorrect' => $isCorrect,
            'message' => $message,
            'analysis' => compact('hasRole', 'hasRequest', 'hasSpecific')
        ];
    }
}
