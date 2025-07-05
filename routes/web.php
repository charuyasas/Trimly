<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/bookings', function () {
    return view('bookings');
});

Route::get('/customers', function () {
    return view('customer');
});

Route::get('/services', function () {
    return view('services');
});

Route::get('/employee', function () {
    return view('employee');
});

Route::get('/invoice', function () {
    return view('invoice/invoice');
});

Route::get('/invoiceList', function () {
    return view('invoice/invoice-list');
});

Route::get('/postingAccount', function () {
    return view('accounts/posting-accounts');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('index');
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
