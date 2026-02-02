<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'),
    ];

    $xml = view('sitemap', ['urls' => $urls]);

    return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/{id}', [ChallengeController::class, 'show'])->name('challenges.show');
    Route::post('/challenges/{id}/submit', [ChallengeController::class, 'submit'])->name('challenges.submit');
    Route::post('/challenges/{id}/reset', [ChallengeController::class, 'reset'])->name('challenges.reset');
});
