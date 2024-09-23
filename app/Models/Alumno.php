<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Alumno extends Model
{
    //
    protected $table = 'alumnos_registro';

    protected $fillable = [
        'no_control',
        'fecha',
        'numero_solicitud',
        'id_pre',
        'id_especialidad',
        'id_curso',
        'horario',
        'grupo',
        'unidad',
        'tipo_curso',
        'realizo',
        'cerrs',
        'etnia',
        'estatus_modificacion',
        'costo',
        'tinscripcion'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function alumnospre() {
        return $this->belongsTo(Alumnopre::class, 'id');
    }

    // in your model
    public function getMyDateFormat($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // scopes

    public function scopeBusqueda($query, $buscar, $tipo=null)
    {
        if (!empty(trim($buscar))) {
            $result = $query->from('alumnos_registro as ar')
                ->select('ar.id as id_reg', 'ap.id_gvulnerable',           
                DB::raw('COALESCE(ti.curp, ar.curp) as curp'),
                DB::raw('COALESCE(ti.matricula, ar.no_control) as matricula'),
                DB::raw("COALESCE(ti.alumno, concat(ar.apellido_paterno,' ', ar.apellido_materno,' ',ar.nombre)) as alumno"),
                DB::raw('COALESCE(substring(ti.curp,11,1), substring(ar.curp,11,1)) as sexo'),
                DB::raw("(CONCAT(
                            CASE
                                WHEN SUBSTRING( COALESCE(ti.curp, ar.curp), 5, 2) > TO_CHAR(NOW(), 'YY') THEN CONCAT('19', SUBSTRING(COALESCE(ti.curp, ar.curp), 5, 2))
                                ELSE CONCAT('20', SUBSTRING(COALESCE(ti.curp, ar.curp), 5, 2))
                            END,'-', SUBSTRING(COALESCE(ti.curp, ar.curp), 7, 2), '-', SUBSTRING(COALESCE(ti.curp, ar.curp), 9, 2) )
                    ) as fecha_nacimiento"),
                DB::raw('COALESCE(EXTRACT(year from (age(ti.inicio,ap.fecha_nacimiento))) , EXTRACT(year from (age(ar.inicio,ap.fecha_nacimiento))) ) as edad'),
                DB::raw('COALESCE(ti.escolaridad, ar.escolaridad) as escolaridad'),
                DB::raw("COALESCE(
                    CASE WHEN ti.id_gvulnerable IS NULL THEN NULL
                        ELSE ( SELECT STRING_AGG(grupo, ', ') FROM grupos_vulnerables WHERE id IN ( SELECT CAST(jsonb_array_elements_text(ti.id_gvulnerable) AS bigint)))
                    END,
                    CASE WHEN ap.id_gvulnerable IS NULL THEN NULL
                        ELSE ( SELECT STRING_AGG(grupo, ', ') FROM grupos_vulnerables WHERE id IN ( SELECT CAST(jsonb_array_elements_text(ap.id_gvulnerable) AS bigint)))
                    END) as grupos"),
                DB::raw('COALESCE(ti.inmigrante, ap.inmigrante) as inmigrante'),
                DB::raw('ap.es_cereso'),
                DB::raw('COALESCE(ti.familia_migrante, ap.familia_migrante) as familia_migrante'),
                DB::raw('COALESCE(ti.madre_soltera, ap.madre_soltera) as madre_soltera'),
                DB::raw('COALESCE(ti.lgbt, ap.lgbt) as lgbt'),
                DB::raw('COALESCE(ti.nacionalidad, ap.nacionalidad) as nacionalidad'),
                DB::raw('COALESCE(ti.tinscripcion, ar.tinscripcion) as tinscripcion'),
                DB::raw('COALESCE(ti.costo, ar.costo) as costo'),
                DB::raw("COALESCE(ti.requisitos::jsonb->'documento', COALESCE(ar.requisitos::jsonb->'documento', ap.requisitos::jsonb->'documento')) as doc_requisitos"),
                DB::raw(" CASE WHEN  id_folio is not null and ti.status='EDICION' THEN  'FOLIO' ELSE ti.status END status")                
            ) 
            ->join('alumnos_pre as ap', 'ap.id', 'ar.id_pre')
            ->leftJoin('tbl_unidades as tu', 'ar.unidad', '=', 'tu.unidad')
            ->leftJoin('tbl_inscripcion as ti', function ($join) {
                $join->on('ti.folio_grupo', '=', 'ar.folio_grupo')
                    ->on('ti.curp','ar.curp');
            });

            if ($tipo=='grupo') {
                $query->where('ar.folio_grupo', $buscar); //dd("pasa");
            } else {
                $query->addSelect('ar.horario','tf.folio',
                DB::raw('COALESCE(tc.curso, c.nombre_curso) as nombre_curso'),
                DB::raw('COALESCE(tc.folio_grupo, ar.folio_grupo) as folio_grupo'),
                DB::raw('COALESCE(tc.clave, null) as clave'),
                DB::raw('COALESCE(tc.inicio, ar.inicio) as inicio'),
                DB::raw('COALESCE(tc.termino, ar.termino) as termino')); 

                $query->where(DB::raw(
                    "CONCAT(ar.folio_grupo, ar.no_control, ar.apellido_paterno, ' ',ar.apellido_materno,' ',ar.nombre,
                     ar.curp)") , 'LIKE', "%$buscar%"    
                ); 
                $query->leftjoin('tbl_cursos as tc','tc.folio_grupo','ar.folio_grupo')
                    ->leftjoin('tbl_folios as tf', 'tf.id', '=', 'ti.id_folio')
                    ->leftjoin('cursos as c','c.id','ar.id_curso');
            }
            
            $query->orderBy(DB::raw("CONCAT(ar.apellido_paterno, ' ',ar.apellido_materno,' ',ar.nombre)"), 'ASC');
            return $result;
        }
    }
}
