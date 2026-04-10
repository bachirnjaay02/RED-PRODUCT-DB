<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Routes pour gérer les hôtels
Route::get('/hotels', [HotelController::class, 'index']);
Route::post('/hotels', [HotelController::class, 'store']);