<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureTokenIsValid;

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
    return view('welcome');
});

Route::group(['prefix' => 'user'], function ($router) {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/check_login', [UserController::class, 'check_login'])->middleware(EnsureTokenIsValid::class);
    Route::post('/change_password', [UserController::class, 'change_password'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update',[UserController::class, 'update'])->middleware(EnsureTokenIsValid::class);
});
