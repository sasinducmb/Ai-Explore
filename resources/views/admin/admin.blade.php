@extends('layout.app')

@section('title')
{{ __('Admin Dashboard') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-6 py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
            <p class="text-gray-600">Monitor and analyze user performance across AI tools</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Design Results</p>
                        <p class="text-3xl font-bold text-blue-600">
                            @if(isset($designResults) && method_exists($designResults, 'total'))
                                {{ $designResults->total() }}
                            @elseif(isset($designResults))
                                {{ $designResults->count() }}
                            @else
                                0
                            @endif
                        </p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Prompting Results</p>
                        <p class="text-3xl font-bold text-green-600">
                            @if(isset($promptingResults) && method_exists($promptingResults, 'total'))
                                {{ $promptingResults->total() }}
                            @elseif(isset($promptingResults))
                                {{ $promptingResults->count() }}
                            @else
                                0
                            @endif
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-2.647-.398l-2.829 2.829A1 1 0 015 21.414V19a8 8 0 1116-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Design Score</p>
                        <p class="text-3xl font-bold text-purple-600">
                            @php
                                $designAvg = 0;
                                if (isset($designResults) && $designResults->count() > 0) {
                                    if (method_exists($designResults, 'getCollection')) {
                                        // It's a paginator
                                        $designAvg = $designResults->getCollection()->avg('percentage');
                                    } else {
                                        // It's a collection
                                        $designAvg = $designResults->avg('percentage');
                                    }
                                }
                            @endphp
                            {{ number_format($designAvg, 1) }}%
                        </p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Prompting Score</p>
                        <p class="text-3xl font-bold text-orange-600">
                            @php
                                $promptingAvg = 0;
                                if (isset($promptingResults) && $promptingResults->count() > 0) {
                                    if (method_exists($promptingResults, 'getCollection')) {
                                        // It's a paginator
                                        $promptingAvg = $promptingResults->getCollection()->avg('percentage');
                                    } else {
                                        // It's a collection
                                        $promptingAvg = $promptingResults->avg('percentage');
                                    }
                                }
                            @endphp
                            {{ number_format($promptingAvg, 1) }}%
                        </p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('design')" id="design-tab"
                            class="tab-button active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                        Design Results
                    </button>
                    <button onclick="showTab('prompting')" id="prompting-tab"
                            class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Prompting Results
                    </button>
                </nav>
            </div>
        </div>

        <!-- Design Results Table -->
        <div id="design-content" class="tab-content">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Design Tools Results</h3>
                    <p class="text-sm text-gray-600">User performance in design challenges</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Session ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Percentage
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grade
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Completion Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($designResults))
                                @forelse($designResults as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ Str::limit($result->session_id, 20) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->name ?? 'Anonymous' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-semibold">{{ $result->total_marks }}</span>
                                        <span class="text-gray-500">/{{ $result->total_possible_marks }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $result->percentage }}%"></div>
                                            </div>
                                            <span class="font-medium">{{ number_format($result->percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                        $gradeColors = [
                                            'A' => 'bg-green-100 text-green-800',
                                            'B' => 'bg-blue-100 text-blue-800',
                                            'C' => 'bg-yellow-100 text-yellow-800',
                                            'D' => 'bg-orange-100 text-orange-800',
                                            'F' => 'bg-red-100 text-red-800'
                                        ];
                                        $gradeColor = $gradeColors[$result->grade] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $gradeColor }}">
                                            {{ $result->grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($result->completion_time_seconds)
                                            {{ gmdate('H:i:s', $result->completion_time_seconds) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($result->completed)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($result->created_at)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewDesignDetails('{{ $result->id }}')"
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        No design results found
                                    </td>
                                </tr>
                                @endforelse
                            @else
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    Design results not available
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Design Results -->
                @if(isset($designResults) && $designResults->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $designResults->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Prompting Results Table -->
        <div id="prompting-content" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Prompting Tools Results</h3>
                    <p class="text-sm text-gray-600">User performance in prompting challenges</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Session ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Percentage
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grade
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Completion Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if(isset($promptingResults))
                                @forelse($promptingResults as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                        {{ Str::limit($result->session_id, 20) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->name ?? 'Anonymous' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-semibold">{{ $result->total_marks }}</span>
                                        <span class="text-gray-500">/{{ $result->total_possible_marks }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $result->percentage }}%"></div>
                                            </div>
                                            <span class="font-medium">{{ number_format($result->percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                        $gradeColors = [
                                            'A' => 'bg-green-100 text-green-800',
                                            'B' => 'bg-blue-100 text-blue-800',
                                            'C' => 'bg-yellow-100 text-yellow-800',
                                            'D' => 'bg-orange-100 text-orange-800',
                                            'F' => 'bg-red-100 text-red-800'
                                        ];
                                        $gradeColor = $gradeColors[$result->grade] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $gradeColor }}">
                                            {{ $result->grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($result->completion_time_seconds)
                                            {{ gmdate('H:i:s', $result->completion_time_seconds) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($result->completed)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewPromptingDetails('{{ $result->id }}')"
                                                class="text-green-600 hover:text-green-900 mr-3">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        No prompting results found
                                    </td>
                                </tr>
                                @endforelse
                            @else
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                    Prompting results not available
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Prompting Results -->
                @if(isset($promptingResults) && $promptingResults->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $promptingResults->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">User Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modal-content" class="max-h-96 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@include('layout.footer')

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

function viewDesignDetails(resultId) {
    // Show loading in modal
    document.getElementById('modal-title').textContent = 'Design Result Details';
    document.getElementById('modal-content').innerHTML = '<div class="text-center py-4">Loading...</div>';
    document.getElementById('detail-modal').classList.remove('hidden');

    // Fetch details via AJAX
    fetch(`/admin/design-results/${resultId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modal-content').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('modal-content').innerHTML = '<div class="text-center py-4 text-red-600">Error loading details</div>';
        });
}

function viewPromptingDetails(resultId) {
    // Show loading in modal
    document.getElementById('modal-title').textContent = 'Prompting Result Details';
    document.getElementById('modal-content').innerHTML = '<div class="text-center py-4">Loading...</div>';
    document.getElementById('detail-modal').classList.remove('hidden');

    // Fetch details via AJAX
    fetch(`/admin/prompting-results/${resultId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modal-content').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('modal-content').innerHTML = '<div class="text-center py-4 text-red-600">Error loading details</div>';
        });
}

function closeModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detail-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<style>
.tab-button.active {
    border-bottom-color: #3b82f6;
    color: #3b82f6;
}
</style>

@endsection
