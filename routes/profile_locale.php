<?php

use App\Http\Controllers\ProfileLocaleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth'])->group(function () {
    Route::put('/profile/locale', [ProfileLocaleController::class, 'update'])->name('profile.locale.update');
});
