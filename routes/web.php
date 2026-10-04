<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/bookmarks');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/bookmarks')->name('dashboard');

    Route::get('bookmarks/export', [BookmarkController::class, 'export'])->name('bookmarks.export');
    Route::resource('bookmarks', BookmarkController::class);
    Route::resource('collections', CollectionController::class);
    Route::resource('tags', TagController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
