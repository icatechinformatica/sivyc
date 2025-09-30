<?php
namespace App\Utilities;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class MiAgenda
{   
    public static function agenda_tdias($folio_grupo){
    return $totalDias = DB::table('agenda as t')
        ->selectRaw("COUNT(DISTINCT d::date) as total_dias")
        ->crossJoin(DB::raw("LATERAL generate_series(t.start::date, t.end::date, interval '1 day') d"))
        ->where('t.id_curso', $folio_grupo)
        ->value('total_dias');
    }

    public static function agenda_dias($folio_grupo){
       $result = DB::selectOne("
            WITH dias AS (
                SELECT DISTINCT EXTRACT(DOW FROM d)::int AS dow
                FROM agenda t
                CROSS JOIN LATERAL generate_series(t.start::date, t.end::date, interval '1 day') d
                WHERE id_curso = ?
            ),
            dias_map AS (
                SELECT unnest(ARRAY[0,1,2,3,4,5,6]) AS dow,
                    unnest(ARRAY['DOMINGO','LUNES','MARTES','MIÉRCOLES','JUEVES','VIERNES','SÁBADO']) AS dia_nombre
            ),
            arr AS (
                SELECT array_agg(dow ORDER BY dow) AS dias FROM dias
            ),
            dias_text AS (
                SELECT dias,
                    min(dow) AS min_dow,
                    max(dow) AS max_dow
                FROM arr, unnest(arr.dias) AS dow
                GROUP BY dias
            )
            SELECT 
                CASE 
                    -- Lunes a Viernes
                    WHEN dias <@ ARRAY[1,2,3,4,5] AND array_length(dias,1) = 5 THEN 'LUNES A VIERNES'
                    -- Sábado y Domingo
                    WHEN dias <@ ARRAY[0,6] AND array_length(dias,1) = 2 THEN 'SÁBADO Y DOMINGO'
                    -- Rango continuo de días (ej. Miércoles a Lunes)
                    WHEN array_length(dias,1) = (( (max_dow - min_dow + 7) % 7 ) + 1) THEN
                        (SELECT dm1.dia_nombre || ' A ' || dm2.dia_nombre
                        FROM dias_map dm1
                        JOIN dias_map dm2 ON dm2.dow = dt.max_dow
                        WHERE dm1.dow = dt.min_dow)
                    -- Días no consecutivos → listados con comas
                    ELSE (SELECT string_agg(dm.dia_nombre, ', ' ORDER BY dm.dow)
                        FROM dias_map dm
                        WHERE dm.dow = ANY(dt.dias))
                END AS dias_texto
            FROM dias_text dt
            ", [$folio_grupo]);
            if(isset($result->dias_texto)) return $result->dias_texto;
    }
}