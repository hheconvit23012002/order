<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TestController;
use App\Http\Middleware\CheckAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/checkout', [CheckoutController::class,'checkout'])->middleware(CheckAuthMiddleware::class);
Route::post('/publish', [TestController::class,'publishMessage']);
Route::get('/consume', [TestController::class,'consumeMessage']);
