<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// routes/web.php
Route::get('auth/google', [SocialLoginController::class, 'auth_google']);
Route::get('auth/google/callback', [SocialLoginController::class, 'auth_google_callback']);

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::post('/user/initial_registration', [UserController::class, 'register']);

//start of Seeker's api routes
Route::middleware(['auth:api'])->group(function () {
    Route::get('/seeker/dashboard', [UserController::class, 'dashboard']);
    Route::get('/seeker/profile', [UserController::class, 'profile_picture']);
    Route::get('/seeker/view_info', [UserController::class, 'View_information']);
    Route::post('/seeker/update_info', [UserController::class, 'edit_info']);
});
//end of Seeker's api routes