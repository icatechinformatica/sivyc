<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\tbl_cursos;

class GrupoService
{    
    public function folio_unico($grupoId){   
        // Obtener siglas según la ubicación
        $siglas = DB::table('tbl_cursos as c')
            ->join('tbl_unidades as u', 'c.unidad', '=', 'u.unidad')
            ->where('c.id', $grupoId)
            ->orderBy('c.id')
            ->value(DB::raw("
                CONCAT(
                UPPER(
                    CASE 
                        WHEN u.ubicacion = 'SAN CRISTOBAL' THEN 'SCL' 
                        ELSE SUBSTR(u.ubicacion, 1, 3) 
                    END
                )::text,
                '-',
                TO_CHAR(
                    CASE 
                        WHEN CURRENT_DATE > c.inicio THEN c.inicio
                        ELSE CURRENT_DATE
                    END,
                    'YYMMDD'
                )

                )
            "));

        // Ejecutar el update con bindings seguros
        return DB::statement("
            UPDATE tbl_cursos c
            SET folio_unico = CONCAT(
                :siglas::text,                             
                '-',
                LPAD(
                    (
                        COALESCE(
                            (
                                SELECT MAX( (split_part(folio_unico, '-', 3))::int )
                                FROM tbl_cursos
                                WHERE folio_unico LIKE CONCAT(:siglas::text, '-%')
                            ),
                        0) + 1
                    )::text,
                    3,
                    '0'
                )
            )
            WHERE c.id = :id and folio_unico is null
        ", [            
            'siglas' => $siglas,
            'id'     => $grupoId
        ]);
    }
    
}
