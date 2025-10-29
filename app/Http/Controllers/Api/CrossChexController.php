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
        // Extraer headers básicos del webhook
        $headers = [
            'nameSpace'      => $request->header('nameSpace'),
            'nameAction'     => $request->header('nameAction'),
            'version'        => $request->header('version'),
            'requestId'      => $request->header('requestId'),
            'content-type'   => $request->header('content-type'),
            'authorize-type' => $request->header('authorize-type'),
            'authorize-sign' => $request->header('authorize-sign'),
        ];

        // Cuerpo del webhook
        $payload = $request->json()->all();

        // ⚙️ Insertar el registro en crosschex_live
        // El trigger se encarga de:
        //  - Derivar check_time_utc desde payload
        //  - Calcular window_5m_id (ventana de 5 minutos)
        //  - Evitar duplicados por (workno, window_5m_id)
        DB::table('crosschex_live')->insertOrIgnore([
            'headers'     => json_encode($headers, JSON_UNESCAPED_UNICODE),
            'payload'     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'received_at' => now('UTC'), // guardamos hora UTC exacta de recepción
        ]);

        // ✅ Respuesta exacta que CrossChex espera
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

    public function punctuality(\Illuminate\Http\Request $request)
    {
        $tz = config('app.timezone', 'America/Mexico_City');

        $rows = \DB::select("
            WITH base AS (
            SELECT
                COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
                ) AS unidad,
                (timezone(?, (payload->'records'->0->>'check_time')::timestamptz))::time AS local_time,
                (timezone(?, (payload->'records'->0->>'check_time')::timestamptz))::date AS local_date
            FROM crosschex_live
            ),
            today AS (
            SELECT *
            FROM base
            WHERE local_date = (timezone(?, now()))::date
            ),
            counts AS (
            SELECT
                LOWER(TRIM(unidad)) AS unidad,
                SUM(
                CASE
                    WHEN (local_time BETWEEN time '07:40' AND time '08:15')
                    OR (local_time BETWEEN time '08:45' AND time '09:15')
                    THEN 1 ELSE 0 END
                ) AS ontime,
                SUM(
                CASE
                    WHEN (local_time BETWEEN time '08:16' AND time '08:30')
                    OR (local_time BETWEEN time '09:16' AND time '09:30')
                    THEN 1 ELSE 0 END
                ) AS late
            FROM today
            GROUP BY LOWER(TRIM(unidad))
            )
            SELECT
            u.unidad,
            COALESCE(u.total_personas_checan, 0)::int AS total,
            COALESCE(c.ontime, 0)::int AS ontime,
            COALESCE(c.late, 0)::int AS late,
            GREATEST(
                COALESCE(u.total_personas_checan, 0)
                - COALESCE(c.ontime, 0)
                - COALESCE(c.late, 0),
                0
            )::int AS missing
            FROM tbl_unidades u
            LEFT JOIN counts c
            ON LOWER(TRIM(c.unidad)) = LOWER(TRIM(u.unidad))
            WHERE
            u.unidad = u.ubicacion
            AND COALESCE(u.total_personas_checan, 0) > 0
            ORDER BY u.unidad ASC
        ", [$tz, $tz, $tz]);

        $data = array_map(fn($r) => [
            'unidad'  => $r->unidad,
            'total'   => (int) $r->total,
            'ontime'  => (int) $r->ontime,
            'late'    => (int) $r->late,
            'missing' => (int) $r->missing,
        ], $rows);

        $serverTimeLocal = \DB::selectOne(
            "SELECT to_char(timezone(?, now()), 'YYYY-MM-DD HH24:MI:SS') AS t", [$tz]
        )->t;

        return response()->json([
            'serverTimeLocal' => $serverTimeLocal,
            'items'           => $data,
        ]);
    }

    public function punctualityList(Request $request)
    {
        $unidad = strtoupper(trim($request->query('unidad', '')));
        $type   = $request->query('type', 'ontime'); // ontime | late
        $tz     = config('app.timezone', 'America/Mexico_City');

        // Ventanas de tiempo
        // a tiempo: 07:40–08:15 y 08:45–09:15
        // retardo: 08:16–08:30 y 09:16–09:30
        $whereTime =
            ($type === 'late')
            ? " (t::time BETWEEN time '08:16' AND time '08:30'
                OR t::time BETWEEN time '09:16' AND time '09:30') "
            : " (t::time BETWEEN time '07:40' AND time '08:15'
                OR t::time BETWEEN time '08:45' AND time '09:15') ";

        $rows = DB::select("
            WITH base AS (
            SELECT
                UPPER(TRIM(COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
                ))) AS unidad_norm,
                timezone(?, (payload->'records'->0->>'check_time')::timestamptz) AS t,  -- local timestamp
                payload
            FROM crosschex_live
            ),
            today AS (
            SELECT unidad_norm, t::date AS d, t::time AS tt, t AS ts, payload
            FROM base
            WHERE t::date = (timezone(?, now()))::date
            )
            SELECT
            payload->'records'->0->'employee'->>'workno'         AS workno,
            CONCAT_WS(' ',
                payload->'records'->0->'employee'->>'first_name',
                payload->'records'->0->'employee'->>'last_name'
            )                                                   AS full_name,
            payload->'records'->0->'device'->>'name'            AS device_name,
            payload->'records'->0->'device'->>'serial_number'   AS serial_number,
            to_char(ts, 'YYYY-MM-DD HH24:MI:SS')                 AS check_time_local,
            payload->'records'->0->>'check_type'                AS check_type
            FROM today
            WHERE unidad_norm = ?
            AND {$whereTime}
            ORDER BY ts ASC
        ", [$tz, $tz, $unidad]);

        return response()->json([
            'unidad' => $unidad,
            'type'   => $type,
            'items'  => $rows,
        ]);
    }

}
