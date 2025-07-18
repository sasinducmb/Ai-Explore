@extends('layout.app')

@section('title')
{{ __('Learn with Gemini') }}
@endsection

@section('content')
<!-- Navigation -->
@include('layout.nav')

<section class="px-6 py-12 bg-white">
    <div class="max-w-6xl mx-auto text-center">
        <!-- Heading -->
        <h2 class="text-4xl font-bold poppins text-gray-900 mb-3">Learn with Gemini</h2>
        <p class="text-gray-600 font-medium poppins max-w-3xl mx-auto mb-12 text-base">
            Chat with Gemini to explore fun facts about math, science, history, and more! Ask questions and spark your curiosity with our friendly AI buddy designed for kids like you!
        </p>

        <!-- Chatbot Interface -->
        <div class="bg-white shadow-md shadow-blue-100 rounded-xl px-6 py-6 border border-blue-200 transition-all duration-300 ease-in-out">
            <div class="flex items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Your Learning Buddy</h3>
            </div>

            <div id="chatBox" class="chat-box h-[500px] overflow-y-auto border-2 border-blue-200 rounded-lg p-6 mb-5 bg-gray-50">
                <div id="typingIndicator" class="hidden typing-indicator bg-blue-100 rounded-2xl p-4 mb-4 max-w-[50%]">
                    <div class="flex items-center">
                        <div class="w-5 h-5 bg-blue-500 rounded-full animate-pulse mr-3"></div>
                        <span class="text-blue-700 text-lg">Gemini is thinking...</span>
                    </div>
                </div>
            </div>

            <form id="chatForm" class="flex gap-3">
                @csrf
                <input type="text" id="messageInput" class="flex-1 p-4 border-2 border-blue-200 rounded-lg text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" placeholder="Ask about math, science, or history!" autocomplete="off">
                <button type="submit" id="sendButton" class="px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all duration-300 text-lg font-medium">
                    <span id="buttonText">Ask!</span>
                    <span id="buttonSpinner" class="animate-spin h-6 w-6 border-3 border-t-transparent border-white rounded-full hidden"></span>
                </button>
            </form>
        </div>
    </div>
</section>

@include('layout.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const chatBox = $('#chatBox');
        const chatForm = $('#chatForm');
        const messageInput = $('#messageInput');
        const typingIndicator = $('#typingIndicator');
        const sendButton = $('#sendButton');
        const buttonText = $('#buttonText');
        const buttonSpinner = $('#buttonSpinner');

        // Add a kid-friendly welcome message
        addBotMessage("Hi there! I'm Gemini, your learning buddy! 😊 Ask me about math, science, history, or stories, and I'll share fun facts and cool answers! What's something you want to learn today?");

        chatForm.on('submit', function(e) {
            e.preventDefault();
            const message = messageInput.val().trim();

            if (message) {
                addUserMessage(message);
                messageInput.val('');
                showTypingIndicator();
                disableSendButton();

                // Send message to server
                $.ajax({
                    url: "{{ route('gemini.chat.send') }}",
                    type: "POST",
                    data: {
                        message: message,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        hideTypingIndicator();
                        if (response.error) {
                            addBotMessage("Oops, something went wrong! Try asking a fun question about math, science, or history!");
                        } else {
                            addBotMessage(response.response);
                        }
                        enableSendButton();
                    },
                    error: function(xhr, status, error) {
                        hideTypingIndicator();
                        let errorMessage = "Oops, I got a little confused! Try asking something about school, like math or animals!";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        addBotMessage(errorMessage);
                        console.error('AJAX error:', xhr.status, error, xhr.responseText);
                        enableSendButton();
                    }
                });
            }
        });

        function addUserMessage(message) {
            chatBox.append(`
                <div class="message bg-blue-500 text-white rounded-2xl p-4 mb-4 max-w-[70%] ml-auto rounded-br-none text-lg">
                    <strong>You:</strong> ${message}
                </div>
            `);
            scrollToBottom();
        }

        function addBotMessage(message) {
            chatBox.append(`
                <div class="message bg-blue-100 text-gray-800 rounded-2xl p-4 mb-4 max-w-[70%] mr-auto rounded-bl-none text-lg">
                    <strong>AI Explorer:</strong> ${message}
                </div>
            `);
            scrollToBottom();
        }

        function showTypingIndicator() {
            typingIndicator.removeClass('hidden');
            scrollToBottom();
        }

        function hideTypingIndicator() {
            typingIndicator.addClass('hidden');
        }

        function disableSendButton() {
            sendButton.prop('disabled', true);
            buttonText.text('Thinking...');
            buttonSpinner.removeClass('hidden');
        }

        function enableSendButton() {
            sendButton.prop('disabled', false);
            buttonText.text('Ask!');
            buttonSpinner.addClass('hidden');
        }

        function scrollToBottom() {
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }

        // Allow pressing Enter to send (but handle Shift+Enter for new lines)
        messageInput.on('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.submit();
            }
        });
    });
</script>

<style>
    .chat-box {
        scrollbar-width: thin;
        scrollbar-color: #60a5fa transparent;
    }
    .chat-box::-webkit-scrollbar {
        width: 10px;
    }
    .chat-box::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-box::-webkit-scrollbar-thumb {
        background-color: #60a5fa;
        border-radius: 5px;
    }
    .animate-bounce {
        animation: bounce 1s infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endsection
