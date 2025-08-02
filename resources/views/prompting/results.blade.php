@extends('layout.app')

@section('title')
{{ __('Prompting Tool Results') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-4 sm:px-6 lg:px-8 py-12 bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl sm:text-4xl font-bold poppins text-gray-900 mb-4">
                🎉 Congratulations! You've Completed the AI Prompting Tool!
            </h2>
            <p class="text-gray-600 font-medium poppins max-w-2xl mx-auto text-sm sm:text-base">
                Here are your detailed results and performance analysis.
            </p>
        </div>

        <!-- Main Results Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 sm:p-8 mb-8 border border-blue-200">
            <!-- Overall Score -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                    <span class="text-3xl font-bold text-white">{{ $result->grade }}</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Final Score</h3>
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ $result->total_marks }}/{{ $result->total_possible_marks }}</div>
                <div class="text-xl text-gray-600">{{ number_format($result->percentage, 1) }}%</div>
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-1000"
                             style="width: {{ $result->percentage }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Question Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Question 1 -->
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="text-2xl mb-2">🧠</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Question 1</h4>
                    <p class="text-sm text-gray-600 mb-3">AI Tool Knowledge</p>
                    <div class="text-2xl font-bold {{ $result->question_1_correct ? 'text-green-600' : 'text-red-500' }}">
                        {{ $result->question_1_marks }}/10
                    </div>
                    <div class="mt-2">
                        @if($result->question_1_correct)
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                ✓ Correct
                            </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                ✗ Incorrect
                            </span>
                        @endif
                    </div>
                    @if($result->question_1_answer)
                    <p class="text-xs text-gray-500 mt-2">Your answer: {{ $result->question_1_answer }}</p>
                    @endif
                </div>

                <!-- Question 2 -->
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="text-2xl mb-2">🔄</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Question 2</h4>
                    <p class="text-sm text-gray-600 mb-3">Reverse Prompt Builder</p>
                    <div class="text-2xl font-bold {{ $result->question_2_correct ? 'text-green-600' : ($result->question_2_marks > 10 ? 'text-yellow-600' : 'text-red-500') }}">
                        {{ $result->question_2_marks }}/20
                    </div>
                    <div class="mt-2">
                        @if($result->question_2_correct)
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                ✓ Excellent
                            </span>
                        @elseif($result->question_2_marks > 10)
                        <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                ◐ Good Effort
                            </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                ○ Needs Work
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    <div class="text-2xl mb-2">⭐</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Question 3</h4>
                    <p class="text-sm text-gray-600 mb-3">Super Question Builder</p>
                    <div class="text-2xl font-bold {{ $result->question_3_correct ? 'text-green-600' : ($result->question_3_marks > 15 ? 'text-yellow-600' : 'text-red-500') }}">
                        {{ $result->question_3_marks }}/30
                    </div>
                    <div class="mt-2">
                        @if($result->question_3_correct)
                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                ✓ Outstanding
                            </span>
                        @elseif($result->question_3_marks > 15)
                        <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                ◐ Good Progress
                            </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">
                                ○ Keep Practicing
                            </span>
                        @endif
                    </div>
                    @if($result->question_3_topic)
                    <p class="text-xs text-gray-500 mt-2">Topic: {{ ucfirst($result->question_3_topic) }}</p>
                    @endif
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="border-t pt-6">
                <h4 class="font-semibold text-lg text-gray-800 mb-4 text-center">Performance Metrics</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $result->grade }}</div>
                        <div class="text-sm text-gray-600">Final Grade</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-purple-600">{{ $result->formatted_completion_time }}</div>
                        <div class="text-sm text-gray-600">Completion Time</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($result->getProgressPercentage()) }}%</div>
                        <div class="text-sm text-gray-600">Progress</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-orange-600">
                            {{ $result->question_1_completed_at && $result->question_2_completed_at && $result->question_3_completed_at ? '3/3' :
                            (($result->question_1_completed_at ? 1 : 0) + ($result->question_2_completed_at ? 1 : 0) + ($result->question_3_completed_at ? 1 : 0)) . '/3' }}
                        </div>
                        <div class="text-sm text-gray-600">Questions Completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analysis (if available) -->
        @if($result->question_2_analysis || $result->question_3_analysis)
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-200">
            <h4 class="font-semibold text-xl text-gray-800 mb-6">Detailed Analysis</h4>

            @if($result->question_2_analysis)
            <div class="mb-6">
                <h5 class="font-medium text-lg text-gray-700 mb-3">Question 2 Analysis</h5>
                <div class="bg-gray-50 rounded-lg p-4">
                    @php $analysis2 = $result->question_2_analysis; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Analysis Method:</strong> {{ ucfirst(str_replace('_', ' ', $analysis2['method'] ?? 'N/A')) }}
                        </div>
                        <div>
                            <strong>Question Pattern:</strong>
                            <span class="{{ ($analysis2['has_question_pattern'] ?? false) ? 'text-green-600' : 'text-red-500' }}">
                                {{ ($analysis2['has_question_pattern'] ?? false) ? 'Found' : 'Missing' }}
                            </span>
                        </div>
                        @if(isset($analysis2['found_entities']))
                        <div class="md:col-span-2">
                            <strong>Found Entities:</strong> {{ implode(', ', $analysis2['found_entities']) ?: 'None' }}
                        </div>
                        @endif
                        @if(isset($analysis2['found_keywords']))
                        <div class="md:col-span-2">
                            <strong>Found Keywords:</strong> {{ implode(', ', $analysis2['found_keywords']) ?: 'None' }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if($result->question_3_analysis)
            <div>
                <h5 class="font-medium text-lg text-gray-700 mb-3">Question 3 Analysis</h5>
                <div class="bg-gray-50 rounded-lg p-4">
                    @php $analysis3 = $result->question_3_analysis; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Selected Topic:</strong> {{ ucfirst($analysis3['selected_topic'] ?? 'N/A') }}
                        </div>
                        <div>
                            <strong>Topic Included:</strong>
                            <span class="{{ ($analysis3['has_topic'] ?? false) ? 'text-green-600' : 'text-red-500' }}">
                                {{ ($analysis3['has_topic'] ?? false) ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div>
                            <strong>Clarity Score:</strong> {{ $analysis3['clarity_count'] ?? 0 }}/2
                        </div>
                        <div>
                            <strong>Specificity Score:</strong> {{ $analysis3['specificity_count'] ?? 0 }}/1
                        </div>
                        @if(isset($analysis3['found_entities']))
                        <div class="md:col-span-2">
                            <strong>Found Entities:</strong> {{ implode(', ', $analysis3['found_entities']) ?: 'None' }}
                        </div>
                        @endif
                        @if(isset($analysis3['found_topic_keywords']))
                        <div class="md:col-span-2">
                            <strong>Topic Keywords:</strong> {{ implode(', ', $analysis3['found_topic_keywords']) ?: 'None' }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Your Responses -->
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-200">
            <h4 class="font-semibold text-xl text-gray-800 mb-6">Your Responses</h4>

            @if($result->question_2_answer)
            <div class="mb-6">
                <h5 class="font-medium text-lg text-gray-700 mb-2">Question 2 - Your Prompt:</h5>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 italic">"{{ $result->question_2_answer }}"</p>
                </div>
            </div>
            @endif

            @if($result->question_3_answer)
            <div>
                <h5 class="font-medium text-lg text-gray-700 mb-2">Question 3 - Your Super Question:</h5>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700 italic">"{{ $result->question_3_answer }}"</p>
                    @if($result->question_3_topic)
                    <p class="text-xs text-gray-500 mt-2">Topic: {{ ucfirst($result->question_3_topic) }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="text-center">
            <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:inline-flex">
                <a href="{{ route('prompting.restart') }}"
                   class="inline-block w-full sm:w-auto px-8 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 font-medium">
                    Try Again
                </a>
                <button onclick="window.print()"
                        class="inline-block w-full sm:w-auto px-8 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 font-medium">
                    Print Results
                </button>
                <a href="{{ url('/') }}"
                   class="inline-block w-full sm:w-auto px-8 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 font-medium">
                    Back to Home
                </a>
            </div>
        </div>

        <!-- Statistics (Optional) -->
        @if(isset($statistics))
        <div class="mt-12 bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h4 class="font-semibold text-xl text-gray-800 mb-6 text-center">Overall Statistics</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_attempts'] }}</div>
                    <div class="text-sm text-gray-600">Total Attempts</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ number_format($statistics['average_score'], 1) }}%</div>
                    <div class="text-sm text-gray-600">Average Score</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($statistics['highest_score'], 1) }}%</div>
                    <div class="text-sm text-gray-600">Highest Score</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-orange-600">{{ $statistics['completed_attempts'] }}</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@include('layout.footer')

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white !important;
        }
    }
</style>

@endsection
