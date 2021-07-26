<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMiddleware
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
        $id_user= Auth::user()->id; //dd($id_user);
        $rol=  DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
        ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%admin%')
            ->value('roles.slug');//dd($rol);
        if ($rol) {
            return $next($request);
        }
        Auth::logout();
        return redirect('login')->withErrors('INGRESAR CON UN USUARIO ADMI');
    }
}
