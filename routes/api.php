<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InsuranceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Insurance API Routes
|--------------------------------------------------------------------------
|
| These routes provide API access to insurance products and contract
| insurances for mobile applications or external integrations.
|
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Insurance Products
    Route::get('/insurance-products', [InsuranceController::class, 'index'])
        ->name('api.insurance-products.index');
    Route::get('/insurance-products/{product}', [InsuranceController::class, 'show'])
        ->name('api.insurance-products.show');

    // Contract Insurances
    Route::get('/contracts/{contract}/insurances', [InsuranceController::class, 'contractInsurances'])
        ->name('api.contracts.insurances.index');
    Route::post('/contracts/{contract}/insurances', [InsuranceController::class, 'addToContract'])
        ->name('api.contracts.insurances.store');
    Route::delete('/contracts/{contract}/insurances/{insurance}', [InsuranceController::class, 'cancelFromContract'])
        ->name('api.contracts.insurances.destroy');
});
