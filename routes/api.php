<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\BookingController;
use App\Models\Booking;
use App\Models\Employee;
use App\Http\Controllers\API\HeadingAccountController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\MainAccountController;
use App\Http\Controllers\API\PostingAccountController;
use App\Http\Controllers\API\TitleAccountController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\CategoryController;

Route::apiResource('categories', CategoryController::class);

Route::apiResource('suppliers', SupplierController::class);

Route::apiResource('bookings', BookingController::class);

Route::apiResource('customers', CustomerController::class);
Route::get('/customer-list', [CustomerController::class, 'loadCustomerDropdown']);

Route::apiResource('services', ServiceController::class);

Route::apiResource('employees', EmployeeController::class);
Route::get('/employees-list', [EmployeeController::class, 'loadEmployeeDropdown']);

Route::apiResource('invoices', InvoiceController::class);
Route::get('/item-list', [InvoiceController::class, 'loadItemDropdown']);
Route::get('/invoice-list', [InvoiceController::class, 'index']);
Route::get('/invoice-list-dropdown', [InvoiceController::class, 'loadInvoiceDropdown']);
Route::post('/new-invoice',[InvoiceController::class, 'store']);
Route::post('/finish-invoice/{id}',[InvoiceController::class, 'finishInvoice']);
Route::get('/invoice-items/{id}', [InvoiceController::class, 'getInvoiceItems']);

Route::apiResource('postingAccount', PostingAccountController::class);

Route::get('/main_account_list', [MainAccountController::class, 'loadMainAccountDropdown']);
Route::get('/heading_account_list/{mainAcc}', [HeadingAccountController::class, 'loadHeadingAccountDropdown']);
Route::get('/title_account_list/{mainAcc}/{headingAcc}', [TitleAccountController::class, 'loadTitleAccountDropdown']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/calendar/employees', function () {
    return Employee::select('id', 'name as title')->get();
});

Route::get('/calendar/bookings', function () {
    return Booking::with(['customer', 'employee'])
        ->where('status', '!=', 'cancelled') // Exclude cancelled
        ->get()
        ->map(function ($b) {
            return [
                'id' => $b->id,
                'resourceId' => $b->employee_id,
                'title' => $b->customer->name ?? 'Customer',
                'start' => $b->booking_date . 'T' . $b->start_time,
                'end' => $b->booking_date . 'T' . $b->end_time,
                'status' => $b->status
            ];
        });
});


Route::get('/bookings/{id}', [BookingController::class, 'show']);

Route::get('/employees-list', [EmployeeController::class, 'loadEmployeeDropdown']);

Route::get('/customer-list', [CustomerController::class, 'loadCustomerDropdown']);

Route::get('/service-list', [ServiceController::class, 'loadServiceDropdown']);

