<?php

use Illuminate\Support\Facades\Route;

Route::get('/demo/widget/view', function () {
    if (app()->environment('production')) {
        abort(404);
    }
    return view('widget');
})->name('widget');

Route::redirect('/login-redirect', '/login')->name('login');


