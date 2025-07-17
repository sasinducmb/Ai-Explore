@extends('layout.app')
@section('title')
{{ __('Explore AI Tools') }}
@endsection

@section('content')
<!-- Background Gradient Layer -->

<!-- Navigation -->
@include('layout.nav')

<section class="px-6 py-12 bg-white">
    <div class="max-w-6xl mx-auto text-center">
        <!-- Heading -->
        <h2 class="text-4xl font-bold poppins text-gray-900 mb-3">Explore AI Tools</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-12 text-base">
            From writing prompts to drawing, math to visuals — discover how AI can boost your ideas and bring learning
            to life in fun and meaningful ways.
        </p>

        <!-- AI Tool Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Quick, Draw! -->
            @php
            $aiTools = [
            [
            'id' => 1,
            'img' => asset('asset/img/explore/1.png'),
            'alt' => 'Prompting',
            'title' => 'Prompting',
            'desc' => 'Use text prompts to interact with AI and spark ideas.',
            ],
            [
            'id'=>2,
            'img' => asset('asset/img/explore/2.png'),
            'alt' => 'Design Tools',
            'title' => 'Design Tools',
            'desc' => 'Create visuals, layouts, and artwork with the help of AI.',
            ],
            [
            'id'=>3,
            'img' => asset('asset/img/explore/4.png'),
            'alt' => 'Funny Activities',
            'title' => 'Funny Activities',
            'desc' => ' Experiment with colors, styles, and beauty powered by AI.',
            ],
            ];
            @endphp

            @foreach ($aiTools as $index => $tool)
            <a href="{{ $tool['id'] === 1 ? route('prompting.show') : '#' }}"
               class="block {{ $tool['id'] === 1 ? 'hover:cursor-pointer' : 'hover:cursor-not-allowed' }}">
                <div
                    class="
                {{ $index === 2 ? 'flex flex-col items-center text-center md:col-span-2' : 'flex items-center' }}
                bg-white shadow-md shadow-blue-100 rounded-xl px-6 py-6 hover:shadow-lg hover:shadow-blue-200
                transition border border-blue-200 transition-all duration-300 ease-in-out
            ">
                    <img src="{{ $tool['img'] }}" alt="{{ $tool['alt'] }}"
                         class="{{ $index === 2 ? 'w-16 h-16 mb-4' : 'w-12 h-12 mr-6' }} transition-all duration-300 ease-in-out object-contain"/>

                    <div class="{{ $index === 2 ? 'text-center' : '' }}">
                        <h3 class="font-semibold text-xl text-gray-800 transition-colors duration-300">
                            {{ $tool['title'] }}
                        </h3>
                        <p class="text-gray-600 transition-colors duration-300">
                            {{ $tool['desc'] }}
                        </p>
                    </div>
                </div>
            </a>
            @endforeach


        </div>
    </div>
</section>


@include('layout.footer')
@endsection
