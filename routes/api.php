<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\UserController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=>'user' , 'middleware'=>'userAuth'],function(){
	route::get('/dashboard',function(){
		return "user dashboard !";
	});
});
