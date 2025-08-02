<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\PromptingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\GeminiChatbotController;

// -----------------------------
// Public Routes (No Login Required)
// -----------------------------

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Language switcher
Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// -----------------------------
// Protected Routes (Login Required)
// -----------------------------

Route::middleware(['auth'])->group(function () {
    // Pages
    Route::get('/learn-ai-tools', function () {
        return view('learn-ai-tools');
    })->name('learn-ai-tools');

    Route::get('/explore-ai-tools', function () {
        return view('explore-ai-tools');
    })->name('explore-ai-tools');

    Route::get('/meet-your-ai-buddy', function () {
        return view('index');
    })->name('meet-your-ai-buddy');

    Route::get('/chat-with-ai', function () {
        return view('index');
    })->name('chat-with-ai');

    Route::get('/learn-ai-tools/{id}', [LanguageController::class, 'show'])->name('ai-tools.show');

    // Prompting
    Route::get('/prompting', [PromptingController::class, 'show'])->name('prompting.show');
    Route::post('/prompting/submit', [PromptingController::class, 'submit'])->name('prompting.submit');
    Route::get('/prompting/results', [PromptingController::class, 'results'])->name('prompting.results');

    // Design tools
    Route::get('/design-tools', [DesignController::class, 'show'])->name('design.tools');
    Route::post('/design-tools/submit', [DesignController::class, 'submit'])->name('design.submit');
    Route::get('/design-results', [DesignController::class, 'results'])->name('design.results');

    // Gemini AI chatbot
    Route::get('/gemini', [GeminiChatbotController::class, 'index'])->name('gemini.index');
    Route::post('/gemini/chat', [GeminiChatbotController::class, 'chatSimple'])->name('gemini.chat.send');
});
