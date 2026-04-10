<?php

use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

Route::get('/demo/widget/view', function () {
    if (app()->environment('production')) {
        abort(404);
    }
    return view('widget');
})->name('widget');

Route::redirect('/login-redirect', '/login')->name('login');

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);
