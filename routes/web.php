<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/', function () {
    return view('index');
});

// Customer Routes
Route::get('/customers', [CustomerController::class, 'index'])->name('view.customers');

Route::get('/add-customer', [CustomerController::class, 'create'])->name('add.customer.form');

Route::post('/add-customer', [CustomerController::class, 'store'])->name('add.customer');

Route::get('/edit-customer/{id}', [CustomerController::class, 'edit'])->name('edit.customer.form');

Route::put('/update-customer/{id}', [CustomerController::class, 'update'])->name('update.customer');

Route::delete('/delete-customer/{id}', [CustomerController::class, 'destroy'])->name('delete.customer');

