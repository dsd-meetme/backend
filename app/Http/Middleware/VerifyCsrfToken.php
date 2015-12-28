<?php

namespace plunner\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Support\Str;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    protected function tokensMatch($request)
    {

        // Don't validate CSRF when testing.
        if (env('APP_ENV') === 'testing') {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
