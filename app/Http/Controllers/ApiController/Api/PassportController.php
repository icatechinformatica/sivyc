<?php

namespace App\Http\Controllers\ApiController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Validator;
use Laravel\Passport\Client as OClient;
use App\User;
use Carbon\Carbon;

class PassportController extends Controller
{
    public $successStatus = 200;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signUp(Request $request)
    {
        //
        $validator =  Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        /**
         * modificaciones
         */

        return response()->json([
            'message' => 'usuario creado exitosamente!'
        ], 201);
    }

    /**
     * login the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // validar los campos
        $loginData = $request->validate([
            'email' => 'email|required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            # modificaciones de una condicion
            return response()->json(['error'=>'No autorizado'], 401);
        } else {
            $usuario_auth = Auth::user();
            $token_result = $usuario_auth->createToken('MyAppToken');
            $token = $token_result->token;
            if ($request->remember_me) {
                # agregamos una semana de expiracion del token
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            $success['access_token'] =  $token_result->accessToken;
            $success['name'] = $usuario_auth->name;
            $success['email'] = $usuario_auth->email;
            $success['token_type'] = "Bearer";
            $success['expires_at'] = Carbon::parse($token->expires_at)->toDateTimeString();
            return response()->json(['success' => $success], $this->successStatus);
        }
    }

    // detalles del usuario con token
    public function details(Request $request)
    {
        $usuarioDetalles = Auth::user();
        return response()->json(['success' => $usuarioDetalles], $this->successStatus);
    }

    // salir del sistema de tokens
    public function logout()
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->tokens->find($id);
        $token->revoke();
        $response = 'Has sido desconectado';
        return response()->json(['success' => response], 200);
    }

}
