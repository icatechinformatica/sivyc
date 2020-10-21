<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\supervision\Token;

class CheckToken
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
        $tmptoken = $request->header('tmptoken');
        if(!isset($tmptoken) || $tmptoken == null || strlen($tmptoken)<1){
            $tmptoken = $request->input('tmpToken');
        }
        if(!Token::where('tmp_token', $tmptoken)->exists()){
            return response()->json([
                "message" => "Invalid token"
              ], 401);
        }
        $urltoken = Token::where('tmp_token', $tmptoken)->first();
        $request->merge(['tmptoken' => $urltoken]);
        return $next($request);
    }
}
