<?php

use App\Http\Controllers\App\NotificationsController;
use Illuminate\Support\Facades\Route;

// Notifications are per-user — no tenant context required.
Route::middleware(['auth'])
    ->prefix('app')
    ->group(function () {
        Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationsController::class, 'markAllRead'])->name('notifications.read_all');
        Route::post('/notifications/{id}/read', [NotificationsController::class, 'markRead'])->name('notifications.read');
    });
