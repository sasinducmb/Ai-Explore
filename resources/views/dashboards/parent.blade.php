@extends('layout.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="flex min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md hidden md:block">
        <div class="p-6 border-b">
            <h1 class="text-2xl font-bold text-blue-600">Parent Panel</h1>
        </div>
        <nav class="mt-6">
            <a href="{{ route('parent.dashboard') }}" class="block px-6 py-3 text-gray-700 hover:bg-blue-100">Dashboard</a>
            <a href="{{ route('home') }}" class="block px-6 py-3 text-gray-700 hover:bg-blue-100">Home</a>
            <a href="{{ route('explore-ai-tools') }}" class="block px-6 py-3 text-gray-700 hover:bg-blue-100">Explore AI Tools</a>
            <a href="{{ route('learn-ai-tools') }}" class="block px-6 py-3 text-gray-700 hover:bg-blue-100">Learn AI Tools</a>
            <a href="{{ route('chat-with-ai') }}" class="block px-6 py-3 text-gray-700 hover:bg-blue-100">Chat With AI</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Nav -->
        <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700 hidden sm:inline">👋 {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6 flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">AI Learning Tools</h3>
                    <p class="text-gray-600">Explore and use AI tools to support your child’s education.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Progress Reports</h3>
                    <p class="text-gray-600">Track your child’s engagement and learning progress.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Tips for Parents</h3>
                    <p class="text-gray-600">Stay involved and encourage your child to use the tools regularly.</p>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
