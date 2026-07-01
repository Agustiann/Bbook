<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/private/avatar/{filename}', function (string $filename) {
    if (! Auth::check()) {
        abort(403);
    }

    $path = 'profile-photos/' . $filename;

    abort_unless(Storage::disk('local')->exists($path), 404);

    $user = Auth::user();
    $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
    $userAvatarPath = $user->$avatarColumn;

    if ($userAvatarPath !== $path && basename($userAvatarPath) !== $filename) {
        abort(403);
    }

    return response()->file(
        Storage::disk('local')->path($path),
        ['Content-Type' => mime_content_type(Storage::disk('local')->path($path))]
    );
})->name('private.avatar');