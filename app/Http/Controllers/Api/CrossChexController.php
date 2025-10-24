<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CrosschexLive;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

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


    public function index()
    {
        return view('tablero.crosschex.live');
    }

    public function metrics(\Illuminate\Http\Request $request)
    {
        $minutes = (int) $request->query('minutes', 60);
        if ($minutes < 1)  $minutes = 1;
        if ($minutes > 720) $minutes = 720;

        // 1) Serie de minutos
        // 2) Conteos por minute bucket (date_trunc) dentro de la ventana
        // 3) Join serie + conteos
        $perMinute = \DB::select("
            WITH series AS (
                SELECT gs AS bucket
                FROM generate_series(
                    date_trunc('minute', now() - (interval '1 minute' * ?)),
                    date_trunc('minute', now()),
                    interval '1 minute'
                ) AS gs
            ),
            counts AS (
                SELECT date_trunc('minute', received_at) AS bucket, COUNT(*) AS c
                FROM crosschex_live
                WHERE received_at >= now() - (interval '1 minute' * ?)
                GROUP BY 1
            )
            SELECT to_char(s.bucket, 'HH24:MI') AS label,
                COALESCE(c.c, 0)            AS value
            FROM series s
            LEFT JOIN counts c ON c.bucket = s.bucket
            ORDER BY s.bucket
        ", [$minutes, $minutes]);

        $perUnidad = \DB::select("
            SELECT
            COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
            ) AS unidad,
            COUNT(*) AS total
            FROM crosschex_live
            WHERE received_at >= now() - (interval '1 minute' * ?)
            GROUP BY 1
            ORDER BY total DESC
            LIMIT 10
        ", [$minutes]);

        $totals = \DB::selectOne("
            SELECT
            COUNT(*) AS total_all,
            SUM(CASE WHEN received_at >= now() - interval '5 minutes' THEN 1 ELSE 0 END) AS total_5min
            FROM crosschex_live
        ");

        return response()->json([
            'windowMinutes' => $minutes,
            'perMinute'     => $perMinute,
            'perUnidad'     => $perUnidad,
            'totals'        => $totals,
            'serverTime'    => now()->toIso8601String(),
        ]);
    }

    public function recent(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        if ($limit < 1) $limit = 1;
        if ($limit > 500) $limit = 500;

        $rows = DB::select("
            SELECT
              id,
              received_at,
              payload->'records'->0->'employee'->>'workno'        AS workno,
              payload->'records'->0->'employee'->>'first_name'    AS first_name,
              payload->'records'->0->'employee'->>'last_name'     AS last_name,
              COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department','—'
              ) AS unidad,
              payload->'records'->0->'device'->>'name'            AS device_name,
              payload->'records'->0->'device'->>'serial_number'   AS serial_number,
              payload->'records'->0->>'check_time'                AS check_time,
              payload->'records'->0->>'check_type'                AS check_type
            FROM crosschex_live
            ORDER BY received_at DESC
            LIMIT ?
        ", [$limit]);

        return response()->json([
            'rows'       => $rows,
            'serverTime' => now()->toIso8601String(),
        ]);
    }
}
