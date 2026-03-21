<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TenantController;

// Public API routes
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // User Management routes
    Route::apiResource('users', UserController::class);
    
    // Role Management routes
    Route::apiResource('roles', RoleController::class);
    
    // Tenant Management routes
    Route::apiResource('tenants', TenantController::class);
    Route::patch('tenants/{id}/toggle-status', [TenantController::class, 'toggleStatus']);
});