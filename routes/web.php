<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\PromptingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\GeminiChatbotController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Role-Based Dashboards
    Route::get('/admin/dashboard', function () {
        if (Auth::user()->role !== 'ADMIN') {
            abort(403, 'Unauthorized access.');
        }
        return view('dashboards.admin');
    })->name('admin.dashboard');

    Route::get('/parent/dashboard', function () {
        if (Auth::user()->role !== 'PARENT') {
            abort(403, 'Unauthorized access.');
        }
        return view('dashboards.parent');
    })->name('parent.dashboard');

    // Static Pages
    Route::view('/learn-ai-tools', 'learn-ai-tools')->name('learn-ai-tools');
    Route::view('/explore-ai-tools', 'explore-ai-tools')->name('explore-ai-tools');
    Route::view('/funny-activities', 'funny-activities.funny-activities')->name('funny-activities');
    Route::view('/meet-your-ai-buddy', 'index')->name('meet-your-ai-buddy');
    Route::view('/chat-with-ai', 'index')->name('chat-with-ai');

    // Dynamic Tool Routes
    Route::get('/learn-ai-tools/{id}', [LanguageController::class, 'show'])->name('ai-tools.show');

    // Prompting
    Route::get('/prompting', [PromptingController::class, 'show'])->name('prompting.show');
    Route::post('/prompting/submit', [PromptingController::class, 'submit'])->name('prompting.submit');
    Route::get('/prompting/results', [PromptingController::class, 'results'])->name('prompting.results');
    Route::match(['GET', 'POST'], '/prompting/restart', [PromptingController::class, 'restart'])->name('prompting.restart');

    // Design Tools
    Route::get('/design-tools', [DesignController::class, 'show'])->name('design.tools');
    Route::post('/design-tools', [DesignController::class, 'submit'])->name('design.submit');
    Route::get('/design-results', [DesignController::class, 'results'])->name('design.results');

    // Gemini Chatbot
    Route::get('/gemini', [GeminiChatbotController::class, 'index'])->name('gemini.index');
    Route::post('/gemini/chat', [GeminiChatbotController::class, 'chatSimple'])->name('gemini.chat.send');

    // Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/design-results/{id}', [AdminController::class, 'showDesignResult'])->name('admin.design.show');
        Route::get('/prompting-results/{id}', [AdminController::class, 'showPromptingResult'])->name('admin.prompting.show');
    });
});
