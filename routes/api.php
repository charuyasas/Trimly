<?php

use App\Http\Controllers\API\ExpensesController;
use App\Http\Controllers\API\ShiftController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\HeadingAccountController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\MainAccountController;
use App\Http\Controllers\API\PostingAccountController;
use App\Http\Controllers\API\TitleAccountController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SidebarLinkController;
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
Route::delete('/grn-item-delete/{id}', [GrnController::class, 'deleteItem']);
Route::put('/grn-item-update/{id}', [GrnController::class, 'updateItem']);
Route::get('/item-cost-details', [GrnController::class, 'getItemCostDetails']);

Route::apiResource('suppliers', SupplierController::class);
Route::get('/suppliers-list', [SupplierController::class, 'loadSupplierDropdown']);

Route::apiResource('bookings', BookingController::class);
Route::get('/bookings/{id}', [BookingController::class, 'show']);
Route::get('/calendar/bookings', [BookingController::class, 'calendarBookings']);
Route::get('/calendar/employees', [BookingController::class, 'calendarEmployees']);

Route::apiResource('customers', CustomerController::class);
Route::get('/customer-list', [CustomerController::class, 'loadCustomerDropdown']);

Route::apiResource('services', ServiceController::class);
Route::get('/service-list', [ServiceController::class, 'loadServiceDropdown']);

Route::apiResource('employees', EmployeeController::class);
Route::get('/employees-list', [EmployeeController::class, 'loadEmployeeDropdown']);

Route::apiResource('employee-stock', StockController::class);
Route::post('/employee-stock-issue', [StockController::class, 'store']);
Route::get('/available-stock', [StockController::class, 'getAvailableStock']);
Route::get('/store-list', [StockController::class, 'loadStoreDropdown']);

Route::apiResource('invoices', InvoiceController::class);
Route::get('/item-list', [InvoiceController::class, 'loadItemDropdown']);
Route::get('/invoice-list', [InvoiceController::class, 'index']);
Route::get('/invoice-list-dropdown', [InvoiceController::class, 'loadInvoiceDropdown']);
Route::post('/new-invoice',[InvoiceController::class, 'store']);
Route::post('/finish-invoice/{id}',[InvoiceController::class, 'finishInvoice']);
Route::get('/invoice-items/{id}', [InvoiceController::class, 'getInvoiceItems']);

Route::get('/main_account_list', [MainAccountController::class, 'loadMainAccountDropdown']);
Route::get('/heading_account_list/{mainAcc}', [HeadingAccountController::class, 'loadHeadingAccountDropdown']);
Route::get('/title_account_list/{mainAcc}/{headingAcc}', [TitleAccountController::class, 'loadTitleAccountDropdown']);
Route::apiResource('postingAccount', PostingAccountController::class);

Route::middleware('auth:sanctum')->apiResource('expenses', ExpensesController::class);
Route::get('/expenses-account-dropdown', [ExpensesController::class, 'loadExpensesAccountDropdown']);
Route::get('/cash-balance', [ExpensesController::class, 'getCashBalance']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('userShift', [ShiftController::class, 'getOngoingShiftDetails']);
    Route::post('userShift', [ShiftController::class, 'startUserShift']);
    Route::get('userShift/{userShift}', [ShiftController::class, 'showUserShiftEndDetails']);
    Route::put('userShift/{userShift}', [ShiftController::class, 'endUserShift']);
});


Route::apiResource('roles', RoleController::class);
Route::get('/role-list-dropdown', [RoleController::class, 'loadRolesDropdown']);
Route::get('/role-permission-list/{role_id}', [RoleController::class, 'loadRolePermissions']);
Route::post('/update-role-permissions/{role}', [RoleController::class, 'updateRolePermissions']);

Route::apiResource('users', UserController::class);
Route::post('/update-password', [UserController::class, 'updateUserPassword']);

Route::apiResource('sidebar-links', SidebarLinkController::class)->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/sidebar-links', [SidebarLinkController::class, 'index']);

Route::get('/stock-value-report', [ItemController::class, 'stockValueReport']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user-permissions', function (Request $request) {
    return response()->json([
        'permissions' => $request->user()->getAllPermissions()->pluck('name')
    ]);
});














