<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logistics\FarmerDeliveryController;

Route::middleware(['auth','tenant.selected','tenant.member'])->group(function () {
    Route::post('/logistics/requests/{requestModel}/publish', [FarmerDeliveryController::class, 'publish']);
    Route::post('/logistics/requests/{requestModel}/self-deliver', [FarmerDeliveryController::class, 'selfDeliver']);
    Route::post('/logistics/requests/{requestModel}/complete', [FarmerDeliveryController::class, 'complete']);
    Route::post('/logistics/requests/{requestModel}/cancel', [FarmerDeliveryController::class, 'cancel']);
});
