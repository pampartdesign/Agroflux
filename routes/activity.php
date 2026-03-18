<?php

use App\Http\Controllers\App\ActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.selected', 'tenant.active'])
  ->prefix('app')
  ->group(function () {
      Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');
  });
