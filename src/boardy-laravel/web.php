<?php

use App\Http\Controllers\Auth\GitHubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/auth/github',          [GitHubController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GitHubController::class, 'callback']);

require __DIR__.'/auth.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Страница, на которую Passport редиректит с ?code=...
Route::view('/oauth/callback', 'oauth-callback');

// Серверный silent-refresh: читает refresh_token из HttpOnly cookie
Route::post('/auth/refresh', function (Request $request) {
    $refresh = $request->cookie('refresh_token');
    if (!$refresh) {
        return response()->json(['error' => 'no refresh token'], 401);
    }

    $resp = Http::asForm()->post(url('/oauth/token'), [
        'grant_type'    => 'refresh_token',
        'refresh_token' => $refresh,
        'client_id'     => '019e5df8-ad10-73c6-a47f-802511349a1b',
    ]);

    if (!$resp->ok()) {
        return response()->json(['error' => 'refresh failed'], 401);
    }

    $data = $resp->json();

    $newRefresh = $data['refresh_token'] ?? null;
    unset($data['refresh_token']);

    $out = response()->json($data);
    if ($newRefresh) {
        $out->cookie(
            'refresh_token',
            $newRefresh,
            60 * 24 * 30,
            '/',
            null,
            true,
            true,
            false,
            'Lax'
        );
    }
    return $out;
});
