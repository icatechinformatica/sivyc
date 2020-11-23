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
use Exception;

class PassportController extends Controller
{

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

        $input = $request->all();
    	$input['passcode'] = bcrypt($request->get('passcode'));
        $input['activo'] = $request->get('activo');
    	$user = User::create($input);
    	$token = $user->createToken('MyApp')->accessToken;

        /**
         * modificaciones
         */
        DB::table('users')->insert([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Successfully created user!'
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
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request>get('password')])) {
            # modificaciones de una condicion
            $usuario_auth = Auth::user();
            $success['token'] =  $usuario_auth->createToken('MyAppToken')->accessToken;
            $success['name'] = $usuario_auth->name;
            $success['email'] = $usuario_auth->email;
            return response()->json(['success' => $success], $this->successStatus);
        } else {
            return response()->json(['error'=>'No autorizado'], 401);
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
