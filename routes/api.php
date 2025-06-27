<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\InvoiceController;

Route::apiResource('customers', CustomerController::class);
Route::get('/customer-list', [CustomerController::class, 'loadCustomerDropdown']);

Route::apiResource('services', ServiceController::class);

Route::apiResource('employees', EmployeeController::class);
Route::get('/employees-list', [EmployeeController::class, 'loadEmployeeDropdown']);

Route::apiResource('invoices', InvoiceController::class);
Route::get('/item-list', [InvoiceController::class, 'loadItemDropdown']);
Route::get('/invoice-list', [InvoiceController::class, 'loadInvoiceDropdown']);
Route::post('/new-invoice',[InvoiceController::class, 'store']);
Route::post('/finish-invoice/{id}',[InvoiceController::class, 'finishInvoice']);
Route::get('/invoice-items/{id}', [InvoiceController::class, 'getInvoiceItems']);


Route::apiResource('invoices', InvoiceController::class);

Route::get('/employees-list', [EmployeeController::class, 'loadEmployeeDropdown']);

Route::apiResource('invoices', InvoiceController::class);
Route::get('/item-list', [InvoiceController::class, 'loadItemDropdown']);
Route::get('/invoice-list', [InvoiceController::class, 'loadInvoiceDropdown']);
Route::post('/new-invoice',[InvoiceController::class, 'store']);
Route::post('/finish-invoice/{id}',[InvoiceController::class, 'finishInvoice']);
Route::get('/invoice-items/{id}', [InvoiceController::class, 'getInvoiceItems']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
