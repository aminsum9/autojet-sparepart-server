<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //user
        'user/login',
        'user/get_users',
        'user/register',
        'user/check_login',
        'user/change_password',
        'user/update',
        'user/delete',
        //barang
        'barang/get_by_id',
        'barang/get_barangs',
        'barang/add',
        'barang/update',
        'barang/delete',
        //supplier
        'supplier/get_by_id',
        'supplier/get_suppliers',
        'supplier/add',
        'supplier/update',
        'supplier/delete',
        //transaksi
        'transaksi/get_by_id',
        'transaksi/get_transaksis',
        'transaksi/create_transaksi',
        'transaksi/update',
        'transaksi/delete',
        //detail transaksi
        'detail_transaksi/get_by_id',
        'detail_transaksi/update',
        'detail_transaksi/delete',
        //warehouse
        'warehouse/get_by_id',
        'warehouse/get_warehouses',
        'warehouse/add',
        'warehouse/update',
        'warehouse/delete',
    ];
}
