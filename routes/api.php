<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// routes/web.php
Route::get('auth/google', [SocialLoginController::class, 'auth_google']);
Route::get('auth/google/callback', [SocialLoginController::class, 'auth_google_callback']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::post('/user/initial_registration', [UserController::class, 'register']);
Route::post('/user/verify/code_to_register/{email}', [UserController::class, 'verify_code_to_register']);
Route::post('/user/fill_missed_info/{email}', [UserController::class, 'fill_missed_info']);

Route::get('/getVisitCount', [UserController::class, 'getVisitCount']);
Route::post('/incrementVisitCount', [UserController::class, 'incrementVisitCount']);
Route::get('/visit/Count/total', [UserController::class, 'getTotalVisits']);

Route::post('/user/forgot-password',[UserController::class,'submit_forgot_password']);

Route::post('/code_to_reset_pswd/{email}',[UserController::class,'code_to_reset_pswd']);

Route::post('/reset/password/{email}/{code}',[UserController::class,'resetPassword']);

//User/Seeker routes
Route::group(['prefix'=>'user' , 'middleware'=>'User'],function(){
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::get('/profile', [UserController::class, 'profile_picture']);
    Route::get('/view_info', [UserController::class, 'View_information']);
    Route::post('/update_info', [UserController::class, 'edit_info']);
    Route::post('/submit_job_category', [UserController::class, 'submitCategories']);
    Route::get('/fetch_user_job_categories', [UserController::class, 'fetch_user_job_Categories']);
    Route::get('/UserCount_job_category', [UserController::class, 'count_job_category']);
    Route::delete('/remove_job_category/{id}', [UserController::class, 'remove_job_category']);
    Route::post('/modify_password', [UserController::class, 'modify_password']);
});

//Admin routes
Route::group(['prefix'=>'adminauth' , 'middleware'=>'Admin'],function(){

});
