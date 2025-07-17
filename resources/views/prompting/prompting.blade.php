@extends('layout.app')

@section('title')
{{ __('Prompting Tool') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-4 sm:px-6 lg:px-8 py-12 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl font-bold poppins text-gray-900 mb-4">Prompting Tool</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-8 sm:mb-12 text-sm sm:text-base">
            Use text prompts to interact with AI and spark ideas. Explore creative ways to generate content, solve problems, or learn new concepts.
        </p>

        <div class="bg-white shadow-md shadow-blue-100 rounded-xl px-4 sm:px-6 py-6 border border-blue-200">
            <h3 class="font-semibold text-lg sm:text-xl text-gray-800 mb-4">AI Tool Challenge</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">
                Children will engage with AI tools to answer fun questions. This activity lets them experience how different tools respond and helps them develop critical thinking.
            </p>

            <div class="max-w-2xl mx-auto">
                <!-- Question 1: MCQ -->
                <div id="question-1" class="{{ isset($currentQuestion) && $currentQuestion != 1 ? 'hidden' : '' }}">
                    <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 1: What is the prompting tool introduced by Google?</h4>
                    <form id="mcq-form" action="{{ route('prompting.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="question" value="1">
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Choose an AI Tool:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4" id="ai-tool-cards">
                                @php
                                $aiTools = ['Grok', 'Bard', 'Copilot', 'ChatGPT', 'Claude'];
                                @endphp
                                @foreach ($aiTools as $tool)
                                <div class="ai-tool-card cursor-pointer p-4 border rounded-lg text-center bg-gray-50 hover:bg-blue-50 transition duration-300" data-tool="{{ $tool }}">
                                    <span class="font-medium text-gray-800 text-sm sm:text-base">{{ $tool }}</span>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="answer" id="answer-input" value="" required>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4">
                            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base">
                                Submit Answer
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Question 2: Reverse Prompt Builder -->
                <div id="question-2" class="{{ isset($currentQuestion) && $currentQuestion == 2 ? '' : 'hidden' }}">
                    <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 2: Reverse Prompt Builder</h4>
                    <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                        <p>"Electric vehicles, or EVs, are cars that use electricity instead of petrol or diesel. One of the biggest benefits of EVs is that they are better for the environment because they reduce air pollution. They are also quieter and need less maintenance. However, a disadvantage is that they can be expensive and may take a long time to charge. Some people also find it hard to find charging stations. Still, many believe EVs will become more popular in the future."</p>
                        <p class="mt-4 font-medium">Instructions: Guess what prompt was used to generate that answer! Read the paragraph above, then think: What question could I ask an AI to get that kind of answer? Write your question and submit it.</p>
                    </div>
                    <form id="prompt-form" action="{{ route('prompting.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="question" value="2">
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Prompt:</label>
                            <textarea name="answer" id="prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base">
                            Submit Prompt
                        </button>
                    </form>
                </div>

                <!-- Question 3: Build a Super Question with AI -->
                <div id="question-3" class="{{ isset($currentQuestion) && $currentQuestion == 3 ? '' : 'hidden' }}">
                    <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 3: Build a Super Question with AI</h4>
                    <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                        <p>In this creative task, you'll learn how to build a smart question with the help of AI. Start by selecting a topic, then improve your question step by step to make it clear and detailed.</p>
                        <p class="mt-4 font-medium">Instructions:</p>
                        <ul class="list-disc pl-5">
                            <li>Select one of the topics below: animals, ocean, robot, or computers.</li>
                            <li>Start with a simple question about your topic (e.g., 'Tell me about animals').</li>
                            <li>Imagine asking an AI your question and how it responds.</li>
                            <li>Improve your question to make it more detailed and clear based on the imagined response.</li>
                            <li>Write your final improved prompt below and submit it.</li>
                            <li>Your score depends on how detailed and clear your final question is!</li>
                        </ul>
                    </div>
                    <form id="super-prompt-form" action="{{ route('prompting.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="question" value="3">
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Choose a Topic:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4" id="topic-cards">
                                @php
                                $topics = ['animals', 'ocean', 'robot', 'computers'];
                                @endphp
                                @foreach ($topics as $topic)
                                <div class="topic-card cursor-pointer p-4 border rounded-lg text-center bg-gray-50 hover:bg-blue-50 transition duration-300 {{ isset($selectedTopic) && $selectedTopic == $topic ? 'bg-blue-100 border-blue-500' : '' }}" data-topic="{{ $topic }}">
                                    <span class="font-medium text-gray-800 text-sm sm:text-base">{{ ucfirst($topic) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="topic" id="topic-input" value="{{ $selectedTopic ?? '' }}" required>
                            @error('topic')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Improved Prompt:</label>
                            <textarea name="answer" id="super-prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base">
                            Submit Prompt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popup for result -->
<div id="result-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center {{ isset($showPopup) && $showPopup ? '' : 'hidden' }}">
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg text-center w-full max-w-xs sm:max-w-sm">
        <div id="result-icon" class="text-5xl sm:text-6xl mb-4">{{ isset($isCorrect) && $isCorrect ? '😊' : '😢' }}</div>
        <p id="result-message" class="text-base sm:text-lg font-medium text-gray-800">
            {{ $resultMessage ?? 'An error occurred. Please try again.' }}
        </p>
        <button id="close-popup" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm sm:text-base">
            Close
        </button>
    </div>
</div>

@include('layout.footer')

<!-- JavaScript for card selection, popup display, and question navigation -->
<script>
    const aiCards = document.querySelectorAll('.ai-tool-card');
    const topicCards = document.querySelectorAll('.topic-card');
    const answerInput = document.getElementById('answer-input');
    const topicInput = document.getElementById('topic-input');
    const popup = document.getElementById('result-popup');
    const closePopupBtn = document.getElementById('close-popup');
    const question1 = document.getElementById('question-1');
    const question2 = document.getElementById('question-2');
    const question3 = document.getElementById('question-3');

    // Event listener for AI tool card selection (Question 1)
    aiCards.forEach(card => {
        card.addEventListener('click', function () {
            aiCards.forEach(c => c.classList.remove('bg-blue-100', 'border-blue-500'));
            this.classList.add('bg-blue-100', 'border-blue-500');
            answerInput.value = this.dataset.tool;
        });
    });

    // Event listener for topic card selection (Question 3)
    topicCards.forEach(card => {
        card.addEventListener('click', function () {
            topicCards.forEach(c => c.classList.remove('bg-blue-100', 'border-blue-500'));
            this.classList.add('bg-blue-100', 'border-blue-500');
            topicInput.value = this.dataset.topic;
        });
    });

    // Handle closing the popup
    closePopupBtn.addEventListener('click', function () {
        popup.classList.add('hidden');
    });

    // Show popup and set correct question on page load
    window.onload = function() {
        if ({{ isset($showPopup) && $showPopup ? 'true' : 'false' }}) {
            popup.classList.remove('hidden');
        }
        // Ensure correct question is displayed based on server state
        if ({{ isset($currentQuestion) ? $currentQuestion : 1 }} == 1) {
            question1.classList.remove('hidden');
            question2.classList.add('hidden');
            question3.classList.add('hidden');
        } else if ({{ isset($currentQuestion) ? $currentQuestion : 1 }} == 2) {
            question1.classList.add('hidden');
            question2.classList.remove('hidden');
            question3.classList.add('hidden');
        } else if ({{ isset($currentQuestion) ? $currentQuestion : 1 }} == 3) {
            question1.classList.add('hidden');
            question2.classList.add('hidden');
            question3.classList.remove('hidden');
            // Restore selected topic if available
            if ({{ isset($selectedTopic) ? 'true' : 'false' }}) {
                const selectedTopic = "{{ $selectedTopic ?? '' }}";
                if (selectedTopic) {
                    const selectedCard = document.querySelector(`.topic-card[data-topic="${selectedTopic}"]`);
                    if (selectedCard) {
                        selectedCard.classList.add('bg-blue-100', 'border-blue-500');
                    }
                }
            }
        }
    }
</script>

@endsection
