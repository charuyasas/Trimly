<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/customers', function () {
    return view('customer');
});

Route::get('/services', function () {
    return view('services');
});

Route::get('/employees', function () {
    return view('employee');
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
