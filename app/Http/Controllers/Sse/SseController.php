<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    public function stream()
    {
        @ini_set('zlib.output_compression', 0);
        @ini_set('output_buffering', 'off');
        @ini_set('implicit_flush', 1);
        if (function_exists('apache_setenv')) @apache_setenv('no-gzip', '1');

        $response = new StreamedResponse(function () {
            $lastId = request()->headers->get('Last-Event-ID');
            $lastId = is_numeric($lastId) ? (int)$lastId : null;

            $deadline  = time() + 90;
            $beatEvery = 10;
            $i = 0;

            $tz = config('app.timezone', 'America/Mexico_City');

            while (true) {
                if (time() >= $deadline) { echo ": closing\n\n"; @ob_flush(); @flush(); break; }

                $rows = \DB::select("
                    SELECT
                        id,
                        to_char(timezone(?, received_at), 'HH24:MI:SS')             AS received_time,   -- 👈
                        to_char(
                        timezone(?, (payload->'records'->0->>'check_time')::timestamptz),
                        'YYYY-MM-DD HH24:MI:SS'
                        )                                                           AS check_time_local, -- 👈
                        payload->'records'->0->'employee'->>'workno'                AS workno,
                        payload->'records'->0->'employee'->>'first_name'            AS first_name,
                        payload->'records'->0->'employee'->>'last_name'             AS last_name,
                        COALESCE(payload->'records'->0->'employee'->>'department',
                                payload->'employee'->>'department','—')            AS unidad,
                        payload->'records'->0->'device'->>'name'                    AS device_name,
                        payload->'records'->0->'device'->>'serial_number'           AS serial_number,
                        payload->'records'->0->>'check_type'                        AS check_type
                    FROM crosschex_live
                    WHERE (? IS NULL OR id > ?)
                    ORDER BY id ASC
                    LIMIT 200
                ", [$tz, $tz, $lastId, $lastId]);

                if ($rows) {
                    $lastId = end($rows)->id;
                    echo "id: {$lastId}\n";
                    echo "event: batch\n";
                    echo 'data: ' . json_encode($rows) . "\n\n";
                    @ob_flush(); @flush();
                }

                if (($i++ % $beatEvery) === 0) { echo ": keepalive\n\n"; @ob_flush(); @flush(); }
                usleep(500000);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no');
        return $response;
    }
}
