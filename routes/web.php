<?php

use Illuminate\Support\Facades\Route;

// Route::get('/login', function () {
//     return view('auth.login');
// });

Route::get('/', function () {
    return view('index');
});

Route::get('/services', function () {
    return view('services.index');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('index');
});
