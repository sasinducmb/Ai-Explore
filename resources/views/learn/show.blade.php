@extends('layout.app')

@section('title')
{{ $tool['title'] }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-6 py-12 bg-white">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">{{ $tool['title'] }}</h1>
            <p class="text-blue-600 mt-2 text-lg">{{ $tool['subtitle'] }}</p>
        </div>

        <p class="text-gray-700 text-base leading-relaxed text-justify">
            {{ $tool['description'] }}
        </p>

        @if (!empty($tool['youtubeUrl']))
        <div class="mt-8">
            <div class="aspect-w-16 aspect-h-9">
                <iframe
                    class="w-full h-80 md:h-[450px] rounded-lg shadow"
                    src="{{ str_replace('watch?v=', 'embed/', $tool['youtubeUrl']) }}"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ url()->previous() }}" class="text-blue-500 hover:underline">← Back to AI Tools</a>
        </div>
    </div>
</section>

@include('layout.footer')
@endsection