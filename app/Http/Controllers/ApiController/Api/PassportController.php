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
//use App\User;
use App\Models\api\UsuarioSice;
use Carbon\Carbon;

class PassportController extends Controller
{
    public $successStatus = 200;

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        //
        $validator =  Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:user_sice',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        UsuarioSice::create([
            'user' => $request->name,
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
        //dd(Auth::guard('api-sice')->check());
        // validar los campos

        $loginData = Validator::make($request->all(), [
            'email' => 'email|required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($loginData->fails()) {
            # code...
            return response()->json(['errors'=>$validator->errors()->all()], 422);
        }

        $credentials = [
            'email'=> $request->email,
            'password'=> $request->password
        ];

        config(['auth.guards.api_sice.driver'=>'session']); 
        
        if (!Auth::guard('api_sice')->attempt($credentials)) {
            # modificaciones de una condicion
            return response()->json(['error'=>'No autorizado'], 422);
        } else {
            $usuario_auth = Auth::guard('api_sice')->user();
            $token_result = $usuario_auth->createToken('MyAppToken');
            $token = $token_result->token;
            if ($request->remember_me) {
                # agregamos una semana de expiracion del token
                $token->expires_at = Carbon::now()->addWeeks(1);
            }
            $token->save();
            $success['access_token'] =  $token_result->accessToken;
            $success['name'] = $usuario_auth->user;
            $success['email'] = $usuario_auth->email;
            $success['token_type'] = "Bearer";
            $success['expires_at'] = Carbon::parse($token->expires_at)->toDateTimeString();
            return response()->json($success, $this->successStatus);
        }
    }

    // detalles del usuario con token
    public function details(Request $request)
    {
        $usuarioDetalles = $request->user();
        return response()->json(['success' => $usuarioDetalles], $this->successStatus);
    }

    // salir del sistema de tokens
    public function logout(Request $request)
    {
        //$value = $request->bearerToken();
        //$id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->token();
        $token->revoke();
        $response = 'Has sido desconectado';
        return response()->json(['success' => $response], 200);
    }

}
