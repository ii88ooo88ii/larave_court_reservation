<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});

Route::get('/home', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth');

Route::get('/test-assets', function() {
    return view('layouts.admin');
});