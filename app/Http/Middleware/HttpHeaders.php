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

        $response->header('Access-Control-Allow-Origin', 'admin.plunner.com');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Request-With');
        $response->header('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
