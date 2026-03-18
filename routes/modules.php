<?php

use App\Http\Controllers\Farm\CropTypeController;
use App\Http\Controllers\Farm\FarmDashboardController;
use App\Http\Controllers\Farm\FieldCropController;
use App\Http\Controllers\Farm\FarmRoutineController;
use App\Http\Controllers\Livestock\LivestockDashboardController;
use App\Http\Controllers\Livestock\StockManagementController;
use App\Http\Controllers\Livestock\ProduceManagementController;
use App\Http\Controllers\Livestock\LivestockRoutineController;
use App\Http\Controllers\Water\WaterDashboardController;
use App\Http\Controllers\Water\WaterResourcesController;
use App\Http\Controllers\Water\WeatherReportController;
use App\Http\Controllers\Equipment\EquipmentController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Drone\DroneDashboardController;
use App\Http\Controllers\Drone\DroneController;
use App\Http\Controllers\Drone\FieldBoundaryController;
use App\Http\Controllers\Drone\DroneMissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.selected'])->group(function () {

    // ── Farm Management ──────────────────────────────────────────────────────
    Route::prefix('farm')->name('farm.')->middleware('module.access:farm')->group(function () {
        Route::get('/', [FarmDashboardController::class, 'index'])->name('dashboard');

        Route::get('/fields', [FieldCropController::class, 'index'])->name('fields.index');
        Route::post('/fields', [FieldCropController::class, 'store'])->name('fields.store');
        Route::put('/fields/{field}', [FieldCropController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{field}', [FieldCropController::class, 'destroy'])->name('fields.destroy');

        Route::get('/crop-types', [CropTypeController::class, 'index'])->name('crop-types.index');
        Route::post('/crop-types', [CropTypeController::class, 'store'])->name('crop-types.store');
        Route::put('/crop-types/{cropType}', [CropTypeController::class, 'update'])->name('crop-types.update');
        Route::delete('/crop-types/{cropType}', [CropTypeController::class, 'destroy'])->name('crop-types.destroy');

        Route::get('/routine', [FarmRoutineController::class, 'index'])->name('routine.index');
        Route::post('/routine', [FarmRoutineController::class, 'store'])->name('routine.store');
        Route::patch('/routine/{task}/done', [FarmRoutineController::class, 'markDone'])->name('routine.done');
        Route::delete('/routine/{task}', [FarmRoutineController::class, 'destroy'])->name('routine.destroy');
    });

    // ── Livestock Management ─────────────────────────────────────────────────
    Route::prefix('livestock')->name('livestock.')->middleware('module.access:livestock')->group(function () {
        Route::get('/', [LivestockDashboardController::class, 'index'])->name('dashboard');

        Route::get('/stock', [StockManagementController::class, 'index'])->name('stock.index');
        Route::post('/stock', [StockManagementController::class, 'store'])->name('stock.store');
        Route::put('/stock/{animal}', [StockManagementController::class, 'update'])->name('stock.update');
        Route::delete('/stock/{animal}', [StockManagementController::class, 'destroy'])->name('stock.destroy');

        Route::get('/produce', [ProduceManagementController::class, 'index'])->name('produce.index');
        Route::post('/produce', [ProduceManagementController::class, 'store'])->name('produce.store');
        Route::delete('/produce/{log}', [ProduceManagementController::class, 'destroy'])->name('produce.destroy');

        Route::get('/routine', [LivestockRoutineController::class, 'index'])->name('routine.index');
        Route::post('/routine', [LivestockRoutineController::class, 'store'])->name('routine.store');
        Route::delete('/routine/{check}', [LivestockRoutineController::class, 'destroy'])->name('routine.destroy');
    });

    // ── Water Management ─────────────────────────────────────────────────────
    Route::prefix('water')->name('water.')->middleware('module.access:water')->group(function () {
        Route::get('/', [WaterDashboardController::class, 'index'])->name('dashboard');
        Route::get('/resources', [WaterResourcesController::class, 'index'])->name('resources.index');
        Route::post('/resources', [WaterResourcesController::class, 'store'])->name('resources.store');
        Route::put('/resources/{waterResource}', [WaterResourcesController::class, 'update'])->name('resources.update');
        Route::delete('/resources/{waterResource}', [WaterResourcesController::class, 'destroy'])->name('resources.destroy');
        Route::get('/weather', [WeatherReportController::class, 'index'])->name('weather.index');
    });

    // ── Equipment ────────────────────────────────────────────────────────────
    Route::prefix('equipment')->name('equipment.')->middleware('module.access:equipment')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
    });

    // ── Inventory ────────────────────────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->middleware('module.access:inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::put('/{inventoryItem}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventoryItem}', [InventoryController::class, 'destroy'])->name('destroy');
    });

    // ── Drones & Field Mapping ────────────────────────────────────────────────
    Route::prefix('drone')->name('drone.')->middleware('module.access:drone')->group(function () {
        Route::get('/', [DroneDashboardController::class, 'index'])->name('dashboard');

        // Drone fleet management
        Route::get('/drones', [DroneController::class, 'index'])->name('drones.index');
        Route::post('/drones', [DroneController::class, 'store'])->name('drones.store');
        Route::put('/drones/{drone}', [DroneController::class, 'update'])->name('drones.update');
        Route::delete('/drones/{drone}', [DroneController::class, 'destroy'])->name('drones.destroy');

        // Field boundary mapping
        Route::get('/fields', [FieldBoundaryController::class, 'index'])->name('fields.index');
        Route::get('/fields/map', [FieldBoundaryController::class, 'map'])->name('fields.map');
        Route::get('/fields/{boundary}/map', [FieldBoundaryController::class, 'map'])->name('fields.map.edit');
        Route::post('/fields', [FieldBoundaryController::class, 'store'])->name('fields.store');
        Route::put('/fields/{boundary}', [FieldBoundaryController::class, 'update'])->name('fields.update');
        Route::delete('/fields/{boundary}', [FieldBoundaryController::class, 'destroy'])->name('fields.destroy');

        // Mission planning
        Route::get('/missions', [DroneMissionController::class, 'index'])->name('missions.index');
        Route::get('/missions/plan', [DroneMissionController::class, 'plan'])->name('missions.plan');
        Route::get('/missions/{mission}/plan', [DroneMissionController::class, 'plan'])->name('missions.plan.edit');
        Route::post('/missions', [DroneMissionController::class, 'store'])->name('missions.store');
        Route::put('/missions/{mission}', [DroneMissionController::class, 'update'])->name('missions.update');
        Route::patch('/missions/{mission}/status', [DroneMissionController::class, 'updateStatus'])->name('missions.status');
        Route::delete('/missions/{mission}', [DroneMissionController::class, 'destroy'])->name('missions.destroy');
        Route::get('/missions/{mission}/export/{format}', [DroneMissionController::class, 'export'])->name('missions.export');
    });
});
