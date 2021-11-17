<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(root_redirect());
    return "Hello and welcome to Padicoins";
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test', function () {
    return inertia('Test');
});
Route::name('admin.')->namespace('Admin')->prefix('admin')->middleware(["auth", "role:admin"])->group(function () {
    Route::get('/', 'AdminController@index');
    Route::get('orders/{track_id}', 'OrderController@show')->name('wallets.show');
});

// ssh padicoins@161.97.131.97
// cp -a ./public/. ./public_html

require __DIR__ . '/auth.php';
