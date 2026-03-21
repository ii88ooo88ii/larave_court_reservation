<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\CourtController;

// Public API routes
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    
    // User Management with permission checks
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:users.view');
    Route::get('/users/{id}', [UserController::class, 'show'])->middleware('permission:users.view');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:users.create');
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('permission:users.edit');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('permission:users.delete');
    
    // Role Management with permission checks
    Route::get('/roles', [RoleController::class, 'index'])->middleware('permission:roles.view');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->middleware('permission:roles.view');
    Route::post('/roles', [RoleController::class, 'store'])->middleware('permission:roles.create');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->middleware('permission:roles.edit');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete');
    
    // Tenant Management with permission checks
    Route::get('/tenants', [TenantController::class, 'index'])->middleware('permission:tenants.view');
    Route::get('/tenants/{id}', [TenantController::class, 'show'])->middleware('permission:tenants.view');
    Route::post('/tenants', [TenantController::class, 'store'])->middleware('permission:tenants.create');
    Route::put('/tenants/{id}', [TenantController::class, 'update'])->middleware('permission:tenants.edit');
    Route::delete('/tenants/{id}', [TenantController::class, 'destroy'])->middleware('permission:tenants.delete');
    Route::patch('/tenants/{id}/toggle-status', [TenantController::class, 'toggleStatus'])->middleware('permission:tenants.edit');
    
    // Court Management (Admin only - will be checked in controller)
    Route::get('/courts', [CourtController::class, 'index']);
    Route::get('/courts/{id}', [CourtController::class, 'show']);
    Route::post('/courts', [CourtController::class, 'store']);
    Route::put('/courts/{id}', [CourtController::class, 'update']);
    Route::delete('/courts/{id}', [CourtController::class, 'destroy']);
    Route::patch('/courts/{id}/toggle-status', [CourtController::class, 'toggleStatus']);
    
    // Additional court endpoints
    Route::get('/court-types', [CourtController::class, 'getTypes']);
    Route::get('/court-surfaces', [CourtController::class, 'getSurfaces']);
    Route::get('/court-facilities', [CourtController::class, 'getFacilities']);
});