<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logi\LogiDashboardController;
use App\Http\Controllers\Logi\DeliveryRequestController;
use App\Http\Controllers\Logi\AvailableRequestsController;
use App\Http\Controllers\Logi\DeliveryOffersController;
use App\Http\Controllers\Logi\TruckerProfileController;

/*
|--------------------------------------------------------------------------
| LogiTrace Routes (AgroFlux)
|--------------------------------------------------------------------------
| Optional logistics marketplace:
| - Farmers can publish delivery requests to receive offers, OR deliver themselves.
| - Truckers can create a profile and send offers.
|
| Notes:
| - Farmer actions require tenant selection (tenant.selected middleware).
| - Trucker actions do NOT require tenant selection.
*/

Route::middleware(['web', 'auth'])
    ->prefix('logi')
    ->name('logi.')
    ->group(function () {

        Route::get('/', [LogiDashboardController::class, 'index'])->name('dashboard');

        // Sub-pages (placeholder — content TBD)
        Route::get('/pickup-requests', [LogiDashboardController::class, 'pickupRequests'])->name('pickup.index');
        Route::get('/shipments', [LogiDashboardController::class, 'shipments'])->name('shipments.index');
        Route::get('/route-planning', [LogiDashboardController::class, 'routePlanning'])->name('route_planning.index');

        // Trucker profile (no tenant required)
        Route::get('/trucker-profile', [TruckerProfileController::class, 'edit'])->name('trucker.profile.edit');
        Route::post('/trucker-profile', [TruckerProfileController::class, 'update'])->name('trucker.profile.update');

        // Trucker: browse open requests & send offers (no tenant required)
        Route::get('/available-requests', [AvailableRequestsController::class, 'index'])->name('available.index');
        Route::get('/available-requests/{request}', [AvailableRequestsController::class, 'show'])->name('available.show');
        Route::post('/available-requests/{request}/offer', [DeliveryOffersController::class, 'store'])->name('offers.store');

        // Trucker: my offers
        Route::get('/my-offers', [DeliveryOffersController::class, 'mine'])->name('offers.mine');

        // Farmer (tenant required)
        // Trucker: mark own accepted delivery as completed (no tenant required)
        Route::post('/requests/{request}/trucker-complete', [DeliveryRequestController::class, 'truckerComplete'])->name('requests.trucker_complete');

        Route::middleware(['tenant.selected'])
            ->group(function () {
                Route::get('/requests', [DeliveryRequestController::class, 'index'])->name('requests.index');
                Route::get('/requests/create', [DeliveryRequestController::class, 'create'])->name('requests.create');
                Route::post('/requests', [DeliveryRequestController::class, 'store'])->name('requests.store');
                Route::get('/requests/{request}', [DeliveryRequestController::class, 'show'])->name('requests.show');

                Route::post('/requests/{request}/publish', [DeliveryRequestController::class, 'publish'])->name('requests.publish');
                Route::post('/requests/{request}/self-delivered', [DeliveryRequestController::class, 'selfDelivered'])->name('requests.self_delivered');
                Route::post('/requests/{request}/cancel', [DeliveryRequestController::class, 'cancel'])->name('requests.cancel');
                Route::post('/requests/{request}/complete', [DeliveryRequestController::class, 'complete'])->name('requests.complete');

                Route::post('/requests/{request}/accept-offer/{offer}', [DeliveryRequestController::class, 'acceptOffer'])->name('requests.accept_offer');
            });
    });
