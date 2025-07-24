@extends('layout.app')
@section('title')
    {{ __('Login') }}
@endsection

@section('content')
    <div class="bg-white min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo & Title -->
            <div class="flex flex-col items-center mb-8">
                <a href="{{ route('home') }}" class="flex flex-col items-center ">
                    <img src="{{ asset('asset/img/logo.png') }}" alt="AI Explorer" class="w-14 h-14 mb-2" />
                    <h1 class="text-lg font-semibold tracking-wide">AI EXPLORER</h1>
                </a>
            </div>

            <!-- Login Box -->
            <div class="bg-white rounded-lg">
                <h2 class="text-3xl font-bold nunito text-center mb-2">Log in to your account</h2>
                <p class="text-gray-600 text-center mb-12">Welcome back! Please enter your details.</p>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required autofocus />

                    <input type="password" name="password" placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        required />

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 mt-4 rounded-lg transition-colors">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection