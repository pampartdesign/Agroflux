<?php

use App\Http\Controllers\App\TenantMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant.selected', 'tenant.active', 'tenant.member'])->group(function () {
    Route::get('/org/members', [TenantMemberController::class, 'index'])->name('members.index');
    Route::post('/org/members', [TenantMemberController::class, 'store'])->name('members.store');
    Route::put('/org/members/{member}', [TenantMemberController::class, 'update'])->name('members.update');
    Route::delete('/org/members/{member}', [TenantMemberController::class, 'destroy'])->name('members.destroy');
});
