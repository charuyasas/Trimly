<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserRoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Admin protected routes for Role and UserRole management
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::post('users/{user}/roles', [UserRoleController::class, 'assignRole']);
    // Using {role} for route model binding for the role to be revoked.
    Route::delete('users/{user}/roles/{role}', [UserRoleController::class, 'revokeRole']);

    // User activation/deactivation routes
    Route::patch('users/{user}/activate', [UserRoleController::class, 'activateUser'])->name('users.activate');
    Route::patch('users/{user}/deactivate', [UserRoleController::class, 'deactivateUser'])->name('users.deactivate');

    // User block/unblock routes
    Route::patch('users/{user}/block', [UserRoleController::class, 'blockUser'])->name('users.block');
    Route::patch('users/{user}/unblock', [UserRoleController::class, 'unblockUser'])->name('users.unblock');
});
