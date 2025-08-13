<div class="space-y-6">
    <!-- Basic Info -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Basic Information</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700">Session ID:</span>
                <span class="text-gray-900 font-mono">{{ $result->session_id }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">User:</span>
                <span class="text-gray-900">{{ $result->name ?? 'Anonymous' }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Total Score:</span>
                <span class="text-gray-900 font-semibold">{{ $result->total_marks }}/{{ $result->total_possible_marks }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Percentage:</span>
                <span class="text-gray-900 font-semibold">{{ number_format($result->percentage, 1) }}%</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Grade:</span>
                <span class="text-gray-900 font-semibold">{{ $result->grade ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Completion Time:</span>
                <span class="text-gray-900">
                    @if($result->completion_time_seconds)
                        {{ gmdate('H:i:s', $result->completion_time_seconds) }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Questions Breakdown -->
    <div class="bg-white border rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 mb-3">Questions Breakdown</h4>
        <div class="space-y-4">
            @for($i = 1; $i <= 5; $i++)
                @php
                    $answer = $result->{"question_{$i}_answer"};
                    $correct = $result->{"question_{$i}_correct"};
                    $marks = $result->{"question_{$i}_marks"};
                    $analysis = $result->{"question_{$i}_analysis"};
                    $completedAt = $result->{"question_{$i}_completed_at"};

                    $questionTitles = [
                        1 => 'MCQ: Google AI Tool',
                        2 => 'Weather Prompt Improvement',
                        3 => 'Recipe Prompt Challenge',
                        4 => 'Advanced Computer Prompt',
                        5 => 'Role-Play Prompting'
                    ];
                @endphp

                <div class="border-l-4 {{ $correct ? 'border-green-500 bg-green-50' : ($answer ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 bg-gray-50') }} p-3">
                    <div class="flex justify-between items-start mb-2">
                        <h5 class="font-medium text-gray-900">Question {{ $i }}: {{ $questionTitles[$i] }}</h5>
                        <div class="flex items-center space-x-2">
                            @if($marks)
                                <span class="text-sm font-semibold {{ $correct ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $marks }}/6 marks
                                </span>
                            @endif
                            @if($correct)
                                <span class="text-green-600">✓</span>
                            @elseif($answer)
                                <span class="text-yellow-600">△</span>
                            @else
                                <span class="text-gray-400">○</span>
                            @endif
                        </div>
                    </div>

                    @if($answer)
                        <div class="text-sm text-gray-700 mb-2">
                            <span class="font-medium">Answer:</span>
                            <div class="mt-1 p-2 bg-white rounded border">
                                {{ $answer }}
                            </div>
                        </div>
                    @endif

                    @if($analysis)
                        <div class="text-sm text-gray-700 mb-2">
                            <span class="font-medium">Analysis:</span>
                            <div class="mt-1 p-2 bg-blue-50 rounded border">
                                @if(is_string($analysis))
                                    {{ $analysis }}
                                @else
                                    @php $analysisData = json_decode($analysis, true); @endphp
                                    @if($analysisData)
                                        @foreach($analysisData as $key => $value)
                                            @if(is_array($value))
                                                <div><span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ implode(', ', $value) }}</div>
                                            @else
                                                <div><span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</div>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($completedAt)
                        <div class="text-xs text-gray-500">
                            Completed: {{ \Carbon\Carbon::parse($completedAt)->format('M d, Y H:i:s') }}
                        </div>
                    @elseif(!$answer)
                        <div class="text-xs text-gray-400">Not attempted</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Metadata -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Metadata</h4>
        <div class="grid grid-cols-1 gap-2 text-sm">
            <div>
                <span class="font-medium text-gray-700">IP Address:</span>
                <span class="text-gray-900">{{ $result->ip_address ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">User Agent:</span>
                <span class="text-gray-900 text-xs">{{ Str::limit($result->user_agent ?? 'N/A', 80) }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Created:</span>
                <span class="text-gray-900">{{ $result->created_at->format('M d, Y H:i:s') }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Last Updated:</span>
                <span class="text-gray-900">{{ $result->updated_at->format('M d, Y H:i:s') }}</span>
            </div>
        </div>
    </div>
</div>
