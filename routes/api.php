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
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\GrnController;

Route::apiResource('items', ItemController::class);
Route::get('/items-list', [ItemController::class, 'loadItemDropdown']);

Route::apiResource('sub-categories', SubCategoryController::class);
Route::get('/sub-categories-list', [SubCategoryController::class, 'loadSubCategoryDropdown']);

Route::apiResource('categories', CategoryController::class);
Route::get('/categories-list', [CategoryController::class, 'loadCategoryDropdown']);

Route::apiResource('grn', GrnController::class);
Route::post('/new-grn', [GrnController::class, 'store']);
Route::get('/grn-list-dropdown', [GrnController::class, 'loadGrnDropdown']);
Route::get('/grn-details/{id}', [GrnController::class, 'getGrnDetails']);
Route::post('/grn-finalize/{id}', [GrnController::class, 'finalize']);

Route::apiResource('suppliers', SupplierController::class);
Route::get('/suppliers-list', [SupplierController::class, 'loadSupplierDropdown']);

Route::apiResource('customers', CustomerController::class);
Route::get('/customer-list', [CustomerController::class, 'loadCustomerDropdown']);

Route::apiResource('services', ServiceController::class);
Route::get('/service-list', [ServiceController::class, 'loadServiceDropdown']);

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

Route::apiResource('bookings', BookingController::class);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
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








