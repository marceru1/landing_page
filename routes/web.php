<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $photos = \App\Models\Photo::latest()->get();
    return view('welcome', compact('photos'));
});

Route::get('/dashboard', function () {
    $photos = \App\Models\Photo::latest()->get();
    return view('dashboard', compact('photos'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/photos', [\App\Http\Controllers\PhotoController::class, 'store'])->name('photos.store');
    Route::delete('/photos/{photo}', [\App\Http\Controllers\PhotoController::class, 'destroy'])->name('photos.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
