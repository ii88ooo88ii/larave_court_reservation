<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\CourtController;
use App\Http\Controllers\Admin\CourtTypeController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\AdditionalFeeController; 
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
});

// Dashboard Redirect
Route::middleware(['auth'])->get('/dashboard', function () {
    if (auth()->user()->isRegularUser()) {
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('admin.dashboard');
})->name('dashboard.redirect');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    
    // Role Management
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
    
    // Tenant Management
    Route::resource('tenants', TenantController::class)->except(['show']);
    Route::get('tenants/{tenant}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('tenants/{tenant}', [TenantController::class, 'update'])->name('tenants.update');
    Route::delete('tenants/{tenant}', [TenantController::class, 'destroy'])->name('tenants.destroy');
    Route::get('tenants/create', [TenantController::class, 'create'])->name('tenants.create');
    Route::post('tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
    
    // Court Management - ADD THESE LINES
    Route::resource('courts', CourtController::class)->except(['show']);
    Route::get('courts/{court}/toggle-status', [CourtController::class, 'toggleStatus'])->name('courts.toggle-status');

    // Pricing Management
    Route::resource('pricings', PricingController::class)->except(['show']);
    Route::get('pricings/{pricing}/toggle-status', [PricingController::class, 'toggleStatus'])->name('pricings.toggle-status');

    // Additional Fees Management
    Route::resource('additional-fees', AdditionalFeeController::class)->except(['show']);
    Route::get('additional-fees/{additionalFee}/toggle-status', [AdditionalFeeController::class, 'toggleStatus'])->name('additional-fees.toggle-status');

});

// User Routes (Regular Users)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
});

// Home Redirect
Route::middleware(['auth'])->get('/home', function () {
    if (auth()->user()->isRegularUser()) {
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('admin.dashboard');
});