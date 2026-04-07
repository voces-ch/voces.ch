<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/login-redirect', '/login')->name('login');

Route::get('/widget-demo', function () {
    if (app()->environment('production')) {
        abort(404);
    }
    return view('widget');
})->name('widget');

