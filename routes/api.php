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


Route::group(['prefix' => 'auth'], function () {
    Route::get('/', 'UserController@index');
});

Route::post('/orders', 'OrderController@order');
Route::post('/verification/bank', 'OrderController@verifyBank');
Route::get('/orders/callback/{track_id}', 'OrderController@orderCallback')->name('orders.callback');
Route::post('/seed', 'OrderController@seed');
