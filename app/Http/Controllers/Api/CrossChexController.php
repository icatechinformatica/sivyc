<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CrosschexLive;
use Illuminate\Http\JsonResponse;

class CrossChexController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $headers = [
            'nameSpace'      => $request->header('nameSpace'),
            'nameAction'     => $request->header('nameAction'),
            'version'        => $request->header('version'),
            'requestId'      => $request->header('requestId'),
            'content-type'   => $request->header('content-type'),
            'authorize-type' => $request->header('authorize-type'),
            'authorize-sign' => $request->header('authorize-sign'),
        ];

        $payload = $request->json()->all();

        // Guardar en BD
        CrosschexLive::create([
            'headers'     => $headers,
            'payload'     => $payload,
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'received_at' => now(),
        ]);

        // Devuelve la respuesta esperada por CrossChex
        return response()->json(['code' => '200', 'msg' => 'success'], 200);
    }
}
