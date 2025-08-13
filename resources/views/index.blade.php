@extends('layout.app')
@section('title')
{{ __('Home') }}
@endsection

@section('content')
<!-- Background Gradient Layer -->
<div class="absolute top-0 left-0 w-full h-[800px] -z-10 bg-no-repeat bg-top bg-cover"
     style="background-image: url('{{ asset('asset/img/bg-grad-01.png') }}');">
    <div class="w-full h-full"
         style="background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);">
    </div>
</div>

<!-- Navigation -->
@include('layout.nav')

<!-- Hero Section -->
<section class="">
    <div class="container mx-auto px-4">
        <div class="flex flex-col items-center justify-center w-full">
            <div class="mt-25 mb-10">
                <img src="{{ asset('asset/img/ai-img-01.png') }}" alt="AI Image"
                     class="w-30 sm:w-50 h-auto max-w-xs sm:max-w-lg object-cover"/>
            </div>
            <h1 class="uppercase text-xl sm:text-3xl mb-4">{{ __('Welcome to') }} 👋</h1>
            <h1 class="uppercase ocraext text-5xl sm:text-7xl mb-4">AI explorer</h1>
            <div class="w-4/4 sm:w-2/4 mb-10 px-3 sm:px-0">
                <p class="uppercase text-lg text-center">
                    AI Explorer helps kids aged 8–14 learn about artificial intelligence
                    through interactive tools, voice-guided avatars, and exciting tasks — all
                    with built-in safety, parental controls, and personalized learning just for them.
                </p>
            </div>
            <div>
                <a href="#contactSection"
                   class="inline-block px-8 py-3 rounded-full bg-blue-600 text-white font-semibold text-lg shadow-lg hover:bg-blue-700 hover:scale-105 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Get in Touch
                </a>
            </div>

            <div class="flex flex-col items-center mt-12">
                <a href="#aboutSection" class="flex flex-col items-center group">
                    <div class="gray-600 p-2 transition-colors duration-200">
                        <svg class="w-8 h-8 text-gray-600 group-hover:text-gray-400 transition-colors duration-200"
                             fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0l-5-5m5 5l5-5"/>
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Placeholder Section -->
<section id="aboutSection" class="relative w-full py-40 px-4"
         style="background-image: linear-gradient(to bottom, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 20%, rgba(255,255,255,0) 80%, rgba(255,255,255,0.8) 100%), url('{{ asset('asset/img/bg-grad-02.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center center;">
    <!-- Flower Decoration -->
    <img src="{{ asset('asset/img/yellow-flower-1.png') }}" alt="Flower"
         class="absolute top-5 right-10 sm:right-20 w-15 sm:w-25 h-15 sm:h-25"/>

    <div class="container mx-auto px-5 sm:px-35">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <!-- Text Content -->
            <div class="lg:w-2/3 text-left">
                <h2
                    class="text-4xl lg:text-5xl text-center sm:text-left font-bold tracking-wider mb-6 sm:mb-10 outlined-text nunito uppercase">
                    About Us
                </h2>

                <!-- Only visible on mobile screens -->
                <div class="block lg:hidden mt-0 mb-8 flex justify-center relative">
                    <img src="{{ asset('asset/img/ai-img-02.png') }}" alt="Robot"
                         class="w-[150px] sm:w-[450px] h-auto object-contain"/>
                </div>

                <p class="uppercase text-base text-gray-900 leading-relaxed mb-6 max-w-2xl">
                    We are a passionate student research team from the Faculty of Technology, University of Colombo, on
                    a
                    mission to make artificial intelligence accessible, ethical, and inspiring for the next generation.
                </p>

                <p class="uppercase text-base text-gray-900 leading-relaxed mb-6 max-w-2xl">
                    Our project, <span class="font-bold">AI Explorer</span>, is a web-based learning platform built for
                    children aged 8–14 to discover the world of AI through fun, interactive, and age-appropriate
                    experiences. With features like voice commands, guided avatars, task-based learning, and robust
                    parental
                    controls, we're creating a safe and personalized space where young minds can build a strong
                    foundation
                    in AI literacy.
                </p>

                <p class="uppercase text-base text-gray-900 leading-relaxed max-w-2xl">
                    Our vision is to spark curiosity, creativity, and ethical thinking in children as they explore the
                    technologies shaping their future.
                </p>
            </div>

            <!-- Robot Image -->
            <div
                class="mt-10 lg:mt-0 lg:w-1/3 flex justify-center relative -right-[20px] sm:right-0 bottom-[50px] sm:-bottom-[50px] hidden lg:block">
                <img src="{{ asset('asset/img/ai-img-02.png') }}" alt="Robot"
                     class="w-[150px] sm:w-[450px] h-auto object-contain"/>
            </div>
        </div>
    </div>
</section>

<section class="px-6 py-12 mb-15 mt-10">
    <div class="max-w-4xl mx-auto flex flex-col gap-20 relative">

        <!-- Card 1 -->
        <div class="relative card-hover-trigger">
            <img src="{{ asset('asset/img/ai-img-g-1.png') }}" alt="Robot Reading"
                 class="absolute top-10 sm:-top-10 -left-3 sm:left-4 w-30 sm:w-40 h-auto z-10 robot-float"/>
            <div
                class="flex justify-end bg-gradient-to-r from-white via-blue-100 to-blue-400 rounded-3xl px-5 sm:px-10 py-12 mt-10"
                style="box-shadow:6px 0px 0px 0px #007eff, 0 4px 24px 0 rgba(0,0,0,0.18);">
                <a href="{{ route('learn-ai-tools') }}"
                   class="text-white font-semibold text-lg text-right hover:underline">
                    Learn AI Tools
                </a>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="relative card-hover-trigger">
            <img src="{{ asset('asset/img/ai-img-g-3.png') }}" alt="Robot Searching"
                 class="absolute top-8 sm:-top-8 -right-5 sm:right-4 w-43 sm:w-55 h-auto z-10 robot-bounce"/>
            <div
                class="flex justify-start bg-gradient-to-l from-white via-blue-100 to-blue-400 rounded-xl px-5 sm:px-10 py-12 mt-10"
                style="box-shadow:-6px 0px 0px 0px #007eff, 0 4px 24px 0 rgba(0,0,0,0.18);">
                <a href="{{ route('explore-ai-tools') }}"
                   class="text-white font-semibold text-lg hover:underline">
                    Explore AI Tools
                </a>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="relative card-hover-trigger">
            <img src="{{ asset('asset/img/ai-img-g-4.png') }}" alt="Robot Chatting"
                 class="absolute top-10 sm:-top-10 -left-3 sm:left-4 w-50 sm:w-65 h-auto z-10 robot-wiggle"/>
            <div
                class="flex justify-end bg-gradient-to-r from-white via-blue-100 to-blue-400 rounded-3xl px-5 sm:px-10 py-12 mt-10"
                style="box-shadow:6px 0px 0px 0px #007eff, 0 4px 24px 0 rgba(0,0,0,0.18);">
                <a href="{{ route('gemini.index') }}"
                   class="text-white font-semibold text-lg text-right hover:underline">
                    Chat with AI
                </a>
            </div>
        </div>
    </div>

    </div>
</section>

<section id="contactSection" class="relative w-full pt-20 pb-40 px-4"
         style="background-image: linear-gradient(to bottom, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 20%, rgba(255,255,255,0) 80%, rgba(255,255,255,0.8) 100%), url('{{ asset('asset/img/bg-grad-03.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">

    <!-- Flower Decoration -->
    <img src="{{ asset('asset/img/yellow-flower-1.png') }}" alt="Flower" class="absolute top-5 left-20 w-25 h-25"/>

    <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">

            <!-- Robot Image -->
            <div class="lg:w-1/2 flex justify-center items-end mt-15">
                <img src="{{ asset('asset/img/ai-img-03.png') }}" alt="Robot"
                     class="w-[250px] lg:w-[300px] h-auto object-contain"/>
            </div>

            <!-- Contact Content -->
            <div class="lg:w-1/2 text-left">
                <h2 class="text-4xl text-center sm:text-left lg:text-5xl mb-10 font-bold tracking-wider mb-8 outlined-text nunito uppercase">
                    Contact Us
                </h2>

                <p class="mb-4 text-gray-900 text-base font-medium">
                    🚀 Ready to guide your child into the world of AI? Don’t wait,<br>
                    empower their future today with AI
                    Explorer!
                </p>
                <p class="mb-6 text-gray-900 text-base font-medium">
                    Have questions or want to get started? We're here to help!
                </p>

                <ul class="space-y-4 text-base text-gray-900">
                    <li class="flex items-center space-x-3 font-medium">
                        <span class="text-xl">📞</span>
                        <span>Call us : 011 2 078 607 / 071 4 873 957</span>
                    </li>
                    <li class="flex items-center space-x-3 font-medium">
                        <span class="text-xl">✉️</span>
                        <span>Email us : <a href="mailto:dean@tec.cmb.ac.lk"
                                            class="text-blue-600 hover:underline underline">dean@tec.cmb.ac.lk</a></span>
                    </li>
                    <li class="flex items-center space-x-3 font-medium">
                        <span class="text-xl">🏫</span>
                        <span>Faculty of Technology, University of Colombo</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')
@endsection

<!-- Add custom CSS for animations -->
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-8px) scale(1.05); }
    }

    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-6deg); }
        75% { transform: rotate(6deg); }
    }

    .card-hover-trigger:hover .robot-float {
        animation: float 3s ease-in-out infinite;
    }

    .card-hover-trigger:hover .robot-bounce {
        animation: bounce 2.5s ease-in-out infinite;
    }

    .card-hover-trigger:hover .robot-wiggle {
        animation: wiggle 4s ease-in-out infinite;
    }
</style>

