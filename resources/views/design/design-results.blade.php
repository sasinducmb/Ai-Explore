@extends('layout.app')

@section('title')
{{ __('Design Tools Results') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-6 py-12 bg-gradient-to-b from-white to-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">🎨 Design Tools Results</h1>
            <p class="text-lg text-gray-600">Great job completing the design challenges!</p>
        </div>

        @if(session('final_marks') !== null)
        <!-- Results Summary Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border border-blue-200">
            <div class="text-center mb-6">
                @php
                $finalMarks = session('final_marks', 0);
                $totalPossible = session('total_possible', 60);
                $percentage = $totalPossible > 0 ? round(($finalMarks / $totalPossible) * 100, 1) : 0;
                $completionTime = session('completion_time', 0);

                // Determine performance level
                if ($percentage >= 90) {
                    $grade = 'A';
                    $gradeColor = 'text-green-600';
                    $gradeEmoji = '🌟';
                    $gradeName = 'Excellent';
                } elseif ($percentage >= 80) {
                    $grade = 'B';
                    $gradeColor = 'text-blue-600';
                    $gradeEmoji = '⭐';
                    $gradeName = 'Great';
                } elseif ($percentage >= 70) {
                    $grade = 'C';
                    $gradeColor = 'text-yellow-600';
                    $gradeEmoji = '👍';
                    $gradeName = 'Good';
                } elseif ($percentage >= 60) {
                    $grade = 'D';
                    $gradeColor = 'text-orange-600';
                    $gradeEmoji = '📝';
                    $gradeName = 'Fair';
                } else {
                    $grade = 'F';
                    $gradeColor = 'text-red-600';
                    $gradeEmoji = '💪';
                    $gradeName = 'Keep Trying';
                }
                @endphp

                <div class="inline-block p-6 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-full mb-4">
                    <span class="text-6xl">{{ $gradeEmoji }}</span>
                </div>

                <h2 class="text-3xl font-bold {{ $gradeColor }} mb-2">Grade: {{ $grade }}</h2>
                <p class="text-xl text-gray-600 mb-4">{{ $gradeName }} Performance!</p>
            </div>

            <!-- Score Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $finalMarks }}</div>
                    <div class="text-sm text-gray-600">Total Score</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $percentage }}%</div>
                    <div class="text-sm text-gray-600">Percentage</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-purple-600">{{ gmdate('i:s', $completionTime) }}</div>
                    <div class="text-sm text-gray-600">Time Taken</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span>{{ $finalMarks }}/{{ $totalPossible }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-1000 ease-out"
                         style="width: {{ $percentage }}%"></div>
                </div>
            </div>

            <!-- Feedback Message -->
            <div class="text-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                @if($percentage >= 90)
                    <p class="text-blue-800">🎉 Outstanding work! You've mastered the design tools and shown excellent creativity and understanding of AI-powered design!</p>
                @elseif($percentage >= 80)
                    <p class="text-blue-800">⭐ Great job! You have a strong grasp of design concepts and AI tools. Keep exploring and creating!</p>
                @elseif($percentage >= 70)
                    <p class="text-blue-800">👍 Good effort! You're getting the hang of design tools. Practice more to improve your skills!</p>
                @elseif($percentage >= 60)
                    <p class="text-blue-800">📝 Fair attempt! You've learned some basics. Try again to strengthen your design understanding!</p>
                @else
                    <p class="text-blue-800">💪 Keep practicing! Design is all about experimentation and learning. Try the challenges again!</p>
                @endif
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8 border border-blue-200">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">📊 Performance Analysis</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Strengths -->
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <h4 class="font-semibold text-green-800 mb-3">🌟 Strengths</h4>
                    <ul class="space-y-2 text-green-700 text-sm">
                        @if($percentage >= 80)
                            <li>• Excellent creative thinking</li>
                            <li>• Strong prompt writing skills</li>
                            <li>• Good understanding of AI tools</li>
                        @elseif($percentage >= 60)
                            <li>• Good effort and participation</li>
                            <li>• Shows creativity potential</li>
                            <li>• Learning AI concepts well</li>
                        @else
                            <li>• Completed all challenges</li>
                            <li>• Shows willingness to learn</li>
                            <li>• Engaged with AI tools</li>
                        @endif
                    </ul>
                </div>

                <!-- Areas for Improvement -->
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h4 class="font-semibold text-yellow-800 mb-3">📈 Areas to Improve</h4>
                    <ul class="space-y-2 text-yellow-700 text-sm">
                        @if($percentage < 80)
                            <li>• Practice writing more detailed prompts</li>
                            <li>• Experiment with different creative approaches</li>
                            <li>• Try the challenges again for better results</li>
                        @else
                            <li>• Explore advanced design techniques</li>
                            <li>• Try more complex creative challenges</li>
                            <li>• Share your knowledge with others</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        @else
        <!-- No Results Found -->
        <div class="bg-white rounded-xl shadow-lg p-8 text-center border border-red-200">
            <div class="text-6xl mb-4">🤔</div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">No Results Found</h2>
            <p class="text-gray-600 mb-6">It looks like you haven't completed the design tools challenge yet.</p>
            <a href="{{ route('design.tools') }}"
               class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                Start Design Challenge
            </a>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('design.tools') }}"
               class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 text-center font-medium">
                🔄 Try Again
            </a>
            <a href="{{ route('explore-ai-tools') }}"
               class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-300 text-center font-medium">
                🚀 Explore More Tools
            </a>
            <a href="{{ route('home') }}"
               class="px-8 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-300 text-center font-medium">
                🏠 Back to Home
            </a>
        </div>

        <!-- Tips Section -->
        <div class="mt-12 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-8 border border-purple-200">
            <h3 class="text-2xl font-bold text-purple-900 mb-4">💡 Design Tips for Future Challenges</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-purple-800 mb-2">Better Prompts:</h4>
                    <ul class="space-y-1 text-purple-700 text-sm">
                        <li>• Be specific and detailed</li>
                        <li>• Use descriptive keywords</li>
                        <li>• Include style preferences</li>
                        <li>• Mention colors and moods</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-purple-800 mb-2">Creative Thinking:</h4>
                    <ul class="space-y-1 text-purple-700 text-sm">
                        <li>• Think outside the box</li>
                        <li>• Combine different ideas</li>
                        <li>• Consider multiple perspectives</li>
                        <li>• Practice regularly</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')

<!-- Add some custom animations -->
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-bounce-in {
        animation: bounceIn 0.8s ease-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add animation classes
        document.querySelector('.max-w-4xl').classList.add('animate-fade-in-up');

        // Animate the grade emoji
        const gradeEmoji = document.querySelector('.text-6xl');
        if (gradeEmoji) {
            gradeEmoji.parentElement.classList.add('animate-bounce-in');
        }
    });
</script>

@endsection
