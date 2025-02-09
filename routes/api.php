<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ForgotPasswordController;
 
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');


    Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.forgot');
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    
});

Route::group([
    'middleware' => 'auth:api'
], function(){
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/user/update', [AuthController::class, 'updateUser']);

    Route::post('/user/password', [AuthController::class, 'changePassword']);

    Route::post('/user/email', [AuthController::class, 'changeEmail']);
});