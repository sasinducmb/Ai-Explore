<nav id="main-navbar" class="bg-transparent border-b-2 border-gray-100 transition-all duration-300">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse" style="min-width: 300px">
            <img src="{{ asset('asset/img/logo.png') }}" class="h-8" alt="AI Explorer Logo" />
        </a>

        @php
        $locale = Cookie::get('locale', config('app.locale'));
        $languages = [
        'en' => ['name' => 'English (US)', 'flag' => 'us'],
        'si' => ['name' => 'සිංහල', 'flag' => 'lk'],
        'ta' => ['name' => 'தமிழ்', 'flag' => 'lk'],
        ];
        $currentLang = $languages[$locale] ?? $languages['en'];
        @endphp

        <div class="flex items-center md:order-2 space-x-1 md:space-x-0 rtl:space-x-reverse">
            {{-- Language Switcher --}}
            <button type="button" data-dropdown-toggle="language-dropdown-menu"
                    class="hidden md:inline-flex items-center font-medium justify-center px-4 py-2 text-sm text-gray-900 rounded-lg cursor-pointer">
                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.3.2/flags/4x3/{{ $currentLang['flag'] }}.svg"
                     alt="{{ $currentLang['name'] }}" class="w-5 h-5 rounded-full me-3 inline object-fit-cover" />
                {{ $currentLang['name'] }}
            </button>

            {{-- Dropdown --}}
            <div class="z-50 hidden my-4 text-base list-none bg-gray-100 divide-y divide-gray-100 rounded-lg shadow-sm"
                 id="language-dropdown-menu">
                <ul class="py-2 font-medium" role="none">
                    @foreach ($languages as $code => $lang)
                    <li>
                        <a href="{{ route('lang.switch', $code) }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-300" role="menuitem">
                            <div class="inline-flex items-center">
                                <img src="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.3.2/flags/4x3/{{ $lang['flag'] }}.svg"
                                     class="h-3.5 w-3.5 rounded-full me-2 inline object-fit-cover" />
                                {{ $lang['name'] }}
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            @auth
            @php
            $dashboardRoute = auth()->user()->role === 'ADMIN' ? route('admin.dashboard') : route('parent.dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}"
               class="hidden md:inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition mr-2">
                Dashboard
            </a>

            <form action="{{ route('logout') }}" method="POST" class="hidden md:inline-block ml-2">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300">
                    Logout
                </button>
            </form>
            @endauth

            @guest
            <a href="{{ route('login.form') }}"
               class="ml-2 text-gray-900 hover:text-blue-700 font-medium transition-colors duration-200 hidden md:inline">Login</a>
            <a href="{{ route('register.form') }}"
               class="ml-4 hidden md:inline-flex items-center px-5 py-2 rounded-full bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-300 text-base">
                Sign Up Free
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            @endguest

            {{-- Hamburger Menu --}}
            <button data-collapse-toggle="navbar-language" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="navbar-language" aria-expanded="false"
                    style="position: absolute; right: 10px;">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>

        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-language">
            <ul
                class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0">
                <li>
                    <a href="{{ route('home') }}"
                       class="block py-2 px-3 rounded-sm {{ Request::routeIs('home') ? 'text-blue-700' : 'text-gray-900' }} md:p-0 fw-500">Home</a>
                </li>
                <li>
                    <a href="{{ route('home') }}#aboutSection"
                       class="block py-2 px-3 text-gray-900 hover:text-blue-700 transition-colors duration-200 md:p-0">About
                        Us</a>
                </li>
                <li>
                    <a href="{{ route('home') }}#contactSection"
                       class="block py-2 px-3 text-gray-900 hover:text-blue-700 transition-colors duration-200 md:p-0">Contact
                        Us</a>
                </li>

                @auth
                <li class="block md:hidden">
                    <a href="{{ $dashboardRoute }}"
                       class="block py-2 px-3 text-white bg-green-600 hover:bg-green-700 rounded-sm transition">Dashboard</a>
                </li>
                <li class="block md:hidden">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="block py-2 px-3 text-white bg-red-600 hover:bg-red-700 rounded-sm transition">
                            Logout
                        </button>
                    </form>
                </li>
                @endauth

                @guest
                <li class="block md:hidden">
                    <a href="{{ route('login.form') }}"
                       class="block py-2 px-3 text-gray-900 hover:text-blue-700 transition">Login</a>
                </li>
                <li class="block md:hidden">
                    <a href="{{ route('register.form') }}"
                       class="block mt-2 px-5 py-2 bg-blue-600 text-white rounded-full text-center hover:bg-blue-700 transition">
                        Sign Up Free
                    </a>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
