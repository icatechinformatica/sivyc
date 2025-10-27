<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CrosschexLive;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\CarbonImmutable;

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

    \App\Models\CrosschexLive::create([
        'headers'     => $headers,
        'payload'     => $payload,
        'ip'          => $request->ip(),
        'user_agent'  => $request->userAgent(),
        // 👇 Guardamos el instante de recepción en UTC
        'received_at' => now(),
    ]);

    return response()->json(['code' => '200', 'msg' => 'success'], 200);
}


    public function index()
    {
        return view('tablero.crosschex.live');
    }

    public function metrics(Request $request)
    {
        $minutes = (int) $request->query('minutes', 60);
        if ($minutes < 1)  $minutes = 1;
        if ($minutes > 720) $minutes = 720;

        // ❶ Zona horaria de la app (local)
        $tz = config('app.timezone', 'UTC');

        // Serie por minuto en ZONA LOCAL: series + counts(local) + join
        $perMinute = DB::select("
            WITH series AS (
                SELECT gs AS bucket_local
                FROM generate_series(
                    date_trunc('minute', timezone(?, now()) - (interval '1 minute' * ?)),
                    date_trunc('minute', timezone(?, now())),
                    interval '1 minute'
                ) AS gs
            ),
            counts AS (
                SELECT
                  date_trunc('minute', timezone(?, received_at)) AS bucket_local,
                  COUNT(*) AS c
                FROM crosschex_live
                WHERE timezone(?, received_at) >= timezone(?, now()) - (interval '1 minute' * ?)
                GROUP BY 1
            )
            SELECT
              to_char(s.bucket_local, 'HH24:MI') AS label,
              COALESCE(c.c, 0)                  AS value
            FROM series s
            LEFT JOIN counts c ON c.bucket_local = s.bucket_local
            ORDER BY s.bucket_local
        ", [$tz, $minutes, $tz, $tz, $tz, $tz, $minutes]);

        // Top unidades (ventana local)
        $perUnidad = DB::select("
            SELECT
              COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
              ) AS unidad,
              COUNT(*) AS total
            FROM crosschex_live
            WHERE timezone(?, received_at) >= timezone(?, now()) - (interval '1 minute' * ?)
            GROUP BY 1
            ORDER BY total DESC
            LIMIT 10
        ", [$tz, $tz, $minutes]);

        // KPIs (totales del día dependen de tu necesidad; dejo total global y últimos 5m)
        $totals = DB::selectOne("
            SELECT
              COUNT(*) AS total_all,
              SUM(CASE WHEN timezone(?, received_at) >= timezone(?, now()) - interval '5 minutes' THEN 1 ELSE 0 END) AS total_5min
            FROM crosschex_live
        ", [$tz, $tz]);

        // Hora local del servidor (string ya formateado)
        $serverTimeLocal = DB::selectOne("SELECT to_char(timezone(?, now()), 'YYYY-MM-DD HH24:MI:SS') AS t", [$tz])->t;

        return response()->json([
            'windowMinutes'   => $minutes,
            'perMinute'       => $perMinute,
            'perUnidad'       => $perUnidad,
            'totals'          => $totals,
            'serverTimeLocal' => $serverTimeLocal,
        ]);
    }

    public function recent(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        if ($limit < 1)   $limit = 1;
        if ($limit > 500) $limit = 500;

        $tz = config('app.timezone', 'America/Mexico_City');

        $rows = DB::select("
            SELECT
            id,
            received_at,
            to_char(timezone(?, received_at), 'YYYY-MM-DD HH24:MI:SS') AS received_local,
            to_char(timezone(?, received_at), 'HH24:MI:SS')             AS received_time,
            payload->'records'->0->'employee'->>'workno'        AS workno,
            payload->'records'->0->'employee'->>'first_name'    AS first_name,
            payload->'records'->0->'employee'->>'last_name'     AS last_name,
            COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department','—'
            ) AS unidad,
            payload->'records'->0->'device'->>'name'            AS device_name,
            payload->'records'->0->'device'->>'serial_number'   AS serial_number,

            -- 🔽 check_time formateado en hora de Chiapas, sin zona
            to_char(
                timezone(?, (payload->'records'->0->>'check_time')::timestamptz),
                'YYYY-MM-DD HH24:MI:SS'
            ) AS check_time_local,

            payload->'records'->0->>'check_type'                AS check_type
            FROM crosschex_live
            ORDER BY received_at DESC
            LIMIT ?
        ", [$tz, $tz, $tz, $limit]);

        return response()->json([
            'rows'            => $rows,
            'serverTimeLocal' => DB::selectOne("SELECT to_char(timezone(?, now()), 'YYYY-MM-DD HH24:MI:SS') AS t", [$tz])->t,
        ]);
    }
}
