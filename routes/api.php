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

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::get('/analytics', 'AdminController@analytics');
    Route::post('wallets/{track_id}/payout', "WalletController@payout");
    Route::apiResource('wallets', 'WalletController');
    Route::apiResource('transactions', 'TransactionController');
});

Route::get('/banks', 'WalletController@banks');
Route::get('/coins', 'WalletController@coins');
Route::get('/rates', 'WalletController@rates');

Route::post('/verification/bank', 'WalletController@verifyBank');
Route::post('/wallets', 'WalletController@order');
Route::get('/wallets/track/{track_id}', 'WalletController@trackOrder')->name('wallets.track');

Route::get('/wallets/callback/{track_id}', 'WalletController@orderCallback')->name('wallets.callback');
Route::post('/wallets/callback/{track_id}', 'WalletController@orderCallback');

Route::post('/admin/wallets/callback/{track_id}', 'Controller@orderCallback')->middleware('auth:sanctum', 'role:admin');

