@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md hidden md:block">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Admin Menu</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-blue-600 font-medium bg-gray-100 rounded-lg p-2">
                    Dashboard
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium p-2">
                    Manage Users
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium p-2">
                    Tool Analytics
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium p-2">
                    Reports
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Navbar -->
        <header class="bg-white shadow p-4 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Welcome, Admin</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                    Logout
                </button>
            </form>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6 bg-gray-100 flex-grow overflow-auto">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Prompting Results</h2>
                @if ($results->isEmpty())
                <p class="text-gray-600">No prompting results found.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Marks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($results as $result)
                        <tr class="{{ $result->completed ? '' : 'bg-yellow-50' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->total_marks ?? 0 }}/100</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($result->percentage, 2) }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->grade ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->formatted_completion_time ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.results.show', $result->session_id) }}" class="text-blue-600 hover:text-blue-800">View Details</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $results->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection
