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
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    In this fun drawing activity, kids will create their own drawing using digital tools in just 30 seconds!
                    We want to see their creative thinking and freestyle expression.
                </p>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Open a simple drawing tool <a href="https://quickdraw.withgoogle.com/" target="_blank"
                                    class="text-blue-600 hover:text-blue-800 underline transition-colors duration-200">https://quickdraw.withgoogle.com/</a></span>
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
                    <!--                <div class="flex justify-center">-->
                    <!--                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">-->
                    <!--                        Submit Drawing-->
                    <!--                    </button>-->
                    <!--                </div>-->
                </form>
            </div>

            <!-- Question 2: AI Picture from Clues -->
            <div id="question-2" class="{{ isset($currentQuestion) && $currentQuestion != 2 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 2: AI Picture from Clues
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids receive a few words related to a secret story image. They must use those words to prompt an AI
                    image generator and create an image that matches the hidden story.
                </p>
                <img src="{{ asset('asset/img/fox.jpeg') }}" alt="AI Picture from Clues Illustration"
                    class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto mb-8 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Read Your Clues.(3–5 secret words that relate to a hidden story
                                image)</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Think About the Scene Write an AI Prompt</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Write an AI Prompt</span>
                        </li>
                    </ul>
                </div>
                <form id="clue-form" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="2">
                    <input type="hidden" name="action" id="action-input-2" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Prompt:</label>
                        <textarea name="prompt" id="prompt-input"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 3: AI Image Transformation Challenge -->
            <div id="question-3" class="{{ isset($currentQuestion) && $currentQuestion != 3 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 3: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids will see two images: Image A (original) and Image B (target). The second image looks similar to the
                    first but has a few changes. Your task is to figure out what’s different and create a prompt that would
                    transform Image A into Image B.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                    <img src="{{ asset('asset/img/image_b.jpeg') }}" alt="Image B"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Carefully look at Image A and Image B.</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Find the differences – what new things are added, removed, or changed?</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Type a prompt below using clear and simple words (e.g.“remove the tree,” “make the sky
                                sunny”).</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="3">
                    <input type="hidden" name="action" id="action-input-3" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 4: AI Image Transformation Challenge -->
            <div id="question-4" class="{{ isset($currentQuestion) && $currentQuestion != 4 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 4: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids receive a few words related to a secret story image. They must use those words to prompt an AI
                    image generator and create an image that matches the hidden story.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a4.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Read Your Clues.(3–5 secret words that relate to a hidden story
                                image)</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Think About the Scene Write an AI Prompt</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Write an AI Prompt</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-4" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="4">
                    <input type="hidden" name="action" id="action-input-4" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-4"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 5: AI Image Transformation Challenge -->
            <div id="question-5" class="{{ isset($currentQuestion) && $currentQuestion != 5 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 5: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids receive a few words related to a secret story image. They must use those words to prompt an AI
                    image generator and create an image that matches the hidden story.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a5.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Read Your Clues.(3–5 secret words that relate to a hidden story
                                image)</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Think About the Scene Write an AI Prompt</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Write an AI Prompt</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-5" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="5">
                    <input type="hidden" name="action" id="action-input-5" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-5"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 6: AI Image Transformation Challenge -->
            <div id="question-6" class="{{ isset($currentQuestion) && $currentQuestion != 6 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 6: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids receive a few words related to a secret story image. They must use those words to prompt an AI
                    image generator and create an image that matches the hidden story.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a6.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Read Your Clues.(3–5 secret words that relate to a hidden story
                                image)</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Think About the Scene Write an AI Prompt</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Write an AI Prompt</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-6" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="6">
                    <input type="hidden" name="action" id="action-input-6" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-6"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 7: AI Image Transformation Challenge -->
            <div id="question-7" class="{{ isset($currentQuestion) && $currentQuestion != 7 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 7: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids receive a few words related to a secret story image. They must use those words to prompt an AI
                    image generator and create an image that matches the hidden story.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a7.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Read Your Clues.(3–5 secret words that relate to a hidden story
                                image)</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Think About the Scene Write an AI Prompt</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Write an AI Prompt</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-7" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="7">
                    <input type="hidden" name="action" id="action-input-7" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-7"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 8: AI Image Transformation Challenge -->
            <div id="question-8" class="{{ isset($currentQuestion) && $currentQuestion != 8 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 8: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids will see two images: Image A (original) and Image B (target). The second image looks similar to the
                    first but has a few changes. Your task is to figure out what’s different and create a prompt that would
                    transform Image A into Image B.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a8.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Carefully look at Image A and Image B.</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Find the differences – what new things are added, removed, or changed?</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Type a prompt below using clear and simple words (e.g.“remove the tree,” “make the sky
                                sunny”).</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-8" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="8">
                    <input type="hidden" name="action" id="action-input-8" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-8"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Question 9: Placeholder for future question -->
            <div id="question-9" class="{{ isset($currentQuestion) && $currentQuestion != 9 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 9: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids will see two images: Image A (original) and Image B (target). The second image looks similar to the
                    first but has a few changes. Your task is to figure out what’s different and create a prompt that would
                    transform Image A into Image B.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a9.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Carefully look at Image A and Image B.</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Find the differences – what new things are added, removed, or changed?</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Type a prompt below using clear and simple words (e.g.“remove the tree,” “make the sky
                                sunny”).</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-9" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="9">
                    <input type="hidden" name="action" id="action-input-9" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-9"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>
            <!-- Question 10: Placeholder for future question -->
            <div id="question-10" class="{{ isset($currentQuestion) && $currentQuestion != 10 ? 'hidden' : '' }}">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold poppins text-gray-900 mb-6 animate-fade-in">
                    Question 10: AI Image Transformation Challenge
                </h2>
                <p
                    class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-10 text-base sm:text-lg leading-relaxed animate-fade-in-delay">
                    Kids will see two images: Image A (original) and Image B (target). The second image looks similar to the
                    first but has a few changes. Your task is to figure out what’s different and create a prompt that would
                    transform Image A into Image B.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <img src="{{ asset('asset/img/image_a10.jpeg') }}" alt="Image A"
                        class="max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto rounded-lg shadow-md hover:shadow-lg transition duration-300">
                </div>
                <div
                    class="bg-gradient-to-br from-blue-50 to-white shadow-lg shadow-blue-100/50 rounded-xl p-6 mb-10 border border-blue-200 transform hover:scale-105 transition duration-300 ease-in-out">
                    <h3 class="font-semibold text-xl sm:text-2xl text-gray-800 mb-5 border-b-2 border-blue-200 pb-2">
                        Instructions:
                    </h3>
                    <ul class="list-disc pl-6 text-gray-700 text-base sm:text-lg space-y-4">
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Carefully look at Image A and Image B.</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Find the differences – what new things are added, removed, or changed?</span>
                        </li>
                        <li class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                            <span>Type a prompt below using clear and simple words (e.g.“remove the tree,” “make the sky
                                sunny”).</span>
                        </li>
                    </ul>
                </div>
                <form id="transform-form-10" action="{{ route('design.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="question" value="10">
                    <input type="hidden" name="action" id="action-input-10" value="submit">
                    <div>
                        <label class="block text-gray-700 font-medium mb-3 text-sm sm:text-base text-left">Your
                            Transformation Prompt:</label>
                        <textarea name="prompt" id="transform-prompt-input-10"
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base"
                            rows="4" required>{{ old('prompt', $prompt ?? '') }}</textarea>
                        @error('prompt')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-center">
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            Submit Prompt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Navigation Buttons -->
            <div class="text-center mt-6 flex justify-center gap-4">
                <a href="#" id="prev-button"
                    class="px-8 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 focus:ring-4 focus:ring-gray-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1 {{ isset($currentQuestion) && $currentQuestion == 1 ? 'hidden' : '' }}">
                    Previous
                </a>
                <a href="#" id="next-button"
                    class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-base sm:text-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-1">
                    {{ isset($currentQuestion) && $currentQuestion == 10 ? 'Finish' : 'Next' }}
                </a>
                <form id="nav-form" action="{{ route('design.submit') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="question" id="current-question-input"
                        value="{{ isset($currentQuestion) ? $currentQuestion : 1 }}">
                    <input type="hidden" name="action" id="nav-action" value="next">
                </form>
            </div>
        </div>
    </section>

    <!-- Popup for result -->
    <div id="result-popup"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center {{ isset($showPopup) && $showPopup ? '' : 'hidden' }} z-50">
        <div
            class="bg-white p-4 sm:p-6 rounded-lg shadow-lg text-center w-full max-w-md sm:max-w-lg transform transition-all duration-300">
            <div id="result-image" class="mb-4">
                <img id="popup-image" src="" alt="Result" class="w-64 h-64 mx-auto object-contain">
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

    <!-- Custom CSS for Animations -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-fade-in-delay {
            animation: fadeIn 0.8s ease-out 0.2s backwards;
        }

        .hidden {
            display: none !important;
        }
    </style>

    <!-- JavaScript for Navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nextButton = document.getElementById('next-button');
            const prevButton = document.getElementById('prev-button');
            const navForm = document.getElementById('nav-form');
            const currentQuestionInput = document.getElementById('current-question-input');
            const navActionInput = document.getElementById('nav-action');
            // Update this to match the total number of questions
            const TOTAL_QUESTIONS = 10;
            let currentQuestion = parseInt(currentQuestionInput.value);

            // Define the action URL to avoid conflicts
            const SUBMIT_URL = '{{ route("design.submit") }}';

            // Function to show result popup with appropriate image
            function showResultPopup(isCorrect, message, marks) {
                const popup = document.getElementById('result-popup');
                const popupImage = document.getElementById('popup-image');
                const resultMessage = document.getElementById('result-message');

                console.log('Showing popup with:', { isCorrect, message, marks });

                // Clear any existing content
                const existingEmoji = document.getElementById('popup-emoji');
                if (existingEmoji) {
                    existingEmoji.remove();
                }

                // Try to load image, fallback to emoji if it fails
                let imageSrc = '';
                let fallbackEmoji = '';

                if (isCorrect) {
                    imageSrc = '{{ asset("asset/img/svg/happy.gif") }}';
                    fallbackEmoji = '😊';
                } else if (marks >= 3) {
                    imageSrc = '{{ asset("asset/img/svg/moderate.gif") }}';
                    fallbackEmoji = '🤔';
                } else {
                    imageSrc = '{{ asset("asset/img/svg/sad.gif") }}';
                    fallbackEmoji = '😢';
                }

                console.log('Attempting to load image:', imageSrc);

                // Test if image exists, otherwise use emoji
                const testImage = new Image();
                testImage.onload = function() {
                    console.log('Image loaded successfully');
                    popupImage.src = imageSrc;
                    popupImage.style.display = 'block';
                    popupImage.className = 'w-64 h-64 mx-auto object-contain';
                };
                testImage.onerror = function() {
                    console.warn('Image not found, using emoji fallback');
                    showEmojiInstead();
                };

                // Function to show emoji fallback
                function showEmojiInstead() {
                    popupImage.style.display = 'none';
                    // Create emoji element
                    const emojiDiv = document.createElement('div');
                    emojiDiv.className = 'text-10xl mb-4';
                    emojiDiv.textContent = fallbackEmoji;
                    emojiDiv.id = 'popup-emoji';

                    popupImage.parentNode.insertBefore(emojiDiv, popupImage);
                }

                // Load the image
                testImage.src = imageSrc;

                resultMessage.textContent = message;
                popup.classList.remove('hidden');

                // Removed auto-close functionality - popup now only closes when user clicks the close button
            }

            // Function to submit form via AJAX
            function submitFormAjax(formElement) {
                const formData = new FormData(formElement);

                console.log('Submitting to URL:', SUBMIT_URL);
                console.log('Form data:', Object.fromEntries(formData));

                fetch(SUBMIT_URL, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Show popup with result
                        showResultPopup(data.isCorrect || false, data.message || 'Response received', data.marks || 0);

                        // Update current question if needed
                        if (data.currentQuestion && data.currentQuestion !== currentQuestion) {
                            currentQuestion = data.currentQuestion;
                            currentQuestionInput.value = currentQuestion;
                            // Wait for user to close popup manually before updating navigation
                            const checkPopupClosed = setInterval(() => {
                                if (popup.classList.contains('hidden')) {
                                    clearInterval(checkPopupClosed);
                                    updateNavigation();
                                }
                            }, 100);
                        }
                    } else {
                        showResultPopup(false, data.message || 'An error occurred.', 0);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResultPopup(false, 'An error occurred. Please try again.', 0);
                });
            }

            // Update button visibility and text based on current question
            function updateNavigation() {
                document.querySelectorAll('[id^="question-"]').forEach(el => el.classList.add('hidden'));
                document.getElementById(`question-${currentQuestion}`).classList.remove('hidden');
                prevButton.classList.toggle('hidden', currentQuestion === 1);
                nextButton.textContent = currentQuestion === TOTAL_QUESTIONS ? 'Finish' : 'Next';
            }

            // Initial setup
            updateNavigation();

            // Handle form submissions with AJAX - use more specific selectors
            const formsToHandle = [
                'draw-form',
                'clue-form',
                'transform-form',
                'transform-form-4',
                'transform-form-5',
                'transform-form-6',
                'transform-form-7',
                'transform-form-8',
                'transform-form-9',
                'transform-form-10'
            ];

            formsToHandle.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Form submitted:', formId);
                        submitFormAjax(this);
                    });
                }
            });

            // Next button click
            nextButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentQuestion < TOTAL_QUESTIONS) {
                    currentQuestion++;
                    currentQuestionInput.value = currentQuestion;
                    navActionInput.value = 'next';
                    updateNavigation();
                } else {
                    navActionInput.value = 'finish';
                    navForm.submit();
                }
            });

            // Previous button click
            prevButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentQuestion > 1) {
                    currentQuestion--;
                    currentQuestionInput.value = currentQuestion;
                    navActionInput.value = 'prev';
                    updateNavigation();
                }
            });

            // Handle closing the popup
            document.getElementById('close-popup').addEventListener('click', function() {
                document.getElementById('result-popup').classList.add('hidden');
            });
        });
    </script>
@endsection
