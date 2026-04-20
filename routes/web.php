<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
 
Route::get('/manifest.json', function () {
    return response()->json([
        'name' => 'Red Product',
        'short_name' => 'RedProduct',
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'icons' => []
    ]);
})->withoutMiddleware(['auth']);