<?php

namespace App\Http\Controllers\ApiController\ApisMovil;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginMovil extends Controller {
    
    public function login(Request $request) {
        $correo = $request->email;
        $password = $request->password;
        $token = $request->token;

        $credentials = [
            'email' => $correo,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) { //si si esta registrado
            User::where('email', $correo)->update([ //se actualiza su token movil
                'token_movil' => $token
            ]);
            $data = User::select('id', 'name')->where('email', $correo)->first();
            return response()->json($data, 200);
        }

        return response()->json('noExiste', 200);
    }
}
