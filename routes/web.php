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

Route::get('/accountStructure', function () {
    return view('accounts/account-structure-breakdown');
});

Route::get('/roles', function () {
    return view('users/roles');
});

Route::get('/users', function () {
    return view('users/users');
});

Route::get('/stockIssue', function () {
    return view('employee-stock-issue');
});

Route::get('/expenses', function () {
    return view('expenses');
});

Route::get('/item-list-report', function () {
    return view('reports/item-list-report');
});

Route::get('/stock-value-report', function () {
    return view('reports.stock-value-report');
});

Route::get('/stock-summary-report', function () {
    return view('reports.stock-summary-report');
});

Route::get('/stock-detail-report', function () {
    return view('reports.stock-detail-report');
});

Route::get('/employee-wise-sales-summary-report', function () {
    return view('reports/employee-wise-sales-summary-report');
});

Route::get('/sales-summary-report', function () {
    return view('reports/sales-summary-report');
});

Route::get('/cash-transfer', function () {
    return view('cash-transfer');
});

Route::get('/supplier-payment-list', function () {
    return view('supplierPayments/supplier-payment-list');
});

Route::get('/supplier-payment', function () {
    return view('supplierPayments/supplier-payment');
})->name('supplier.payment');


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
