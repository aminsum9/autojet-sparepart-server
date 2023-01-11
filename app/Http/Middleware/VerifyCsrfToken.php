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
        //product
        'product/get_by_id',
        'product/get_products',
        'product/add',
        'product/update',
        'product/delete',
    ];
}
