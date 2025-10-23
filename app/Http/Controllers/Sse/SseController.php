<?php

namespace App\Http\Controllers\Sse;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    public function stream()
    {
        // Evitar buffering para SSE
        @ini_set('zlib.output_compression', 0);
        @ini_set('output_buffering', 'off');
        @ini_set('implicit_flush', 1);
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }

        $response = new StreamedResponse(function () {
            // Si el browser re-conecta, puede mandar Last-Event-ID
            $lastIdHeader = request()->headers->get('Last-Event-ID');
            $lastId = is_numeric($lastIdHeader) ? (int)$lastIdHeader : null;

            // Vida útil de la conexión (segundos) para reciclar workers
            $deadline = time() + 90;

            // Heartbeat cada N iteraciones (con sleep de 0.5s)
            $beatEvery = 10;
            $i = 0;

            while (true) {
                if (time() >= $deadline) {
                    echo ": closing\n\n";
                    @ob_flush(); @flush();
                    break;
                }

                // Traer filas nuevas (orden ascendente por id)
                $rows = DB::select("
                    SELECT
                        id,
                        received_at,
                        payload->'records'->0->'employee'->>'workno'      AS workno,
                        payload->'records'->0->'employee'->>'first_name'  AS first_name,
                        payload->'records'->0->'employee'->>'last_name'   AS last_name,
                        COALESCE(
                          payload->'records'->0->'employee'->>'department',
                          payload->'employee'->>'department','—'
                        ) AS unidad,
                        payload->'records'->0->'device'->>'name'          AS device_name,
                        payload->'records'->0->'device'->>'serial_number' AS serial_number,
                        payload->'records'->0->>'check_time'              AS check_time,
                        payload->'records'->0->>'check_type'              AS check_type
                    FROM crosschex_live
                    WHERE (? IS NULL OR id > ?)
                    ORDER BY id ASC
                    LIMIT 200
                ", [$lastId, $lastId]);

                if (!empty($rows)) {
                    $lastId = end($rows)->id;
                    echo "id: {$lastId}\n";
                    echo "event: batch\n";
                    echo 'data: ' . json_encode($rows) . "\n\n";
                    @ob_flush(); @flush();
                }

                if (($i++ % $beatEvery) === 0) {
                    echo ": keepalive\n";
                    @ob_flush(); @flush();
                }

                usleep(500000); // 0.5 s
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('X-Accel-Buffering', 'no'); // Nginx
        return $response;
    }
}
