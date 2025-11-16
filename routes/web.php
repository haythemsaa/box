<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\InsuranceProductController;
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

// Stripe webhook (must be outside auth middleware)
Route::post('/stripe/webhook', [StripePaymentController::class, 'webhook'])->name('stripe.webhook');

// Public Reservation Routes
Route::prefix('reservations')->name('reservations.')->group(function () {
    Route::get('/', [ReservationController::class, 'index'])->name('index');
    Route::get('/boxes/{box}/reserve', [ReservationController::class, 'create'])->name('create');
    Route::post('/boxes/{box}/reserve', [ReservationController::class, 'store'])->name('store');
    Route::get('/{reservation}/confirmation', [ReservationController::class, 'confirmation'])->name('confirmation');
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
    Route::get('/boxes/plan', [BoxController::class, 'plan'])->name('boxes.plan');
    Route::resource('boxes', BoxController::class);

    // Customers Management
    Route::resource('customers', CustomerController::class);

    // Contracts Management
    Route::resource('contracts', ContractController::class);
    Route::post('/contracts/{contract}/insurances', [ContractController::class, 'addInsurance'])->name('contracts.insurances.add');
    Route::delete('/contracts/{contract}/insurances/{contractInsurance}', [ContractController::class, 'cancelInsurance'])->name('contracts.insurances.cancel');

    // Insurance Products Management
    Route::resource('insurance-products', InsuranceProductController::class);
    Route::get('/api/insurance-products/active', [InsuranceProductController::class, 'getActive'])->name('insurance-products.active');

    // Reservations Management (Admin)
    Route::prefix('admin/reservations')->name('admin.reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'adminIndex'])->name('index');
        Route::get('/{reservation}', [ReservationController::class, 'adminShow'])->name('show');
        Route::post('/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('confirm');
        Route::post('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
        Route::post('/{reservation}/convert', [ReservationController::class, 'convertToContract'])->name('convert');
    });

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

    // Stripe Payments
    Route::get('/invoices/{invoice}/checkout', [StripePaymentController::class, 'checkout'])->name('invoices.checkout');
    Route::post('/invoices/{invoice}/payment-intent', [StripePaymentController::class, 'createPaymentIntent'])->name('invoices.payment-intent');
    Route::post('/payments/confirm', [StripePaymentController::class, 'confirmPayment'])->name('payments.confirm');
});

require __DIR__.'/auth.php';
