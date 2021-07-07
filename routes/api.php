<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix'=>'user'],function(){
    Route::post('register',[userController::class,'create']);
    Route::get('verifi/{token}',[userController::class,'userVerification']);
    Route::post('login',[userController::class,'handleLogin']);
    Route::middleware('auth:api')->get('me',[userController::class,'checkInfo']);
    Route::middleware('auth:api')->get('resend',[userController::class,'resendEmail']);
    Route::get('forgetPassword',[userController::class,'forgetPassword']);
});

