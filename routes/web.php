<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ClientPortalController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - Admin/Staff
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Sites Management
    Route::resource('sites', SiteController::class);

    // Boxes Management
    Route::resource('boxes', BoxController::class);

    // Customers Management
    Route::resource('customers', CustomerController::class);

    // Contracts Management
    Route::resource('contracts', ContractController::class);

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Client Portal Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('client')->name('client.')->group(function () {
    // Client Dashboard
    Route::get('/dashboard', [ClientPortalController::class, 'dashboard'])->name('dashboard');

    // Contracts
    Route::get('/contracts', [ClientPortalController::class, 'contracts'])->name('contracts');
    Route::get('/contracts/{contract}', [ClientPortalController::class, 'showContract'])->name('contracts.show');
    Route::get('/contracts/{contract}/download', [ClientPortalController::class, 'downloadContract'])->name('contracts.download');

    // Invoices
    Route::get('/invoices', [ClientPortalController::class, 'invoices'])->name('invoices');
    Route::get('/invoices/{invoice}/download', [ClientPortalController::class, 'downloadInvoice'])->name('invoices.download');

    // Payments
    Route::get('/payments', [ClientPortalController::class, 'payments'])->name('payments');

    // Profile
    Route::get('/profile', [ClientPortalController::class, 'profile'])->name('profile');
    Route::patch('/profile', [ClientPortalController::class, 'updateProfile'])->name('profile.update');
});

require __DIR__.'/auth.php';
