@extends('layout.app')

@section('title')
{{ __('Funny Activities') }}
@endsection

@section('content')
@include('layout.nav')

<section class="px-4 sm:px-6 lg:px-8 py-12 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto text-center">
        <h2 class="text-3xl sm:text-4xl font-bold poppins text-gray-900 mb-4">Funny Activities</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-8 sm:mb-12 text-sm sm:text-base">
            Experiment with colors, styles, and beauty powered by AI. Have fun with creative activities that make learning enjoyable and engaging!
        </p>

        <div class="bg-white shadow-md shadow-blue-100 rounded-xl px-4 sm:px-6 py-6 border border-blue-200">
            <h3 class="font-semibold text-lg sm:text-xl text-gray-800 mb-4">AI-Powered Fun Activities</h3>
            <p class="text-gray-600 mb-6 text-sm sm:text-base">
                Choose from our collection of fun activities that combine creativity with AI technology.
            </p>

            <!-- Activities Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Color Palette Generator -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-lg border border-purple-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">🎨</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Color Palette Fun</h4>
                    <p class="text-gray-600 text-sm mb-4">Generate beautiful color combinations and learn about color theory with AI!</p>
                    <button class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition duration-300 text-sm font-medium" onclick="startColorActivity()">
                        Start Creating
                    </button>
                </div>

                <!-- Story Generator -->
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-lg border border-blue-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">📚</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Story Creator</h4>
                    <p class="text-gray-600 text-sm mb-4">Create funny and amazing stories with the help of AI imagination!</p>
                    <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300 text-sm font-medium" onclick="startStoryActivity()">
                        Create Story
                    </button>
                </div>

                <!-- Joke Generator -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-lg border border-yellow-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">😄</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Joke Machine</h4>
                    <p class="text-gray-600 text-sm mb-4">Generate hilarious jokes and riddles that will make everyone laugh!</p>
                    <button class="w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-300 text-sm font-medium" onclick="startJokeActivity()">
                        Get Jokes
                    </button>
                </div>

                <!-- Drawing Assistant -->
                <div class="bg-gradient-to-br from-green-50 to-teal-50 p-6 rounded-lg border border-green-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">✏️</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Drawing Helper</h4>
                    <p class="text-gray-600 text-sm mb-4">Get AI suggestions to improve your drawings and learn new techniques!</p>
                    <button class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300 text-sm font-medium" onclick="startDrawingActivity()">
                        Start Drawing
                    </button>
                </div>

                <!-- Music Maker -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 p-6 rounded-lg border border-red-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">🎵</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Music Creator</h4>
                    <p class="text-gray-600 text-sm mb-4">Compose simple melodies and learn about music with AI assistance!</p>
                    <button class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300 text-sm font-medium" onclick="startMusicActivity()">
                        Make Music
                    </button>
                </div>

                <!-- Word Games -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-6 rounded-lg border border-indigo-200 hover:shadow-md transition duration-300">
                    <div class="text-4xl mb-4">🎯</div>
                    <h4 class="font-semibold text-lg text-gray-800 mb-2">Word Games</h4>
                    <p class="text-gray-600 text-sm mb-4">Play fun word games and puzzles powered by AI to expand your vocabulary!</p>
                    <button class="w-full px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition duration-300 text-sm font-medium" onclick="startWordActivity()">
                        Play Games
                    </button>
                </div>
            </div>

            <!-- Activity Display Area -->
            <div id="activity-area" class="hidden bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h4 id="activity-title" class="font-semibold text-lg text-gray-800"></h4>
                    <button onclick="closeActivity()" class="text-gray-500 hover:text-gray-700 transition duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="activity-content" class="text-center">
                    <!-- Activity content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')

<script>
    function startColorActivity() {
        showActivity('Color Palette Fun', `
            <div class="space-y-4">
                <p class="text-gray-600">Click the button below to generate a beautiful color palette!</p>
                <button onclick="generateColors()" class="px-6 py-3 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition duration-300">
                    Generate Colors
                </button>
                <div id="color-palette" class="grid grid-cols-5 gap-2 mt-4"></div>
            </div>
        `);
    }

    function startStoryActivity() {
        showActivity('Story Creator', `
            <div class="space-y-4">
                <p class="text-gray-600">Enter a character or theme to create an amazing story!</p>
                <input type="text" id="story-input" placeholder="Enter a character (e.g., brave princess, funny robot)" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button onclick="generateStory()" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                    Create Story
                </button>
                <div id="story-output" class="text-left bg-white p-4 rounded-lg border hidden"></div>
            </div>
        `);
    }

    function startJokeActivity() {
        showActivity('Joke Machine', `
            <div class="space-y-4">
                <p class="text-gray-600">Get ready to laugh with AI-generated jokes!</p>
                <button onclick="generateJoke()" class="px-6 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition duration-300">
                    Tell Me a Joke
                </button>
                <div id="joke-output" class="bg-white p-4 rounded-lg border hidden"></div>
            </div>
        `);
    }

    function startDrawingActivity() {
        showActivity('Drawing Helper', `
            <div class="space-y-4">
                <p class="text-gray-600">Describe what you want to draw and get helpful tips!</p>
                <input type="text" id="drawing-input" placeholder="What do you want to draw? (e.g., cat, house, tree)" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                <button onclick="getDrawingTips()" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">
                    Get Tips
                </button>
                <div id="drawing-output" class="text-left bg-white p-4 rounded-lg border hidden"></div>
            </div>
        `);
    }

    function startMusicActivity() {
        showActivity('Music Creator', `
            <div class="space-y-4">
                <p class="text-gray-600">Choose a mood and create a simple melody!</p>
                <select id="mood-select" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Select a mood</option>
                    <option value="happy">Happy</option>
                    <option value="sad">Sad</option>
                    <option value="energetic">Energetic</option>
                    <option value="calm">Calm</option>
                </select>
                <button onclick="createMelody()" class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-300">
                    Create Melody
                </button>
                <div id="music-output" class="bg-white p-4 rounded-lg border hidden"></div>
            </div>
        `);
    }

    function startWordActivity() {
        showActivity('Word Games', `
            <div class="space-y-4">
                <p class="text-gray-600">Play word association and vocabulary games!</p>
                <button onclick="startWordGame()" class="px-6 py-3 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition duration-300">
                    Start Game
                </button>
                <div id="word-output" class="bg-white p-4 rounded-lg border hidden"></div>
            </div>
        `);
    }

    function showActivity(title, content) {
        document.getElementById('activity-title').textContent = title;
        document.getElementById('activity-content').innerHTML = content;
        document.getElementById('activity-area').classList.remove('hidden');
        document.getElementById('activity-area').scrollIntoView({ behavior: 'smooth' });
    }

    function closeActivity() {
        document.getElementById('activity-area').classList.add('hidden');
    }

    function generateColors() {
        const palette = document.getElementById('color-palette');
        palette.innerHTML = '';
        const colors = [];

        for (let i = 0; i < 5; i++) {
            const hue = Math.floor(Math.random() * 360);
            const saturation = 70 + Math.floor(Math.random() * 30);
            const lightness = 50 + Math.floor(Math.random() * 30);
            const color = `hsl(${hue}, ${saturation}%, ${lightness}%)`;
            colors.push(color);

            const colorDiv = document.createElement('div');
            colorDiv.style.backgroundColor = color;
            colorDiv.style.height = '60px';
            colorDiv.className = 'rounded-lg shadow-sm border';
            colorDiv.title = color;
            palette.appendChild(colorDiv);
        }
    }

    function generateStory() {
        const input = document.getElementById('story-input').value;
        const output = document.getElementById('story-output');

        if (!input.trim()) {
            alert('Please enter a character or theme!');
            return;
        }

        const stories = [
            `Once upon a time, there was a ${input} who discovered a magical portal in their backyard. This portal led to a world where everything was made of candy! The ${input} had amazing adventures, making friends with gummy bear guardians and solving puzzles made of chocolate.`,
            `In a faraway kingdom, a brave ${input} was chosen to find the lost rainbow crystal. Along the journey, they met talking animals who taught them about friendship and courage. Together, they saved the kingdom and brought color back to the world!`,
            `The ${input} woke up one morning to find they could speak to computers! They used this special power to help solve problems around the world, making technology more fun and helpful for everyone.`
        ];

        const randomStory = stories[Math.floor(Math.random() * stories.length)];
        output.textContent = randomStory;
        output.classList.remove('hidden');
    }

    function generateJoke() {
        const output = document.getElementById('joke-output');

        const jokes = [
            "Why don't robots ever panic? Because they have great algorithms for staying calm! 🤖",
            "What do you call a computer that sings? A-dell! 🎵",
            "Why did the AI go to school? To improve its learning algorithms! 📚",
            "What's a computer's favorite snack? Chips! 🍟",
            "Why don't programmers like nature? It has too many bugs! 🐛"
        ];

        const randomJoke = jokes[Math.floor(Math.random() * jokes.length)];
        output.textContent = randomJoke;
        output.classList.remove('hidden');
    }

    function getDrawingTips() {
        const input = document.getElementById('drawing-input').value;
        const output = document.getElementById('drawing-output');

        if (!input.trim()) {
            alert('Please enter what you want to draw!');
            return;
        }

        const tips = {
            'cat': 'Start with a circle for the head, add triangle ears, draw oval eyes, and don\'t forget the whiskers! Cats love to stretch, so make the body long and graceful.',
            'house': 'Begin with a square or rectangle for the main structure, add a triangle roof, draw windows and a door. Add details like a chimney or garden to make it special!',
            'tree': 'Draw a vertical line for the trunk, add branches that get smaller as they go up, and create a cloud-like shape for the leaves. Remember, no two trees look exactly the same!',
            'default': `For drawing a ${input}, start with basic shapes like circles, squares, and triangles. Break down what you see into simple parts, then add details step by step. Practice makes perfect!`
        };

        const tip = tips[input.toLowerCase()] || tips['default'];
        output.textContent = tip;
        output.classList.remove('hidden');
    }

    function createMelody() {
        const mood = document.getElementById('mood-select').value;
        const output = document.getElementById('music-output');

        if (!mood) {
            alert('Please select a mood!');
            return;
        }

        const melodies = {
            'happy': 'Your melody: Do-Re-Mi-Fa-Sol! 🎵 This creates a cheerful, upward-moving tune that makes people smile!',
            'sad': 'Your melody: Sol-Fa-Mi-Re-Do 🎶 This descending pattern creates a gentle, thoughtful feeling.',
            'energetic': 'Your melody: Do-Mi-Sol-Do-Sol-Mi-Do! ⚡ This jumping pattern creates excitement and energy!',
            'calm': 'Your melody: Do-Mi-Re-Fa-Mi-Re-Do 🌙 This gentle wave pattern is soothing and peaceful.'
        };

        output.textContent = melodies[mood];
        output.classList.remove('hidden');
    }

    function startWordGame() {
        const output = document.getElementById('word-output');

        const words = ['ROBOT', 'CREATIVITY', 'ADVENTURE', 'FRIENDSHIP', 'DISCOVERY'];
        const randomWord = words[Math.floor(Math.random() * words.length)];
        const scrambled = randomWord.split('').sort(() => Math.random() - 0.5).join('');

        output.innerHTML = `
            <div class="space-y-4">
                <h5 class="font-semibold">Word Scramble Challenge!</h5>
                <p>Unscramble this word: <span class="font-bold text-lg text-indigo-600">${scrambled}</span></p>
                <input type="text" id="word-guess" placeholder="Your answer" class="w-full p-2 border rounded">
                <button onclick="checkWord('${randomWord}')" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 transition duration-300">
                    Check Answer
                </button>
                <div id="word-result"></div>
            </div>
        `;
        output.classList.remove('hidden');
    }

    function checkWord(correctWord) {
        const guess = document.getElementById('word-guess').value.toUpperCase();
        const result = document.getElementById('word-result');

        if (guess === correctWord) {
            result.innerHTML = '<p class="text-green-600 font-semibold">🎉 Correct! Great job!</p>';
        } else {
            result.innerHTML = `<p class="text-orange-600">Not quite! The answer was: <span class="font-semibold">${correctWord}</span>. Try another word!</p>`;
        }
    }
</script>

@endsection
