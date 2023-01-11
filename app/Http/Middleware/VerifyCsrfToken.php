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
        'user/register',
        'user/check_login',
        'user/change_password',
        'user/update',
        //barang
        'barang/get_by_id',
        'barang/get_barangs',
        'barang/add',
        'barang/update',
        'barang/delete',
    ];
}
