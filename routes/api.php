<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// routes/web.php
Route::get('auth/google', [SocialLoginController::class, 'auth_google']);
Route::get('auth/google/callback', [SocialLoginController::class, 'auth_google_callback']);

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::get('/test_user_data', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'age' => 30
    ];
    
    return response()->json($userData);
});