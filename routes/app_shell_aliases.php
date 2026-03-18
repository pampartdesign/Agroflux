<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // If Plus monitoring route isn't implemented yet, fall back to simulator.
    Route::get('/plus/iot/dashboard', function () {
        return redirect('/plus/iot/simulator');
    })->name('plus.iot.dashboard.fallback');
});
