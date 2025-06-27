<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\BookingController;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Employee;

Route::apiResource('bookings', BookingController::class);

Route::apiResource('customers', CustomerController::class);

Route::apiResource('services', ServiceController::class);

Route::apiResource('employees', EmployeeController::class);

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

