<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\AdminController;

// Route::post('/login', [AuthController::class, 'login']);

Route::group(['prefix'=>'admin' , 'middleware'=>'adminAuth'],function(){
    route::get('/dashboard',function(){
        return "admin dashboard !";
    });
});

Route::get('/', function () {
    return "Testing....";
});