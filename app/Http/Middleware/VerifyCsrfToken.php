<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * Midtrans webhook needs to be excluded because it sends POST requests
     * from external servers that don't have our CSRF token.
     *
     * @var array<int, string>
     */
    protected $except = [
        'midtrans/notification',
        'midtrans/*',
    ];
}
