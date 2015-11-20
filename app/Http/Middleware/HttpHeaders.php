<?php

namespace plunner\Http\Middleware;

use Closure;

class HttpHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With, X-XSRF-TOKEN, X-CSRF-TOKEN');
        $response->header('Access-Control-Allow-Credentials', 'true');
        $response->header('Access-Control-Max-Age', '1000');
        $response->header('Cache-Control', 'no-cache, must-revalidate');
        $response->header('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');

        return $response;
    }
}
