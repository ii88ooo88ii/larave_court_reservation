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
use App\Http\Controllers\Admin\ReservationController;
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
    
    // User Management - Use only resource, no individual routes
    Route::resource('users', UserController::class);
    
    // Role Management - Use only resource
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
    
    // Tenant Management - Use only resource
    Route::resource('tenants', TenantController::class);
    Route::get('tenants/{tenant}/toggle-status', [TenantController::class, 'toggleStatus'])->name('tenants.toggle-status');
    
    // Court Management - Use only resource
    Route::resource('courts', CourtController::class);
    Route::get('courts/{court}/toggle-status', [CourtController::class, 'toggleStatus'])->name('courts.toggle-status');
    
    // Court Type Management - Use only resource
    Route::resource('court-types', CourtTypeController::class);
    Route::get('court-types/{courtType}/toggle-status', [CourtTypeController::class, 'toggleStatus'])->name('court-types.toggle-status');
    
    // Pricing Management - Use only resource
    Route::resource('pricings', PricingController::class);
    Route::get('pricings/{pricing}/toggle-status', [PricingController::class, 'toggleStatus'])->name('pricings.toggle-status');
    
    // Additional Fees Management - Use only resource
    Route::resource('additional-fees', AdditionalFeeController::class);
    Route::get('additional-fees/{additionalFee}/toggle-status', [AdditionalFeeController::class, 'toggleStatus'])->name('additional-fees.toggle-status');
    
    // Reservation Routes
    Route::resource('reservations', ReservationController::class);
    Route::post('reservations/{reservation}/extend', [ReservationController::class, 'extend'])->name('reservations.extend');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::get('reservations-calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
    Route::post('check-availability', [ReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
    Route::get('available-slots', [ReservationController::class, 'getAvailableSlots'])->name('reservations.available-slots');
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