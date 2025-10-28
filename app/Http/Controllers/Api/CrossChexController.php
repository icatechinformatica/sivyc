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

        $localTz = config('app.timezone', 'America/Mexico_City');

        // 1) “Momento de llegada” en hora local (-06)
        $arrivalLocal = CarbonImmutable::now($localTz);          // 2025-10-27T13:11:27-06:00

        // 2) El MISMO instante en UTC para persistir (estándar con timestamptz)
        $arrivalUtc   = $arrivalLocal->utc();                    // 2025-10-27T19:11:27+00:00

        \App\Models\CrosschexLive::create([
            'headers'     => $headers,
            'payload'     => $payload,
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'received_at' => $arrivalUtc->toIso8601String(),     // ✅ UTC real del instante
        ]);

        return response()->json(['code' => '200', 'msg' => 'success'], 200);
    }


    public function index()
    {
        return view('tablero.crosschex.live');
    }

    public function metrics(\Illuminate\Http\Request $request)
    {
        $minutes = (int) $request->query('minutes', 60);
        $minutes = max(1, min($minutes, 720));

        $tz = config('app.timezone', 'America/Mexico_City');

        // Serie y conteos en la MISMA referencia: (now() AT TIME ZONE tz) y (received_at AT TIME ZONE tz)
        $perMinute = \DB::select("
            WITH
            local_now AS (
                SELECT now() AT TIME ZONE ? AS t   -- timestamp (sin tz) en hora local
            ),
            series AS (
                SELECT generate_series(
                    date_trunc('minute', (SELECT t FROM local_now) - (interval '1 minute' * ?)),
                    date_trunc('minute', (SELECT t FROM local_now)),
                    interval '1 minute'
                ) AS bucket_local
            ),
            counts AS (
                SELECT
                date_trunc('minute', received_at AT TIME ZONE ?) AS bucket_local,
                COUNT(*) AS c
                FROM crosschex_live
                WHERE (received_at AT TIME ZONE ?) >= (SELECT t FROM local_now) - (interval '1 minute' * ?)
                GROUP BY 1
            )
            SELECT
            to_char(s.bucket_local, 'HH24:MI') AS label,
            COALESCE(c.c, 0)                   AS value
            FROM series s
            LEFT JOIN counts c USING (bucket_local)
            ORDER BY s.bucket_local
        ", [$tz, $minutes, $tz, $tz, $minutes]);

        // Top unidades en la misma ventana local
        $perUnidad = \DB::select("
            WITH local_now AS (
                SELECT now() AT TIME ZONE ? AS t
            )
            SELECT
            COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
            ) AS unidad,
            COUNT(*) AS total
            FROM crosschex_live
            WHERE (received_at AT TIME ZONE ?) >= (SELECT t FROM local_now) - (interval '1 minute' * ?)
            GROUP BY 1
            ORDER BY total DESC
            LIMIT 10
        ", [$tz, $tz, $minutes]);

        // KPIs en local
        $totals = \DB::selectOne("
            WITH local_now AS ( SELECT now() AT TIME ZONE ? AS t )
            SELECT
            COUNT(*) AS total_all,
            SUM(CASE WHEN (received_at AT TIME ZONE ?) >= (SELECT t FROM local_now) - interval '5 minutes'
                    THEN 1 ELSE 0 END) AS total_5min
            FROM crosschex_live
        ", [$tz, $tz]);

        // Hora del servidor en local (string)
        $serverTimeLocal = \DB::selectOne("
            SELECT to_char(now() AT TIME ZONE ?, 'YYYY-MM-DD HH24:MI:SS') AS t
        ", [$tz])->t;

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

        $rows = \DB::select("
            SELECT
                id,
                received_at,
                to_char(timezone(?, received_at), 'HH24:MI:SS')             AS received_time,   -- 👈 Recibido (solo hora)
                to_char(
                timezone(?, (payload->'records'->0->>'check_time')::timestamptz),
                'YYYY-MM-DD HH24:MI:SS'
                )                                                           AS check_time_local, -- 👈 Check time formateado local
                payload->'records'->0->'employee'->>'workno'                AS workno,
                payload->'records'->0->'employee'->>'first_name'            AS first_name,
                payload->'records'->0->'employee'->>'last_name'             AS last_name,
                COALESCE(payload->'records'->0->'employee'->>'department',
                        payload->'employee'->>'department','—')            AS unidad,
                payload->'records'->0->'device'->>'name'                    AS device_name,
                payload->'records'->0->'device'->>'serial_number'           AS serial_number,
                payload->'records'->0->>'check_type'                        AS check_type
            FROM crosschex_live
            ORDER BY received_at DESC
            LIMIT ?
            ", [$tz, $tz, $limit]);

        $serverTimeLocal = \DB::selectOne("SELECT to_char(timezone(?, now()), 'YYYY-MM-DD HH24:MI:SS') AS t", [$tz])->t;

        return response()->json([
            'rows'            => $rows,
            'serverTimeLocal' => $serverTimeLocal,
        ]);
    }
}
