@extends('layout.app')

@section('title')
{{ __('Learn AI Tools') }}
@endsection

@section('content')

@include('layout.nav')

<section class="px-6 py-12 bg-white">
    <div class="max-w-6xl mx-auto text-center">
        <h2 class="text-4xl font-bold poppins text-gray-900 mb-3">Learn AI Tools</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-12 text-base">
            From drawing and music to chatting and storytelling, explore fun, interactive AI experiences that spark
            creativity, curiosity, and learning — all in a safe, easy-to-use space designed for young minds.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            @php
            $aiTools = [
            [
            'id' => 1,
            'img' => asset('asset/img/learn/1.png'),
            'icon' => '✍️',
            'title' => 'Quick, Draw!',
            'subtitle' => 'A fun game where AI guesses your doodles in real-time',
            'description' => 'Dive into an engaging and entertaining interactive drawing game where an advanced artificial intelligence system watches your every stroke and attempts to identify the objects or scenes you are sketching in real-time.',
            'youtubeUrl' => 'https://youtu.be/X8v1GWzZYJ4?si=ywiWbOOhpR3GeZDz',
            ],
            [
            'id' => 2,
            'img' => asset('asset/img/learn/2.png'),
            'icon' => '🌊',
            'title' => 'DeepSeek',
            'subtitle' => 'An AI tool that finds answers from large knowledge sources',
            'description' => 'Harness the power of an advanced AI-driven search tool designed to scour vast knowledge bases and deliver precise, comprehensive answers to your queries.',
            'youtubeUrl' => 'https://youtu.be/_8tcA9-14JQ?si=nJpoj1SvwLcd_6-d',
            ],
            [
            'id' => 3,
            'img' => asset('asset/img/learn/3.png'),
            'icon' => '📖',
            'title' => 'Storybird AI',
            'subtitle' => 'Helps you turn ideas into creative illustrated stories using AI',
            'description' => 'Unleash your storytelling potential with Storybird AI, a platform that transforms your imaginative ideas into beautifully illustrated stories using AI.',
            'youtubeUrl' => 'https://youtu.be/FpE6z2AOK-I?si=A1FsOkmV6O7AOd4Y',
            ],
            [
            'id' => 4,
            'img' => asset('asset/img/learn/4.png'),
            'icon' => '💬',
            'title' => 'ChatGPT',
            'subtitle' => 'A smart chat that understands and responds to your messages',
            'description' => 'Experience the future of conversation with ChatGPT, an intelligent chatbot powered by advanced AI that comprehends and responds to your messages.',
            'youtubeUrl' => 'https://youtu.be/Gaf_jCnA6mc?si=KJ7YIoEdmSyR1OT-',
            ],
            [
            'id' => 5,
            'img' => asset('asset/img/learn/5.png'),
            'icon' => '🎶',
            'title' => 'AI Duet',
            'subtitle' => 'Play a melody and an AI responds with its own musical notes',
            'description' => 'Embark on a musical journey with AI Duet where you play a melody and a sophisticated AI responds in real-time with its own notes.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_aiduet_video',
            ],
            [
            'id' => 6,
            'img' => asset('asset/img/learn/6.png'),
            'icon' => '🎨',
            'title' => 'Scribble Diffusion',
            'subtitle' => 'Transforms your rough sketches into refined images using AI',
            'description' => 'Elevate your artistic skills with Scribble Diffusion, an AI tool that transforms your sketches into polished, refined images.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_scribblediffusion_video',
            ],
            [
            'id' => 7,
            'img' => asset('asset/img/learn/7.png'),
            'icon' => '📊',
            'title' => 'Tome AI',
            'subtitle' => 'Creates structured presentation slides instantly from your input',
            'description' => 'Revolutionize presentation creation with Tome AI, which transforms your ideas into structured and visually appealing slides.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_tomeai_video',
            ],
            [
            'id' => 8,
            'img' => asset('asset/img/learn/8.png'),
            'icon' => '✏️',
            'title' => 'AutoDraw',
            'subtitle' => 'Suggests polished drawings based on your quick sketches',
            'description' => 'AutoDraw analyzes your quick sketches and offers polished versions in real-time.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_autodraw_video',
            ],
            [
            'id' => 9,
            'img' => asset('asset/img/learn/8.png'),
            'icon' => '🖌️',
            'title' => 'Figma',
            'subtitle' => 'A collaborative design tool for UI, UX, and prototyping',
            'description' => 'Figma is a collaborative design tool with AI-enhanced features for crafting digital interfaces.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_figma_video',
            ],
            [
            'id' => 10,
            'img' => asset('asset/img/learn/10.png'),
            'icon' => '🖼️',
            'title' => 'Midjourney',
            'subtitle' => 'An AI tool that creates artistic images from text prompts',
            'description' => 'Midjourney translates your text prompts into breathtaking artistic visuals using powerful AI.',
            'youtubeUrl' => 'https://www.youtube.com/watch?v=your_midjourney_video',
            ],
            ];
            @endphp

            @foreach ($aiTools as $tool)
            <a href="{{ route('ai-tools.show', ['id' => $tool['id']]) }}" class="block">
                <div class="flex bg-white shadow-md shadow-blue-100 rounded-xl px-6 py-6 hover:shadow-lg hover:shadow-blue-200 transition border border-blue-200">
                    <img src="{{ $tool['img'] }}" alt="{{ $tool['title'] }}" class="w-14 h-14 rounded-full mr-4 object-contain" />
                    <div class="text-left">
                        <h3 class="font-semibold text-xl text-gray-800 flex items-center">
                            <span class="text-2xl"></span>{{ $tool['title'] }}
                        </h3>
                        <p class="text-sm text-blue-600 mt-1 font-medium">{{ $tool['subtitle'] }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

@include('layout.footer')
@endsection
