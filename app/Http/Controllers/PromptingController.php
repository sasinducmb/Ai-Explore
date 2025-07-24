<?php

namespace App\Http\Controllers;

use Google\Cloud\Language\V2\Client\LanguageServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Language\V1\Document;
use Google\Cloud\Language\V1\Document\Type;

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
        return view('prompting.prompting', ['currentQuestion' => 1]);
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

        if ($action === 'next') {
            // Move to the next question without validation
            if ($question == 1) {
                $currentQuestion = 2; // Fixed typo: 'intestine' to 'Question'
            } elseif ($question == 2) {
                $currentQuestion = 3;
            } elseif ($question == 3) {
                $currentQuestion = 3; // Stay on Question 3
            }
        } elseif ($action === 'finish') {
            // Handle finish action
            return redirect()->route('prompting.results');
        } else {
            // Handle submit action with validation
            if ($question == 1) {
                $request->validate([
                    'answer' => 'required|string|in:Grok,Bard,Copilot,ChatGPT,Claude',
                ]);
                $isCorrect = $request->answer === 'Bard';
                $resultMessage = $isCorrect ? 'Correct! Great job!' : 'Oops, that\'s incorrect. Try again!';
                $imoji = $isCorrect ? 'happy' : 'sad';
                $currentQuestion = $isCorrect ? 2 : 1;
            } elseif ($question == 2) {
                $request->validate([
                    'answer' => 'required|string|max:5000',
                ]);

                Log::info('Submitted prompt for Question 2: ' . $request->answer);

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

                        if ($isCorrect) {
                            $resultMessage = 'Correct! Your prompt is relevant and well-structured! Found entities: ' . implode(', ', array_unique($foundEntities));
                            $imoji =  'happy';
                            $currentQuestion = 3;
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
                            $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found entities: ' . (empty($foundEntities) ? 'none' : implode(', ', array_unique($foundEntities)));
                            $imoji = 'moderate';
                            $currentQuestion = 2;
                        }
                    } else {
                        // Fallback to original keyword-based logic
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

                        // Simplified question pattern check
                        $textLower = strtolower($request->answer);
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

                        if ($isCorrect) {
                            $resultMessage = 'Correct! Your prompt is good! Found relevant keywords: ' . implode(', ', $uniqueKeywords);
                            $imoji = 'happy';
                            $currentQuestion = 3;
                        } else {
                            $suggestions = [];
                            if (count($uniqueKeywords) < 2) {
                                $suggestions[] = 'include more relevant keywords like advantages, disadvantages, pollution, or charging';
                            }
                            if (!$hasQuestionPattern) {
                                $suggestions[] = 'make it more like a question or request';
                            }
                            $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(' and ', $suggestions) . '. Found keywords: ' . (empty($uniqueKeywords) ? 'none' : implode(', ', $uniqueKeywords));
                            $imoji = 'moderate';
                            $currentQuestion = 2;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error in Question 2: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $imoji =  'sad';
                    $isCorrect = false;
                    $currentQuestion = 2;
                }
            } elseif ($question == 3) {
                $request->validate([
                    'topic' => 'required|string|in:animals,ocean,robot,computers',
                    'answer' => 'required|string|max:5000',
                ]);

                Log::info('Submitted prompt for Question 3: ' . $request->answer . ' | Topic: ' . $request->topic);

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

                        if ($isCorrect) {
                            $resultMessage = "Correct! Your improved prompt is clear, detailed, and relevant to '$selectedTopic'! Found entities: " . implode(', ', array_unique($foundEntities));
                            $imoji =  'happy';
                            $currentQuestion = 3;
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
                            $resultMessage = 'Your prompt needs improvement. Try to: ' . implode(', ', $suggestions) . '. Found entities: ' . (empty($foundEntities) ? 'none' : implode(', ', array_unique($foundEntities)));
                            $imoji = 'moderate';
                            $currentQuestion = 3;
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

                        if ($isCorrect) {
                            $resultMessage = "Correct! Your improved prompt is clear, detailed, and relevant to '$selectedTopic'! Found topic keywords: " . implode(', ', $foundTopicKeywords);
                            $imoji = 'happy';
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
                            $imoji = 'moderate';
                            $currentQuestion = 3;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Unexpected error in Question 3: ' . $e->getMessage());
                    $resultMessage = 'An error occurred while analyzing your prompt. Please try again.';
                    $isCorrect = false;
                    $currentQuestion = 3;
                }
            }
        }


        // dd([
        //     'showPopup' => $action === 'submit',
        //     'isCorrect' => $isCorrect,
        //     'imoji' => $imoji,
        //     'resultMessage' => $resultMessage,
        //     'currentQuestion' => $currentQuestion,
        //     'selectedTopic' => $selectedTopic,
        // ]);

        return view('prompting.prompting', [
            'showPopup' => $action === 'submit',
            'isCorrect' => $isCorrect,
            'imoji' => $imoji,
            'resultMessage' => $resultMessage,
            'currentQuestion' => $currentQuestion,
            'selectedTopic' => $selectedTopic,
        ]);
    }

    public function results()
    {
        return view('prompting.results');
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
