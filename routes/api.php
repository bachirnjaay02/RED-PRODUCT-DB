<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail; // ✅ Ajouter cet import



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

Route::get('/hotels', [HotelController::class, 'index']);
Route::post('/hotels', [HotelController::class, 'store']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']); // ✅ Nouveau endpoint pour vérifier le code d'activation

// ✅ Route test mail
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
// route mail avec brevo
Route::get('/test-mail-brevo', function () {
    try {
        Mail::raw('Test email RED PRODUCT via Brevo', function($m) {
            $m->to('bachirndiaye233@gmail.com')
              ->subject('Test mail Laravel via Brevo');
        });
        return response()->json(['message' => 'Mail envoyé !']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});


Route::options('{any}', function() {
    return response()->json([], 200);
})->where('any', '.*');