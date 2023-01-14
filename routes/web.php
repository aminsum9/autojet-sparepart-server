<?php

use Illuminate\Support\Facades\Route;
//controller
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DetailTransaksiController;
use App\Http\Controllers\WarehouseController;
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
    Route::post('/get_by_id', [UserController::class, 'get_user_by_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/get_users', [UserController::class, 'get_users'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [UserController::class, 'delete_user'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'barang'], function () {
    Route::post('/get_by_id', [BarangController::class, 'get_barang_by_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/get_barangs', [BarangController::class, 'get_barangs'])->middleware(EnsureTokenIsValid::class);
    Route::post('/add', [BarangController::class, 'add_barang'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update', [BarangController::class, 'update_barang'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [BarangController::class, 'delete_barang'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'supplier'], function () {
    Route::post('/get_by_id', [SupplierController::class, 'get_supplier_by_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/get_suppliers', [SupplierController::class, 'get_suppliers'])->middleware(EnsureTokenIsValid::class);
    Route::post('/add', [SupplierController::class, 'add_supplier'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update', [SupplierController::class, 'update_supplier'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [SupplierController::class, 'delete_supplier'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'transaksi'], function () {
    Route::post('/get_by_id', [TransaksiController::class, 'get_transaksi_by_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/get_transaksis', [TransaksiController::class, 'get_transaksis'])->middleware(EnsureTokenIsValid::class);
    Route::post('/create_transaksi', [TransaksiController::class, 'create_transaksi'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update', [TransaksiController::class, 'update_transaksi'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [TransaksiController::class, 'delete_transaksi'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'detail_transaksi'], function () {
    Route::post('/get_by_id', [DetailTransaksiController::class, 'get_detail_trans_by_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update', [DetailTransaksiController::class, 'update_detail_trans'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [DetailTransaksiController::class, 'delete_detail_trans'])->middleware(EnsureTokenIsValid::class);
});

Route::group(['prefix' => 'warehouse'], function () {
    Route::post('/get_by_id', [WarehouseController::class, 'get_warehouse_id'])->middleware(EnsureTokenIsValid::class);
    Route::post('/get_warehouses', [WarehouseController::class, 'get_warehouses'])->middleware(EnsureTokenIsValid::class);
    Route::post('/add', [WarehouseController::class, 'add_warehouse'])->middleware(EnsureTokenIsValid::class);
    Route::post('/update', [WarehouseController::class, 'update_warehouse'])->middleware(EnsureTokenIsValid::class);
    Route::post('/delete', [WarehouseController::class, 'delete_warehouse'])->middleware(EnsureTokenIsValid::class);
});
