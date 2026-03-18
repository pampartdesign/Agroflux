<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Logistics\OfferController;

Route::post('/logistics/request/{requestModel}/offer',[OfferController::class,'store']);
Route::post('/logistics/offer/{offer}/accept',[OfferController::class,'accept']);
