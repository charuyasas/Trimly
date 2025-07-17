<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('/grn', function () {
    return view('grn');
});

Route::get('/items', function () {
    return view('items');
});

Route::get('/sub-categories', function () {
    return view('sub-categories');
});

Route::get('/categories', function () {
    return view('categories');
});

Route::get('/supplier', function () {
    return view('supplier');
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

Route::get('/stockIssue', function () {
    return view('employee-stock-issue');
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
