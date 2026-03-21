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
    // Dashboard - requires view permission
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');
    
    // User Management Routes with permissions
    Route::resource('users', UserController::class)
        ->middleware('permission:users.view');
    
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('permission:users.edit');
    
    Route::put('users/{user}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:users.edit');
    
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('permission:users.delete');
    
    Route::get('users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('permission:users.create');
    
    Route::post('users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:users.create');
    
    // Role Management Routes with permissions
    Route::resource('roles', RoleController::class)
        ->middleware('permission:roles.view');
    
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('permission:roles.edit');
    
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:roles.edit');
    
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:roles.delete');
    
    Route::get('roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('permission:roles.create');
    
    Route::post('roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:roles.create');
    
    // Permission management for roles
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])
        ->name('roles.permissions')
        ->middleware('permission:roles.manage-permissions');
    
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
        ->name('roles.update-permissions')
        ->middleware('permission:roles.manage-permissions');
    
    // Tenant Management Routes with permissions
    Route::resource('tenants', TenantController::class)
        ->middleware('permission:tenants.view');
    
    Route::get('tenants/{tenant}/edit', [TenantController::class, 'edit'])
        ->name('tenants.edit')
        ->middleware('permission:tenants.edit');
    
    Route::put('tenants/{tenant}', [TenantController::class, 'update'])
        ->name('tenants.update')
        ->middleware('permission:tenants.edit');
    
    Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])
        ->name('tenants.destroy')
        ->middleware('permission:tenants.delete');
    
    Route::get('tenants/create', [TenantController::class, 'create'])
        ->name('tenants.create')
        ->middleware('permission:tenants.create');
    
    Route::post('tenants', [TenantController::class, 'store'])
        ->name('tenants.store')
        ->middleware('permission:tenants.create');
    
    Route::get('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])
        ->name('tenants.toggle-status')
        ->middleware('permission:tenants.edit');
});

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');