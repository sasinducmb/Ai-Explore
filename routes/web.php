<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PromptingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DesignController;

Route::get('/', function () {
    return view('index');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('login');
})->name('register');

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

Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/learn-ai-tools/{id}', [LanguageController::class, 'show'])->name('ai-tools.show');

Route::get('/prompting', [PromptingController::class, 'show'])->name('prompting.show');
Route::post('/prompting/submit', [PromptingController::class, 'submit'])->name('prompting.submit');
Route::get('/prompting/results', [PromptingController::class, 'results'])->name('prompting.results');




Route::get('/design-tools', [DesignController::class, 'show'])->name('design.tools');
Route::post('/design-tools/submit', [DesignController::class, 'submit'])->name('design.submit');
Route::get('/design-results', [DesignController::class, 'results'])->name('design.results');
