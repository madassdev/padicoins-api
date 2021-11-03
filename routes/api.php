<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
    Route::post('/login', 'LoginController@login');
});

Route::group(['namespace' => 'Admin', 'prefix'=>'admin', 'middleware' => ['auth:sanctum', 'role:admin']],function(){
    Route::apiResource('orders', 'OrderController');
});


Route::post('/orders', 'OrderController@order');
Route::post('/verification/bank', 'OrderController@verifyBank');
Route::get('/orders/callback/{track_id}', 'OrderController@orderCallback')->name('orders.callback');
Route::get('/orders/track/{track_id}', 'OrderController@trackOrder')->name('orders.track');
Route::get('/banks', 'OrderController@banks');
Route::get('/coins', 'OrderController@coins');
