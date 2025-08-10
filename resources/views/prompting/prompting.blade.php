@extends('layout.app')

@section('title')
{{ __('Prompting Tool') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-4 sm:px-6 lg:px-8 py-12 bg-white min-h-screen">
    @if(session('success'))
    <div class="max-w-7xl mx-auto mb-6">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3 close-alert">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </button>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl font-bold poppins text-gray-900 mb-4">Prompting Tool</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-8 sm:mb-12 text-sm sm:text-base">
            Use text prompts to interact with AI and spark ideas. Explore creative ways to generate content, solve
            problems, or learn new concepts.
        </p>

        <div class="bg-white shadow-md shadow-blue-100 rounded-xl px-4 sm:px-6 py-6 border border-blue-200">
            <h3 class="font-semibold text-lg sm:text-xl text-gray-800 mb-4">AI Tool Challenge</h3>
            <p class="text-gray-600 mb-4 text-sm sm:text-base">
                Children will engage with AI tools to answer fun questions. This activity lets them experience how
                different tools respond and helps them develop critical thinking.
            </p>

            <div class="max-w-2xl mx-auto">
                <form id="all-answers-form" action="{{ route('prompting.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="finish">
                    <!-- Question 1: MCQ -->
                    <div id="question-1" class="{{ isset($currentQuestion) && $currentQuestion != 1 ? 'hidden' : '' }}">
                        <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 1: What is
                            the prompting tool introduced by Google?</h4>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Choose an AI
                                Tool:</label>
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
                            <input type="hidden" name="q1_answer" id="answer-input" value="" required>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4 justify-end">
                            <button type="button" id="next-btn-1" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base" disabled>
                                Next
                            </button>
                        </div>
                    </div>

                    <!-- Question 2: First Prompt Improvement -->
                    <div id="question-2" class="{{ isset($currentQuestion) && $currentQuestion == 2 ? '' : 'hidden' }}">
                        <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 2: Weather Prompt Challenge</h4>
                        <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                            <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                                <p class="font-medium text-red-500">Bad Prompt:</p>
                                <p class="mt-2">"What's the weather like?"</p>
                            </div>

                            <div class="mt-4 bg-green-50 p-4 rounded-lg">
                                <p class="font-medium text-green-600">Make this prompt better by:</p>
                                <ul class="list-disc pl-5 mt-2">
                                    <li>Adding a specific location</li>
                                    <li>Specifying a time period</li>
                                    <li>Asking about specific weather conditions</li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Improved Prompt:</label>
                            <textarea name="q2_answer" id="improved-prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4 justify-between">
                            <button type="button" id="prev-btn-2" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 text-sm sm:text-base">
                                Prev
                            </button>
                            <button type="button" id="next-btn-2" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base" disabled>
                                Next
                            </button>
                        </div>
                    </div>

                    <!-- Question 3: Second Prompt Improvement -->
                    <div id="question-3" class="{{ isset($currentQuestion) && $currentQuestion == 3 ? '' : 'hidden' }}">
                        <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 3: Food Recipe Challenge</h4>
                        <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                            <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                                <p class="font-medium text-red-500">Bad Prompt:</p>
                                <p class="mt-2">"How do I make pasta?"</p>
                            </div>

                            <div class="mt-4 bg-green-50 p-4 rounded-lg">
                                <p class="font-medium text-green-600">Make this prompt better by:</p>
                                <ul class="list-disc pl-5 mt-2">
                                    <li>Specifying the type of pasta</li>
                                    <li>Mentioning dietary requirements</li>
                                    <li>Adding serving size information</li>
                                    <li>Including cooking skill level</li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Improved Prompt:</label>
                            <textarea name="q3_answer" id="second-improved-prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4 justify-between">
                            <button type="button" id="prev-btn-3" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 text-sm sm:text-base">
                                Prev
                            </button>
                            <button type="button" id="next-btn-3" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base" disabled>
                                Next
                            </button>
                        </div>
                    </div>

                    <!-- Question 4: Advanced Prompt Improvement -->
                    <div id="question-4" class="{{ isset($currentQuestion) && $currentQuestion == 4 ? '' : 'hidden' }}">
                        <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 4: Advanced Prompt Improvement</h4>
                        <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                            <p>Now let's tackle a more challenging prompt improvement task! Make this vague question about technology more specific and engaging.</p>

                            <div class="mt-4 bg-gray-50 p-4 rounded-lg">
                                <p class="font-medium text-red-500">Bad Prompt:</p>
                                <p class="mt-2">"How do computers work?"</p>
                            </div>

                            <div class="mt-4">
                                <p class="font-medium">Advanced Instructions:</p>
                                <ul class="list-disc pl-5 mt-2">
                                    <li>Focus on a specific aspect of computers</li>
                                    <li>Add context or real-world applications</li>
                                    <li>Make it engaging for young learners</li>
                                    <li>Include specific details or numbers</li>
                                    <li>Ask for examples or comparisons</li>
                                </ul>
                            </div>

                            <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                                <p class="font-medium text-blue-600">Tip:</p>
                                <p class="mt-2">Think about what specifically interests you about computers. Is it the memory? The speed? How they process games? Use that interest in your prompt!</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Advanced Improved Prompt:</label>
                            <textarea name="q4_answer" id="advanced-prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4 justify-between">
                            <button type="button" id="prev-btn-4" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 text-sm sm:text-base">
                                Prev
                            </button>
                            <button type="button" id="next-btn-4" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm sm:text-base" disabled>
                                Next
                            </button>
                        </div>
                    </div>

                    <!-- Question 5: Role-Play Prompting -->
                    <div id="question-5" class="{{ isset($currentQuestion) && $currentQuestion == 5 ? '' : 'hidden' }}">
                        <h4 class="font-semibold text-base sm:text-lg text-gray-800 mb-4 text-left">🧠 Question 5: Role-Play Prompting</h4>
                        <div class="text-left text-gray-600 mb-4 text-sm sm:text-base">
                            <p>In this game, you pretend the AI has a special role (like a scientist, chef, or teacher) and you ask it a question in that role. This helps you learn to ask questions for different situations.</p>
                            <div class="mt-4 bg-yellow-50 p-4 rounded-lg">
                                <p class="font-medium text-yellow-700">Role: Chef</p>
                                <p class="mt-2">Write a prompt as if you are talking to an AI chef.</p>
                            </div>
                            <div class="mt-4 bg-green-50 p-4 rounded-lg">
                                <p class="font-medium text-green-600">Example:</p>
                                <ul class="mt-2 space-y-2">
                                    <li>
                                        <span class="text-yellow-700">Role:</span> Chef<br>
                                        <span class="text-green-600">Prompt:</span> “Teach me how to make a healthy fruit smoothie.”
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base">Your Role-Play Prompt:</label>
                            <textarea name="q5_answer" id="roleplay-prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required></textarea>
                            @error('answer')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex space-x-4 justify-between">
                            <button type="button" id="prev-btn-5" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 text-sm sm:text-base">
                                Prev
                            </button>
                            <button type="submit" id="finish-btn" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 text-sm sm:text-base" disabled>
                                Finish
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Popup for result -->
<div id="result-popup"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center {{ isset($showPopup) && $showPopup ? '' : 'hidden' }}">
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg text-center w-full max-w-xs sm:max-w-sm">
        <div id="result-icon" class="text-5xl sm:text-6xl mb-4">{{ isset($isCorrect) && $isCorrect ? '😊' : '😢' }}</div>
        <div id="result-icon" class="mb-4">
            <video autoplay loop muted playsinline class="w-full h-auto max-h-40 mx-auto">
                    <source src="{{ asset('img/svg/happy.mp4') }}" type="video/mp4">
            </video>
        </div>
        <p id="result-message" class="text-base sm:text-lg font-medium text-gray-800">
            {{ $resultMessage ?? 'An error occurred. Please try again.' }}
        </p>
        <button id="close-popup"
                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm sm:text-base">
            Close
        </button>
    </div>
</div>



@include('layout.footer')

<!-- JavaScript for card selection, popup display, and question navigation -->
<script>
    const aiCards = document.querySelectorAll('.ai-tool-card');
    const answerInput = document.getElementById('answer-input');
    const popup = document.getElementById('result-popup');
    const closePopupBtn = document.getElementById('close-popup');

    const question1 = document.getElementById('question-1');
    const question2 = document.getElementById('question-2');
    const question3 = document.getElementById('question-3');
    const question4 = document.getElementById('question-4');
    const question5 = document.getElementById('question-5');

    const nextBtn1 = document.getElementById('next-btn-1');
    const nextBtn2 = document.getElementById('next-btn-2');
    const nextBtn3 = document.getElementById('next-btn-3');
    const nextBtn4 = document.getElementById('next-btn-4');
    const finishBtn = document.getElementById('finish-btn');

    const improvedPromptInput = document.getElementById('improved-prompt-input');
    const secondImprovedPromptInput = document.getElementById('second-improved-prompt-input');
    const advancedPromptInput = document.getElementById('advanced-prompt-input');
    const roleplayPromptInput = document.getElementById('roleplay-prompt-input');

    // Event listener for AI tool card selection (Question 1)
    aiCards.forEach(card => {
        card.addEventListener('click', function () {
            aiCards.forEach(c => c.classList.remove('bg-blue-100', 'border-blue-500'));
            this.classList.add('bg-blue-100', 'border-blue-500');
            answerInput.value = this.dataset.tool;
            nextBtn1.disabled = false;
        });
    });

    // Function to submit individual question
    function submitQuestion(questionNumber, answer, callback) {
        console.log('Submitting question', questionNumber, 'with answer:', answer); // Debug log

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('action', 'submit');
        formData.append('question', questionNumber);
        formData.append('answer', answer);

        fetch('{{ route("prompting.submit") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            if (callback) callback(data);
        })
        .catch(error => {
            console.error('Error:', error);
            if (callback) callback(null);
        });
    }

    // Function to show question
    function showQuestion(n) {
        [question1, question2, question3, question4, question5].forEach((q, idx) => {
            if (q) q.classList.toggle('hidden', idx !== n - 1);
        });
    }

    // Handle Next button for Question 1 - Submit and move to next
    nextBtn1.addEventListener('click', function() {
        if (answerInput.value) {
            nextBtn1.disabled = true;
            nextBtn1.textContent = 'Saving...';

            submitQuestion(1, answerInput.value, function(response) {
                nextBtn1.textContent = 'Next';
                nextBtn1.disabled = false; // Re-enable button
                if (response && response.success) {
                    showQuestion(2);
                } else {
                    console.error('Failed to save question 1');
                }
            });
        }
    });

    // Handle Next button for Question 2 - Submit and move to next
    nextBtn2.addEventListener('click', function() {
        if (improvedPromptInput.value.trim()) {
            nextBtn2.disabled = true;
            nextBtn2.textContent = 'Saving...';

            submitQuestion(2, improvedPromptInput.value, function(response) {
                nextBtn2.textContent = 'Next';
                nextBtn2.disabled = false;
                if (response && response.success) {
                    showQuestion(3);
                } else {
                    console.error('Failed to save question 2');
                }
            });
        }
    });

    // Handle Next button for Question 3 - Submit and move to next
    nextBtn3.addEventListener('click', function() {
        if (secondImprovedPromptInput.value.trim()) {
            nextBtn3.disabled = true;
            nextBtn3.textContent = 'Saving...';

            submitQuestion(3, secondImprovedPromptInput.value, function(response) {
                nextBtn3.textContent = 'Next';
                nextBtn3.disabled = false;
                if (response && response.success) {
                    showQuestion(4);
                } else {
                    console.error('Failed to save question 3');
                }
            });
        }
    });

    // Handle Next button for Question 4 - Submit and move to next
    nextBtn4.addEventListener('click', function() {
        if (advancedPromptInput.value.trim()) {
            nextBtn4.disabled = true;
            nextBtn4.textContent = 'Saving...';

            submitQuestion(4, advancedPromptInput.value, function(response) {
                nextBtn4.textContent = 'Next';
                nextBtn4.disabled = false;
                if (response && response.success) {
                    showQuestion(5);
                } else {
                    console.error('Failed to save question 4');
                }
            });
        }
    });

    // Handle Previous buttons
    document.getElementById('prev-btn-2').addEventListener('click', function() { showQuestion(1); });
    document.getElementById('prev-btn-3').addEventListener('click', function() { showQuestion(2); });
    document.getElementById('prev-btn-4').addEventListener('click', function() { showQuestion(3); });
    document.getElementById('prev-btn-5').addEventListener('click', function() { showQuestion(4); });

    // Enable/disable buttons based on input
    answerInput.addEventListener('change', function() {
        nextBtn1.disabled = !this.value.trim();
    });

    improvedPromptInput.addEventListener('input', function() {
        nextBtn2.disabled = !this.value.trim();
    });

    secondImprovedPromptInput.addEventListener('input', function() {
        nextBtn3.disabled = !this.value.trim();
    });

    advancedPromptInput.addEventListener('input', function() {
        nextBtn4.disabled = !this.value.trim();
    });

    roleplayPromptInput.addEventListener('input', function() {
        finishBtn.disabled = !this.value.trim();
    });

    // Handle form submission on finish
    finishBtn.addEventListener('click', function(e) {
        e.preventDefault();

        if (confirm('Are you sure you want to finish and submit all your answers?')) {
            finishBtn.disabled = true;
            finishBtn.textContent = 'Submitting...';

            // Submit final question first
            submitQuestion(5, roleplayPromptInput.value, function(response) {
                if (response && response.success) {
                    // Now submit the completion form
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('action', 'finish');

                    fetch('{{ route("prompting.submit") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("prompting.results") }}';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = '{{ route("prompting.results") }}';
                    });
                } else {
                    console.error('Failed to save final question');
                    finishBtn.disabled = false;
                    finishBtn.textContent = 'Finish';
                }
            });
        }
    });

    // Handle popup close
    closePopupBtn.addEventListener('click', function() {
        popup.classList.add('hidden');
    });

    // On page load
    window.onload = function() {
        if ({{ isset($showPopup) && $showPopup ? 'true' : 'false' }}) {
            popup.classList.remove('hidden');
        }

        const currentQuestion = {{ isset($currentQuestion) ? $currentQuestion : 1 }};
        showQuestion(currentQuestion);
    };

    // Handle alert closing
    document.querySelectorAll('.close-alert').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('[role="alert"]').remove();
        });
    });
</script>

@endsection
