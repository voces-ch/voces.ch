<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin')->name('home');

Route::get('/demo/widget/view', function () {
    if (app()->environment('production')) {
        abort(404);
    }
    return view('widget');
})->name('widget');

Route::redirect('/login-redirect', '/login')->name('login');

Route::get('/auth/google/redirect', [SocialiteController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);

Route::get('/verify/signature/{signature:uuid}/{token}', [SignatureController::class, 'verify'])->name('signature.verify');
Route::get("/thank-you/{campaign}/{signature:uuid}", [SignatureController::class, 'thankYou'])->name('thank-you');
