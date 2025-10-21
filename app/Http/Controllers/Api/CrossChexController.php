<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
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

        if (empty($headers['nameSpace']) || empty($headers['nameAction']) || empty($headers['requestId'])) {
            Log::channel('crosschex')->warning('crosschex.bad_headers', [
                'headers' => $headers,
                'payload' => $payload,
            ]);
            return response()->json(['code' => '200', 'msg' => 'accepted_with_warnings']);
        }

        Log::channel('crosschex')->info('crosschex.event', [
            'ts'       => now()->toIso8601ZuluString(),
            'headers'  => $headers,
            'payload'  => $payload,
            'ip'       => $request->ip(),
            'ua'       => $request->userAgent(),
        ]);

        return response()->json(['code' => '200', 'msg' => 'success'], 200);
    }
}
