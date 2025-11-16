<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\CustomerController;

// Redirect root to dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Sites Management
Route::resource('sites', SiteController::class);

// Boxes Management
Route::resource('boxes', BoxController::class);

// Customers Management
Route::resource('customers', CustomerController::class);

// Contracts Management
Route::resource('contracts', \App\Http\Controllers\ContractController::class);
