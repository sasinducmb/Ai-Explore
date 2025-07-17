    <footer class="bg-white border-t border-gray-200 mt-30 pt-15 pb-6">
        <div class="container px-15 mx-auto ">
            <div class="flex flex-col lg:flex-row justify-center sm:justify-between items-start lg:items-center gap-10">

                <!-- Logo -->
                <div class="flex-shrink-0 mb-4 flex flex-col items-center ">
                    <img src="{{ asset('asset/img/logo.png') }}" alt="AI Explorer" class="h-12 mb-2">
                    <span class="font-bold">AI Explorer</span>
                </div>

                <!-- Footer Links -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-sm w-full max-w-4xl">

                    <!-- Product -->
                    <div>
                        <h3 class="text-gray-500 font-semibold mb-2">Product</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="hover:underline">News</a></li>
                            <li><a href="#" class="hover:underline">Article</a></li>
                            <li><a href="#" class="hover:underline">Education</a></li>
                        </ul>
                    </div>

                    <!-- Explore -->
                    <div>
                        <h3 class="text-gray-500 font-semibold mb-2">Explore</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('learn-ai-tools') }}" class="hover:underline">Learn AI Tools</a></li>
                            <li><a href="{{ route('explore-ai-tools') }}" class="hover:underline">Explore AI Tools</a>
                            </li>
                            <li><a href="{{ route('meet-your-ai-buddy') }}" class="hover:underline">Meet Your AI
                                    Buddy</a></li>
                            <li><a href="{{ route('chat-with-ai') }}" class="hover:underline">Chat with AI</a></li>
                        </ul>
                    </div>

                    <!-- Company -->
                    <div>
                        <h3 class="text-gray-500 font-semibold mb-2">Company</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}#contactSection" class="hover:underline">Contact Us</a></li>
                            <li><a href="{{ route('home') }}#aboutSection" class="hover:underline">About Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-200 pt-6 mt-10 flex justify-center">

            <div class=" container px-15 flex flex-col lg:flex-row items-center justify-between text-sm text-gray-500">
                <div class="mb-4 lg:mb-0">
                    © 2025 All rights reserved.
                </div>
                <div class="flex items-center space-x-6">
                    <a href="#" class="hover:underline">Privacy Policy</a>
                    <a href="#" class="hover:underline">Terms & Conditions</a>
                </div>
                <div class="flex items-center space-x-4 mt-4 lg:mt-0">
                    <!-- Social links using Heroicons (Tailwind CSS) -->
                    <a href="#" aria-label="YouTube"
                        class="text-gray-600 hover:text-blue-500 transition-colors group">
                        <svg class="w-6 h-6 text-gray-800 group-hover:text-blue-500 transition-colors"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M21.7 8.037a4.26 4.26 0 0 0-.789-1.964 2.84 2.84 0 0 0-1.984-.839c-2.767-.2-6.926-.2-6.926-.2s-4.157 0-6.928.2a2.836 2.836 0 0 0-1.983.839 4.225 4.225 0 0 0-.79 1.965 30.146 30.146 0 0 0-.2 3.206v1.5a30.12 30.12 0 0 0 .2 3.206c.094.712.364 1.39.784 1.972.604.536 1.38.837 2.187.848 1.583.151 6.731.2 6.731.2s4.161 0 6.928-.2a2.844 2.844 0 0 0 1.985-.84 4.27 4.27 0 0 0 .787-1.965 30.12 30.12 0 0 0 .2-3.206v-1.516a30.672 30.672 0 0 0-.202-3.206Zm-11.692 6.554v-5.62l5.4 2.819-5.4 2.801Z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter"
                        class="text-gray-600 hover:text-blue-500 transition-colors group">
                        <svg class="w-6 h-6 text-gray-800 group-hover:text-blue-500 transition-colors"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M22 5.892a8.178 8.178 0 0 1-2.355.635 4.074 4.074 0 0 0 1.8-2.235 8.343 8.343 0 0 1-2.605.981A4.13 4.13 0 0 0 15.85 4a4.068 4.068 0 0 0-4.1 4.038c0 .31.035.618.105.919A11.705 11.705 0 0 1 3.4 4.734a4.006 4.006 0 0 0 1.268 5.392 4.165 4.165 0 0 1-1.859-.5v.05A4.057 4.057 0 0 0 6.1 13.635a4.192 4.192 0 0 1-1.856.07 4.108 4.108 0 0 0 3.831 2.807A8.36 8.36 0 0 1 2 18.184 11.732 11.732 0 0 0 8.291 20 11.502 11.502 0 0 0 19.964 8.5c0-.177 0-.349-.012-.523A8.143 8.143 0 0 0 22 5.892Z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn"
                        class="text-gray-600 hover:text-blue-500 transition-colors group">
                        <svg class="w-6 h-6 text-gray-800 group-hover:text-blue-500 transition-colors"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M12.51 8.796v1.697a3.738 3.738 0 0 1 3.288-1.684c3.455 0 4.202 2.16 4.202 4.97V19.5h-3.2v-5.072c0-1.21-.244-2.766-2.128-2.766-1.827 0-2.139 1.317-2.139 2.676V19.5h-3.19V8.796h3.168ZM7.2 6.106a1.61 1.61 0 0 1-.988 1.483 1.595 1.595 0 0 1-1.743-.348A1.607 1.607 0 0 1 5.6 4.5a1.601 1.601 0 0 1 1.6 1.606Z"
                                clip-rule="evenodd" />
                            <path d="M7.2 8.809H4V19.5h3.2V8.809Z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        </div>

    </footer>
