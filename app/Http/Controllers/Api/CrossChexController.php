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

    public function punctuality(Request $request)
    {
        $tz = config('app.timezone', 'America/Mexico_City');

        $rows = DB::select("
            WITH base AS (
            SELECT
                COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
                ) AS unidad_raw,
                timezone(?, (payload->'records'->0->>'check_time')::timestamptz) AS t -- local timestamptz
            FROM crosschex_live
            ),
            today AS (
            SELECT
                UPPER(TRIM(unidad_raw)) AS unidad_norm,
                t::date AS d,
                t::time AS local_time
            FROM base
            WHERE t::date = (timezone(?, now()))::date
            ),
            counts AS (
                SELECT
                    unidad_norm,
                    SUM(
                        CASE
                            -- A TIEMPO: 07:40:00–08:15:59 y 08:45:00–09:15:59
                            WHEN (
                                local_time >= time '07:40:00' AND local_time < time '08:16:00'
                            ) OR (
                                local_time >= time '08:45:00' AND local_time < time '09:16:00'
                            )
                            THEN 1 ELSE 0
                        END
                    ) AS ontime,
                    SUM(
                        CASE
                            -- RETARDO: 08:16:00–08:30:59 y 09:16:00–09:30:59
                            WHEN (
                                local_time >= time '08:16:00' AND local_time < time '08:31:00'
                            ) OR (
                                local_time >= time '09:16:00' AND local_time < time '09:31:00'
                            )
                            THEN 1 ELSE 0
                        END
                    ) AS late
                FROM today
                GROUP BY unidad_norm
            ),
            -- Unidades principales (texto) normalizadas
            principal AS (
            SELECT DISTINCT ON (UPPER(TRIM(u.unidad)))
                u.id                                              AS id_unidad,
                UPPER(TRIM(u.unidad))                             AS unidad_norm,
                u.unidad                                          AS unidad_display
            FROM tbl_unidades u
            WHERE UPPER(TRIM(u.unidad)) = UPPER(TRIM(u.ubicacion))
            ORDER BY UPPER(TRIM(u.unidad)), u.id
            ),
            -- Totales reales por unidad desde tbl_funcionario
            totals AS (
            SELECT f.id_unidad, COUNT(*)::int AS total
            FROM tbl_funcionario f
            WHERE f.status = true AND f.checado = true
            GROUP BY f.id_unidad
            )
            SELECT
            p.unidad_display                                          AS unidad,
            COALESCE(t.total, 0)                                      AS total,
            COALESCE(c.ontime, 0)::int                                AS ontime,
            COALESCE(c.late,   0)::int                                AS late,
            GREATEST(COALESCE(t.total,0) - COALESCE(c.ontime,0) - COALESCE(c.late,0), 0)::int AS missing
            FROM principal p
            LEFT JOIN totals  t ON t.id_unidad = p.id_unidad
            LEFT JOIN counts  c ON c.unidad_norm = p.unidad_norm
            WHERE COALESCE(t.total, 0) > 0
            ORDER BY p.unidad_display ASC
        ", [$tz, $tz]);

        $data = array_map(fn($r) => [
            'unidad'  => $r->unidad,
            'total'   => (int)$r->total,
            'ontime'  => (int)$r->ontime,
            'late'    => (int)$r->late,
            'missing' => (int)$r->missing,
        ], $rows);

        $serverTimeLocal = DB::selectOne(
            "SELECT to_char(timezone(?, now()), 'YYYY-MM-DD HH24:MI:SS') AS t", [$tz]
        )->t;

        return response()->json([
            'serverTimeLocal' => $serverTimeLocal,
            'items'           => $data,
        ]);
    }

    public function punctualityList(Request $request)
    {
        $unidadParam = strtoupper(trim($request->query('unidad', '')));
        $type        = $request->query('type', 'ontime'); // ontime | late | missing
        $tz          = config('app.timezone', 'America/Mexico_City');

        // Resolvemos la unidad principal e id_unidad
        $u = DB::selectOne("
            SELECT u.id AS id_unidad, UPPER(TRIM(u.unidad)) AS unidad_norm, u.unidad AS unidad_display
            FROM tbl_unidades u
            WHERE UPPER(TRIM(u.unidad)) = UPPER(TRIM(u.ubicacion))
            AND UPPER(TRIM(u.unidad)) = ?
            LIMIT 1
        ", [$unidadParam]);

        if (!$u) {
            return response()->json(['unidad' => $unidadParam, 'type' => $type, 'items' => []]);
        }

        if ($type === 'missing') {
            $rows = DB::select("
                WITH unit AS (
                    SELECT ?::int AS id_unidad, ?::text AS unidad_norm
                ),
                expected AS (
                    -- Personas que deberían checar en esa unidad
                    SELECT
                        f.clave_empleado,
                        f.nombre_trabajador AS full_name
                    FROM tbl_funcionario f
                    JOIN unit u ON u.id_unidad = f.id_unidad
                    WHERE f.status  = TRUE
                    AND f.checado = TRUE
                ),
                present_raw AS (
                    -- Todos los checks de hoy para esa unidad (cualquier hora)
                    SELECT
                        cl.payload->'records'->0->'employee'->>'workno' AS workno,
                        timezone(?, (cl.payload->'records'->0->>'check_time')::timestamptz) AS t
                    FROM crosschex_live cl
                    JOIN unit u ON TRUE
                    WHERE UPPER(TRIM(COALESCE(
                            cl.payload->'records'->0->'employee'->>'department',
                            cl.payload->'employee'->>'department',
                            '—'
                        ))) = u.unidad_norm
                    AND (timezone(?, (cl.payload->'records'->0->>'check_time')::timestamptz))::date
                            = (timezone(?, now()))::date
                ),
                present AS (
                    -- Primer check del día por empleado
                    SELECT
                        workno,
                        MIN(t) AS first_ts
                    FROM present_raw
                    GROUP BY workno
                ),
                joined AS (
                    -- Esperados + su primer check (si existe)
                    SELECT
                        e.clave_empleado,
                        e.full_name,
                        p.first_ts
                    FROM expected e
                    LEFT JOIN present p
                        ON p.workno = e.clave_empleado::text
                ),
                classified AS (
                    SELECT
                        j.clave_empleado,
                        j.full_name,
                        j.first_ts,
                        CASE
                            WHEN j.first_ts IS NULL THEN 'missing'
                            ELSE
                                CASE
                                    -- A TIEMPO: 07:40:00–08:15:59 y 08:45:00–09:15:59
                                    WHEN (
                                        (j.first_ts::time >= time '07:40:00' AND j.first_ts::time < time '08:16:00')
                                        OR
                                        (j.first_ts::time >= time '08:45:00' AND j.first_ts::time < time '09:16:00')
                                    ) THEN 'ontime'
                                    -- RETARDO: 08:16:00–08:30:59 y 09:16:00–09:30:59
                                    WHEN (
                                        (j.first_ts::time >= time '08:16:00' AND j.first_ts::time < time '08:31:00')
                                        OR
                                        (j.first_ts::time >= time '09:16:00' AND j.first_ts::time < time '09:31:00')
                                    ) THEN 'late'
                                    -- Todo lo demás: checó fuera de ventana → tratamos como falta
                                    ELSE 'missing'
                                END
                        END AS status
                    FROM joined j
                )
                SELECT
                    full_name,
                    clave_empleado,
                    to_char(first_ts, 'YYYY-MM-DD HH24:MI:SS') AS check_time_local
                FROM classified
                WHERE status = 'missing'
                ORDER BY full_name ASC
            ", [
                $u->id_unidad,
                $u->unidad_norm,
                $tz,
                $tz,
                $tz,
            ]);

            $items = array_map(fn($r) => [
                'full_name'        => $r->full_name,
                'workno'           => $r->clave_empleado,
                'check_time_local' => $r->check_time_local, // puede ser null o fecha/hora local
            ], $rows);

            return response()->json([
                'unidad' => $u->unidad_display,
                'type'   => 'missing',
                'items'  => $items,
            ]);
        }


        // ontime / late → mismas ventanas por hora local
        $whereTime = ($type === 'late')
            ? " (
                    (tt >= time '08:16:00' AND tt < time '08:31:00')
                OR (tt >= time '09:16:00' AND tt < time '09:31:00')
                ) "
            : " (
                    (tt >= time '07:40:00' AND tt < time '08:16:00')
                OR (tt >= time '08:45:00' AND tt < time '09:16:00')
            ) ";
        $rows = DB::select("
            WITH base AS (
            SELECT
                UPPER(TRIM(COALESCE(
                payload->'records'->0->'employee'->>'department',
                payload->'employee'->>'department',
                '—'
                ))) AS unidad_norm,
                timezone(?, (payload->'records'->0->>'check_time')::timestamptz) AS t, -- ts local
                payload
            FROM crosschex_live
            ),
            today AS (
            SELECT
                unidad_norm,
                t::date AS d,
                t::time AS tt,
                t      AS ts,
                payload
            FROM base
            WHERE t::date = (timezone(?, now()))::date
            )
            SELECT
            payload->'records'->0->'employee'->>'workno' AS workno,
            CONCAT_WS(' ',
                payload->'records'->0->'employee'->>'first_name',
                payload->'records'->0->'employee'->>'last_name'
            )                                           AS full_name,
            to_char(ts, 'YYYY-MM-DD HH24:MI:SS')        AS check_time_local
            FROM today
            WHERE unidad_norm = ?
            AND {$whereTime}
            ORDER BY ts ASC
        ", [
            $tz,        // timezone(?, check_time)
            $tz,        // timezone(?, now())
            $u->unidad_norm, // unidad_norm normalizada de la unidad seleccionada
        ]);

        return response()->json([
            'unidad' => $u->unidad_display,
            'type'   => $type,
            'items'  => $rows,
        ]);
    }


}
