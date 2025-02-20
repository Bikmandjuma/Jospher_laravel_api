<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\AdminController;

// Route::post('/login', [AuthController::class, 'login']);

//Admin routes
Route::group(['prefix'=>'admin' , 'middleware'=>'adminAuth','throttle:100,1'],function(){
    Route::get('/dashboard', [AdminController::class, 'home'])->name('admin.dashboard');
    Route::get('/view_info', [AdminController::class, 'View_information']);
    Route::get('/count_seekers', [AdminController::class, 'count_seekers']);
    Route::get('/count_seekers_today', [AdminController::class, 'count_seekers_today']);
    Route::post('/update_password', [AdminController::class, 'update_password']);
    Route::post('/update_info', [AdminController::class, 'edit_info']);
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('admin.logout');
    
});
Route::get('/refresh_counts', [AdminController::class, 'refresh_counts'])->name('admin.refresh_counts');

Route::get('/', [WebAuthController::class, 'login_form'])->name('admin.login');
Route::post('/submit_login', [WebAuthController::class, 'submit_login'])->name('admin.submit.login');
