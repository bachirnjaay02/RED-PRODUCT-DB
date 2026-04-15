<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;

// Ajoute cette ligne en haut, avant toutes les autres routes
Route::options('{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/activate/{token}', [AuthController::class, 'activate']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Routes pour gérer les hôtels
Route::get('/hotels', [HotelController::class, 'index']);
Route::post('/hotels', [HotelController::class, 'store']);


Route::get('/test-mail', function () {
    try {
        Mail::raw('Test email RED PRODUCT', function($m) {
            $m->to('bachirnjaay@icloud.com')
              ->subject('Test mail Laravel');
        });
        return response()->json(['message' => 'Mail envoyé !']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});