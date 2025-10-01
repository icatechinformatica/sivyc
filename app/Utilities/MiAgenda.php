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
                SELECT d::date AS fecha
                FROM agenda t
                CROSS JOIN LATERAL generate_series(t.start::date, t.end::date, interval '1 day') d
                WHERE id_curso = ?
                ),
                dias_ordenados AS (
                    SELECT fecha,
                        ROW_NUMBER() OVER (ORDER BY fecha) AS rn
                    FROM dias
                ),
                rangos AS (
                    SELECT fecha,
                        fecha - (rn * interval '1 day') AS grupo
                    FROM dias_ordenados
                ),
                rangos_agrupados AS (
                    SELECT MIN(fecha) AS inicio, MAX(fecha) AS fin
                    FROM rangos
                    GROUP BY grupo
                    ORDER BY MIN(fecha)
                ),
                dias_map AS (
                    SELECT unnest(ARRAY[0,1,2,3,4,5,6]) AS dow,
                        unnest(ARRAY['DOMINGO','LUNES','MARTES','MIÉRCOLES','JUEVES','VIERNES','SÁBADO']) AS dia_nombre
                )
                SELECT string_agg(
                    CASE 
                        WHEN r.inicio = r.fin THEN dm_in.dia_nombre
                        WHEN r.fin - r.inicio = 1 THEN dm_in.dia_nombre || ' Y ' || dm_fin.dia_nombre
                        ELSE dm_in.dia_nombre || ' A ' || dm_fin.dia_nombre
                    END,
                    ', '
                ) AS dias_texto
                FROM rangos_agrupados r
                JOIN dias_map dm_in ON EXTRACT(DOW FROM r.inicio)::int = dm_in.dow
                JOIN dias_map dm_fin ON EXTRACT(DOW FROM r.fin)::int = dm_fin.dow
            ", [$folio_grupo]);
            if(isset($result->dias_texto)) return $result->dias_texto;
    }
}