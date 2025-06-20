<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\View\CustomerViewController;

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/', function () {
    return view('index');
});

// Customer Routes

Route::get('/customers', [CustomerViewController::class, 'index'])->name('view.customers');

Route::get('/add-customer', [CustomerViewController::class, 'create'])->name('add.customer.form');

Route::post('/add-customer', [CustomerViewController::class, 'store'])->name('add.customer');

Route::get('/edit-customer/{id}', [CustomerViewController::class, 'edit'])->name('edit.customer.form');

Route::put('/update-customer/{id}', [CustomerViewController::class, 'update'])->name('update.customer');

Route::delete('/delete-customer/{id}', [CustomerViewController::class, 'destroy'])->name('delete.customer');
