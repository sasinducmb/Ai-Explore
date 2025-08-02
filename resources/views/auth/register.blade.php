@extends('layout.app')

@section('title')
{{ __('Register') }}
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

        <!-- Register Box -->
        <div class="bg-white rounded-lg">
            <h2 class="text-3xl font-bold nunito text-center mb-2">Create your account</h2>
            <p class="text-gray-600 text-center mb-12">Please fill the form below.</p>

            <!-- Error Messages -->
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-700 rounded-lg">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required />

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required />

                <input type="password" name="password" placeholder="Password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required />

                <input type="password" name="password_confirmation" placeholder="Confirm Password"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required />

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 mt-4 rounded-lg transition-colors">
                    Register
                </button>
            </form>

            <p class="text-sm text-center text-gray-600 mt-6">
                Already have an account?
                <a href="{{ route('login.form') }}" class="text-blue-600 hover:underline">Login here</a>
            </p>
        </div>
    </div>
</div>
@endsection
