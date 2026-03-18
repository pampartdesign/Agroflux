<?php

use App\Http\Controllers\MediaLibraryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web','auth','tenant.selected'])->group(function () {
    Route::get('/media', [MediaLibraryController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaLibraryController::class, 'upload'])->name('media.upload');
});
