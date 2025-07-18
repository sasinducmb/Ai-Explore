@extends('layout.app')

@section('title')
{{ __('Design Tools') }}
@endsection

@section('content')
<!-- Navigation -->
@include('layout.nav')

<section class="px-6 py-12 bg-gradient-to-b from-white to-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto text-center">
        <!-- Question 1: Draw with AI! -->
        <div id="question-1" class="{{ isset($currentQuestion) && $currentQuestion != 1 ? 'hidden' : '' }}">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                Question 1 - Draw with AI!
            </h2>
            <img src="{{ asset('asset/img/quickdraw.jpeg') }}" alt="Draw with AI Illustration"
                 class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto mb-10 rounded-lg hover:shadow-lg transition duration-300">
            <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                In this fun drawing activity, kids will create their own drawing using digital tools in just 30 seconds! We want to see their creative thinking and freestyle expression.
            </p>
            <div class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                    Instructions:
                </h3>
                <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>Open a simple drawing tool <a href="https://quickdraw.withgoogle.com/" target="_blank" class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">https://quickdraw.withgoogle.com/</a></span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>Draw anything you like within 30 seconds, express your imagination!</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>Save and enjoy your creation!</span>
                    </li>
                </ul>
            </div>
            <form id="draw-form" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="question" value="1">
                <input type="hidden" name="action" id="action-input-1" value="submit">
                <div class="flex justify-center">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        Submit Drawing
                    </button>
                </div>
            </form>
        </div>

        <!-- Question 2: AI Picture from Clues -->
        <div id="question-2" class="{{ isset($currentQuestion) && $currentQuestion != 2 ? 'hidden' : '' }}">
            <img src="{{ asset('asset/img/ai-picture-placeholder.jpg') }}" alt="AI Picture from Clues Illustration"
                 class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto mb-8 rounded-lg shadow-md hover:shadow-lg transition duration-300">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                Question 2: AI Picture from Clues
            </h2>
            <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                Kids receive a few words related to a secret story image. They must use those words to prompt an AI image generator and create an image that matches the hidden story.
            </p>
            <div class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                    Instructions:
                </h3>
                <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>You will be given 4 key words (e.g., 'fox', 'rock', 'thinking', 'sunset').</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>Use a kid-friendly image tool like <a href="https://www.craiyon.com/" target="_blank" class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">Craiyon</a>.</span>
                    </li>
                    <li class="flex items-center">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                        <span>Enter your prompt below and submit to check if it matches the secret story.</span>
                    </li>
                </ul>
            </div>
            <form id="clue-form" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="question" value="2">
                <input type="hidden" name="action" id="action-input-2" value="submit">
                <div>
                    <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your Prompt:</label>
                    <textarea name="prompt" id="prompt-input" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                    @error('prompt')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        Submit Prompt
                    </button>
                </div>
            </form>
        </div>

        <!-- Next/Finish Button -->
        <div class="text-center mt-6">
            <a href="#" id="next-button" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                Next
            </a>
            <form id="next-form" action="{{ route('design.submit') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="question" id="current-question-input" value="{{ isset($currentQuestion) ? $currentQuestion : 1 }}">
                <input type="hidden" name="action" value="next">
            </form>
        </div>
    </div>
</section>

<!-- Popup for result -->
<div id="result-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center {{ isset($showPopup) && $showPopup ? '' : 'hidden' }} z-50">
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg text-center w-full max-w-xs sm:max-w-sm transform transition-all duration-300">
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

<!-- Custom CSS for Animations -->
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInDelay {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    .animate-fade-in-delay {
        animation: fadeInDelay 0.8s ease-out 0.2s backwards;
    }
    .hidden {
        display: none !important;
    }
</style>

<!-- JavaScript for Next Button Navigation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nextButton = document.getElementById('next-button');
        const currentQuestionInput = document.getElementById('current-question-input');
        let currentQuestion = parseInt(currentQuestionInput.value);

        nextButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentQuestion < 2) { // Assuming only 2 questions for now
                currentQuestion++;
                currentQuestionInput.value = currentQuestion;
                document.getElementById('question-' + (currentQuestion - 1)).classList.add('hidden');
                document.getElementById('question-' + currentQuestion).classList.remove('hidden');
                if (currentQuestion === 2) {
                    nextButton.textContent = 'Finish'; // Change to 'Finish' on last question
                }
            } else {
                // Submit form to move to next step or results
                document.getElementById('next-form').submit();
            }
        });

        // Set initial question visibility
        if (currentQuestion === 2) {
            document.getElementById('question-1').classList.add('hidden');
            document.getElementById('question-2').classList.remove('hidden');
            nextButton.textContent = 'Finish';
        }
    });

    // Handle closing the popup
    document.getElementById('close-popup').addEventListener('click', function() {
        document.getElementById('result-popup').classList.add('hidden');
    });
</script>
@endsection
