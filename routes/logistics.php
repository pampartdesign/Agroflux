<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logistics\FarmerDeliveryController;
use App\Http\Controllers\Logistics\TruckerController;
Route::middleware(['auth','tenant.selected','tenant.member'])->group(function(){
 Route::get('/logistics/requests',[FarmerDeliveryController::class,'index']);
 Route::post('/logistics/requests',[FarmerDeliveryController::class,'store']);
 Route::get('/logistics/market',[TruckerController::class,'market']);
});