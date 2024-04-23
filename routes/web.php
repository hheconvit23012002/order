<?php

use App\Http\Controllers\TestController;
use App\Http\Middleware\CheckAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
//Route::post('/test', function (){
//    dd(1);
//})->middleware(CheckAuthMiddleware::class);
//Route::post('/publish', [TestController::class,'publishMessage']);
//Route::get('/consume', [TestController::class,'consumeMessage']);
