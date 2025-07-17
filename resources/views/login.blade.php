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

                <!-- Form -->
                <form action="#" method="POST" class="space-y-4">
                    <input type="email" placeholder="Enter your email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

                    <input type="password" placeholder="••••••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 mt-4 rounded-lg transition-colors">
                        Sign in
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-4">
                    <button
                        class="w-full flex items-center justify-center border border-gray-300 rounded-lg py-3 hover:bg-gray-100 transition-colors">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/800px-Google_%22G%22_logo.svg.png"
                            alt="Google logo" class="w-5 h-5 mr-2" />
                        <span class="text-sm font-medium text-gray-800">Sign in with Google</span>
                    </button>
                </div>

                <!-- Footer Links -->
                <div class="text-center text-sm text-gray-700 mt-6">
                    <p>
                        Don’t have an account?
                        <a href="#" class="text-blue-600 hover:underline">Sign up</a>
                    </p>
                    <p class="mt-2">
                        <a href="#" class="text-blue-600 hover:underline">Forget password</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
