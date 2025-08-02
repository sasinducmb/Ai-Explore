@extends('layout.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md hidden md:block">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Admin Menu</h2>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-blue-600 font-medium">
                    Dashboard
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium">
                    Manage Users
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium">
                    Tool Analytics
                </a>
                <a href="#" class="block text-gray-700 hover:text-blue-600 font-medium">
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
        <main class="p-6 bg-gray-100 flex-grow">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">User Management</h2>
                    <p class="text-gray-600">Manage registered users, assign roles, and control access.</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">AI Tool Analytics</h2>
                    <p class="text-gray-600">View usage statistics and user activity.</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Content Moderation</h2>
                    <p class="text-gray-600">Review and moderate user-generated content.</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">System Settings</h2>
                    <p class="text-gray-600">Configure system preferences and application settings.</p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
