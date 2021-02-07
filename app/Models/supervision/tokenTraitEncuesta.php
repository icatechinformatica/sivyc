<?php

namespace App\Models\supervision;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\supervision\tokenEncuesta;

trait tokenTraitEncuesta
{

    protected function TokenExpired($urltoken)
    {
          $currentTime = time();
          $token = tokenEncuesta::where('url_token', $urltoken)->first();
          if($token->url_token != null && $currentTime>$token->ttl) {
               //ELIMINANDO TOKEN CADUCADO
               //$token->delete();
               return true;
          }
    }

    protected function generateTmpToken($urltoken)
    {

          $currentTime = time();
          if(!tokenEncuesta::where('url_token', $urltoken)->exists()){
                return null;
          }
          $token = tokenEncuesta::where('url_token', $urltoken)->first();
          if($currentTime > $token->ttl) {
            //ELIMINANDO TOKEN CADUCADO
              //$token->delete();
              return null;
          }
          if($token->tmp_token != null && $token->ttl > $currentTime) {
              /* PARA LOS CASOS QUE SE DESEE QUE NO SE ABRA EN OTRO NAVEGADOR
              if($request->session()->has('tmpToken')) {
                    $tmptoken = $request->session()->get('tmpToken');
                    if($tmptoken == $token->tmp_token ){
                        return $token;
                    }
              }
              return null;*/
              return $token;
          }
          $token->tmp_token = hash('sha256', Str::random(60));
          $token->save();
          return $token;
    }
}
