<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/book-image/{path}', function (string $path) {
    abort_unless(Auth::check(), 403);

    $disk = Storage::disk('private');

    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path));
})->where('path', '.*')->name('book.image');