<?php

namespace plunner\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Log;

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
        if(env('APP_ENV') === 'testing') {
            return true;
        }


        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (! $token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header);
        }

        Log::info('Sent token: '.$token);
        Log::info('Stored token: '.$request->session()->token());

        return Str::equals($request->session()->token(), $token);

        //return parent::tokensMatch($request);
    }
}
