<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TenantController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Role Management Routes
    Route::resource('roles', RoleController::class);
    
    // Tenant Management Routes
    Route::resource('tenants', TenantController::class);
    Route::get('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
});

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');