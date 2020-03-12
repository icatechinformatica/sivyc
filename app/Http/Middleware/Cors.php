<?php

namespace App\Http\Middleware;

use Closure;

class Cors
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
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('content-type:', 'application/json; charset=utf-8')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE', 'OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, x-auth, Authorization, Access-Control-Allow-Origin');
    }
}
