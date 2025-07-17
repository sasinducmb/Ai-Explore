<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

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
