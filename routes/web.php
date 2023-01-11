<?php

use Illuminate\Support\Facades\Route;
//controller
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
//middleware
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

Route::group(['prefix' => 'user'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/check_login', [UserController::class, 'check_login'])->middleware(EnsureTokenIsValid::class);
    Route::post('/change_password', [UserController::class, 'change_password'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update',[UserController::class, 'update'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'barang'], function () {
    Route::post('/get_by_id', [BarangController::class, 'get_barang_by_id']);
    Route::post('/get_barangs', [BarangController::class, 'get_barangs']);
    Route::post('/add', [BarangController::class, 'add_barang']);
    Route::post('/update', [BarangController::class, 'update_barang'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [BarangController::class, 'delete_barang'])->middleware(EnsureTokenIsValid::class);
});
