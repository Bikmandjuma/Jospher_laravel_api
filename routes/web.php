<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\AdminController;

//Admin routes
Route::group(['prefix'=>'owner' , 'middleware'=>'ownerAuth','throttle:100,1'],function(){
    
    Route::get('/dashboard', [AdminController::class, 'home'])->name('owner.dashboard');
    
    Route::get('/view_info', [AdminController::class, 'View_information']);
    
    Route::get('/count_seekers', [AdminController::class, 'count_seekers']);
    
    Route::get('/count_seekers_today', [AdminController::class, 'count_seekers_today']);
    
    Route::post('/update_password', [AdminController::class, 'update_password']);
    
    Route::post('/update_info', [AdminController::class, 'edit_info']);
    
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('owner.logout');
    
    Route::get('/display_paid_users', [AdminController::class, 'display_paid_users'])->name('owner.display_paid_users');

    Route::get('/view_all_users',[AdminController::class,'view_all_users'])->name('owner.view_all_users');

    Route::get('/view_all_users_joined_today',[AdminController::class,'view_all_users_joined_today'])->name('owner.view_all_users_joined_today');
    
    Route::get('/search_users_payment', [AdminController::class, 'search_users_payment'])->name('owner.search_users_payment');

    Route::get('/assign_payment_ToUser/{id}', [AdminController::class, 'assign_payment_ToUser'])->name('owner.assign_payment_ToUser');

    // Route::get('/assign_payment_ToUser/{id}', [AdminController::class, 'assign_payment_ToUser'])->name('admin.assign_payment_ToUser');

    Route::post('/submit_payment_ToUser/{id}', [AdminController::class, 'submit_payment_ToUser'])->name('owner.submit_payment_ToUser');
});
Route::get('/refresh_counts', [AdminController::class, 'refresh_counts'])->name('owner.refresh_counts');

Route::get('/', [WebAuthController::class, 'login_form'])->name('owner.login');
Route::post('/submit_login', [WebAuthController::class, 'submit_login'])->name('owner.submit.login');
