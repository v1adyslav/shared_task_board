<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [BoardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::patch('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
