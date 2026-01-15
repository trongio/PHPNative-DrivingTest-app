<?php

use App\Http\Controllers\QuestionBrowserController;
use App\Http\Controllers\UserSelectionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// User Selection (Login/Register)
Route::get('/', [UserSelectionController::class, 'index'])->name('home');
Route::post('/login', [UserSelectionController::class, 'login'])->name('login');
Route::post('/register', [UserSelectionController::class, 'store'])->name('register');
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Question Browser
    Route::get('questions', [QuestionBrowserController::class, 'index'])->name('questions.index');
    Route::post('questions/{question}/answer', [QuestionBrowserController::class, 'answer'])->name('questions.answer');
    Route::post('questions/{question}/bookmark', [QuestionBrowserController::class, 'bookmark'])->name('questions.bookmark');

    // Profile image update
    Route::post('/profile/image', [UserSelectionController::class, 'updateImage'])->name('profile.image.update');
});

require __DIR__.'/settings.php';
