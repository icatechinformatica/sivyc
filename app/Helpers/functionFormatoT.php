<?php

use Illuminate\Support\Facades\DB;
use Mockery\Undefined;
use PhpParser\Node\Stmt\Foreach_;

//       dataFormatoT($unidad, $status, $fecha_turnado=null, $mes = null ,$add=false, $memo=null) {
    //   unction dataFormatoT2do($unidad, $turnado, $fecha, $mesSearch, $status) {
function dataFormatoT($unidad, $status, $turnado=null, $fecha=null, $mesSearch=null, $fecha_turnado=null, $mes = null ,$add=false, $memo=null) {

    $var_cursos = DB::table('tbl_cursos as c')
        ->select(
            DB::raw("to_char(c.fecha_turnado, 'TMMONTH') AS fechaturnado"),
            'c.id AS id_tbl_cursos',
            'c.status AS estadocurso',
            'c.unidad',
            'c.plantel',
            'c.espe',
            'c.curso',
            'c.clave',
            'c.mod',
            DB::raw("array(select folio from tbl_folios where id_curso = c.id order by folio) as folios"),
            DB::raw("array(select movimiento from tbl_folios where id_curso = c.id order by folio) as movimientos"),
            'c.dura',
            'c.turnado AS turnados_enlaces', //new
            DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
            DB::raw('extract(day from c.inicio) as diai'),
            DB::raw('extract(month from c.inicio) as mesi'),
            DB::raw('extract(day from c.termino) as diat'),
            DB::raw('extract(month from c.termino) as mest'),
            DB::raw("case when EXTRACT(Month FROM c.termino) between '7' and '9' then '1' when EXTRACT(Month FROM c.termino) between '10' and '12' then '2' when EXTRACT(Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
            'c.horas',
            'c.dia',
            DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),

            // --- SUMA DE INSCRITOS SIN RESTAR LGBT ---
            DB::raw('count(distinct(ins.id)) as tinscritos'),
            DB::raw("SUM(CASE WHEN ins.sexo='M' THEN 1 ELSE 0 END) as imujer"),
            DB::raw("SUM(CASE WHEN ins.sexo='H' THEN 1 ELSE 0 END) as ihombre"),

            // --- SUMA DE INSCRITOS SIN RESTAR LGBT ---
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='M' THEN 1 ELSE 0 END) as emujer"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='H' THEN 1 ELSE 0 END) as ehombre"),

            DB::raw("SUM(CASE WHEN ins.calificacion = 'NP' THEN 1 ELSE 0 END) as desertado"),
            DB::raw("ROUND(SUM(ins.costo) / COUNT(distinct(ins.id)), 2) as costo"),
            DB::raw("SUM(ins.costo) as ctotal"),
            DB::raw("CASE WHEN COUNT(distinct(ins.costo)) = 1 THEN 'NO' ELSE 'SI' END AS cuotamixta"),

            // --- SUMA DE EXONERACION TOTAL SIN RESTAR LGBT ---
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='M' then 1 else 0 end) as etmujer"),
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='H' then 1 else 0 end) as ethombre"),
            // --- SUMA DE EXONERACION PARCIAL SIN RESTAR LGBT ---
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='M' then 1 else 0 end) as epmujer"),
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='H' then 1 else 0 end) as ephombre"),


            'c.cespecifico',
            'c.mvalida',
            'c.efisico',
            'c.nombre',
            'c.instructor_escolaridad as grado_profesional',
            'c.instructor_titulo as estatus',
            'c.instructor_sexo as sexo',
            // 'ip.grado_profesional',
            // 'ip.estatus',
            // 'i.sexo',
            'c.instructor_mespecialidad as memorandum_validacion',
            // 'ei.memorandum_validacion',
            'c.mexoneracion',
            DB::raw("sum(case when ins.empleado = true then 1 else 0 end) as empleado"),
            DB::raw("sum(case when ins.empleado = false then 1 else 0 end) as desempleado"),
            // DB::raw("sum(case when ins.id_gvulnerable::text like '%18%' or ins.id_gvulnerable #-# '4' or ins.id_gvulnerable::text like '%20%' or ins.id_gvulnerable::text like '%21%' or ins.id_gvulnerable::text like '%22%' then 1 else 0 end) as discapacidad"),
            DB::raw("sum(case when jsonb_exists(ins.id_gvulnerable::jsonb, '18') or jsonb_exists(ins.id_gvulnerable::jsonb, '4') or jsonb_exists(ins.id_gvulnerable::jsonb, '20') or jsonb_exists(ins.id_gvulnerable::jsonb, '21') or jsonb_exists(ins.id_gvulnerable::jsonb, '22') then 1 else 0 end) as discapacidad"),
            DB::raw("sum(case when madre_soltera is true then 1 else 0 end) as madres_solteras"), // debe ir madres solteras
            DB::raw("sum(case when ins.inmigrante = true then 1 else 0 end) as migrante"),
            DB::raw("sum(CASE WHEN EXTRACT(YEAR FROM (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 AND jsonb_exists(ins.id_gvulnerable::jsonb, '4') THEN 1 ELSE 0 END) as adolescente_calle"),
            DB::raw("SUM(CASE WHEN jsonb_exists(ins.id_gvulnerable::jsonb, '8') and ins.sexo='M' and ins.lgbt = false or jsonb_exists(ins.id_gvulnerable::jsonb, '8') and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as jefa_familia"),
            DB::raw("sum(case when ins.indigena = true then 1 else 0 end) as indigena"),
            DB::raw("sum(case when ins.etnia <> NULL then 1 else 0 end) as etnia"),
            DB::raw("CONCAT(sum(case when ins.id_cerss IS NOT NULL then 1 else 0 end), ' - (', cerss.nombre, ')' ) as cerss_nombre"),
            'c.programa',
            'c.muni',
            'c.ze',
            'm.region',
            'c.depen',
            'c.cgeneral',
            'c.sector',
            'c.mpaqueteria',
            'gv.grupo',

            // --- RANGO DE EDADES EN RUBRO FEDERAL ---
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='M' then 1 else 0 end) as iem1f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='H' then 1 else 0 end) as ieh1f"),
            DB::raw("sum(CASE WHEN EXTRACT(YEAR FROM (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 AND ins.sexo = 'M'  THEN 1 ELSE 0 END) as iem2f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 and ins.sexo='H' then 1 else 0 end) as ieh2f"),
            DB::raw("sum(CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 AND ins.sexo='M' THEN 1 ELSE 0 END) as iem3f"),
            DB::raw("sum(Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 and ins.sexo='H' then 1 else 0 end) as ieh3f"),
            DB::raw("sum(CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='M' THEN 1 ELSE 0 END) as iem4f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='H' then 1 else 0 end) as ieh4f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='M' then 1 else 0 end) as iem5f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='H' then 1 else 0 end) as ieh5f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='M' then 1 else 0 end) as iem6f"),
            db::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='H' then 1 else 0 end) as ieh6f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 AND ins.sexo='M' then 1 else 0 end) as iem7f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 and ins.sexo='H' then 1 else 0 end) as ieh7f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 AND ins.sexo='M' then 1 else 0 end) as iem8f"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 and ins.sexo='H' then 1 else 0 end) as ieh8f"),

            // --- SUMA DE HOMBRES Y MUJERES EN ESCOLARIDAD SIN RESTAR LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' then 1 else 0 end) as iesm1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' then 1 else 0 end) as iesh1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' then 1 else 0 end) as iesm2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' then 1 else 0 end) as iesh2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' then 1 else 0 end) as iesm3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' then 1 else 0 end) as iesh3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' then 1 else 0 end) as iesm4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' then 1 else 0 end) as iesh4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' then 1 else 0 end) as iesm5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' then 1 else 0 end) as iesh5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' then 1 else 0 end) as iesm6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' then 1 else 0 end) as iesh6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' then 1 else 0 end) as iesm7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' then 1 else 0 end) as iesh7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' then 1 else 0 end) as iesm8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' then 1 else 0 end) as iesh8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' then 1 else 0 end) as iesm9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' then 1 else 0 end) as iesh9"),

            // --- SUMA DE HOMBRES Y MUJERES EN ACREDITADOS SIN RESTAR LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion != 'NP' then 1 else 0 end) as aesm9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion != 'NP' then 1 else 0 end) as aesh9"),

            // --- SUMA DE HOMBRES Y MUJERES EN NO ACREDITO SIN RESTAR LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion = 'NP' then 1 else 0 end) as naesm9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion = 'NP' then 1 else 0 end) as naesh9"),

            /* DB::raw("COALESCE(sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='   ' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'
                THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END )) + COALESCE(sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34
                AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end)) as sumatoria_total_ins_edad"), */
            is_null($mesSearch)
                ? DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD' AS observaciones_enlaces")
                : (in_array('TURNADO_DTA',$status)
                    ? DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD' AS observaciones_enlaces")
                    : (in_array('REVISION_DTA', $status)
                        ? DB::raw("c.observaciones_formato_t->'OBSERVACIONES_REVISION_DIRECCION_DTA'->>'OBSERVACION_REVISION_JEFE_DTA' AS observaciones_enlaces")
                        : DB::raw("c.observaciones_formato_t->'OBSERVACION_DIRECCIONDTA_TO_PLANEACION'->>'OBSERVACION_ENVIO_PLANEACION' AS observacion_envio_to_planeacion"))),

            'c.arc',
            DB::raw("c.observaciones_formato_t->'OBSERVACION_FIRMA' AS observaciones_firma"),
            DB::raw("
                                (
                                CASE
                                    WHEN c.arc='01'  AND c.nota ILIKE '%INSTRUCTOR%' THEN c.nota
                                    WHEN c.arc='01' THEN(
                                        CASE
                                                WHEN c.modinstructor = 'ASIMILADOS A SALARIOS' THEN 'INSTRUCTOR POR HONORARIOS ' || c.modinstructor || ', '
                                                WHEN c.modinstructor = 'HONORARIOS' THEN 'INSTRUCTOR POR ' || c.modinstructor || ', '
                                                ELSE ''
                                        END
                                            ||
                                        CASE
                                                WHEN c.tipo = 'EXO' THEN 'MEMORÁNDUM DE EXONERACIÓN No. ' || c.mexoneracion || ', '
                                                WHEN c.tipo = 'EPAR' THEN 'MEMORÁNDUM DE REDUCIÓN DE CUOTA No. ' || c.mexoneracion || ', '
                                                ELSE ''
                                        END
                                            ||
                                        CASE
                                        WHEN c.tipo != 'EXO' THEN
                                                    'CUOTA DE RECUPERACIÓN $' || ROUND((c.costo)/(c.hombre+c.mujer),2) || ' POR PERSONA, ' ||
                                                    'TOTAL CURSO $' || TO_CHAR(ROUND(c.costo, 2), 'FM999,999,999.00')
                                        ELSE ''
                                        END
                                            || ' MEMORÁNDUM DE VALIDACIÓN DEL INSTRUCTOR ' || c.instructor_mespecialidad
                                            || ' ' || COALESCE(c.nota, '')
                                        )
                                    ELSE
                                            c.observaciones
                                END

                                ) AS tnota
                            "),
            DB::raw("c.observaciones_formato_t->'OBSERVACION_ENLACES_RETORNO_UNIDAD' AS comentario_enlaces_retorno"), //new
            DB::raw("c.observaciones_formato_t->'COMENTARIOS_UNIDAD' AS observaciones_unidad"), // new
            'c.status_solicitud_arc02',
            'c.arc'
        )

        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->JOIN('tbl_municipios as m', 'm.id', '=', 'c.id_municipio')
        ->LEFTJOIN('grupos_vulnerables as gv', 'gv.id', '=', 'c.id_gvulnerable')
        ->LEFTJOIN('cerss', 'cerss.id', 'c.id_cerss')
        ->WhereIn('c.status', $status) // new
        ->WHERE('c.status_curso', '=', 'AUTORIZADO')
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
        ->where('m.id_estado', '=', '7')
        ->groupby(
            'c.id',
            'c.status',
            'c.unidad',
            'c.nombre',
            'c.clave',
            'c.mod',
            'c.espe',
            'c.curso',
            'c.inicio',
            'c.termino',
            'c.dia',
            'c.dura',
            'c.hini',
            'c.hfin',
            'c.horas',
            'c.plantel',
            'c.programa',
            'c.muni',
            'c.depen',
            'c.cgeneral',
            'c.mvalida',
            'c.efisico',
            'c.cespecifico',
            'c.sector',
            'c.mpaqueteria',
            'c.mexoneracion',
            'c.nota',
            // 'i.sexo',
            // 'ei.memorandum_validacion',
            // 'ip.grado_profesional',
            // 'ip.estatus',
            'm.region',
            'gv.grupo',
            'cerss.nombre'
        )
        ->distinct();

        if($add==true) {
            $var_cursos = $var_cursos->addSelect('c.tcapacitacion','c.status','c.inicio','c.termino',
                'c.memos->TURNADO_DTA->MEMORANDUM as memo_turnado_dta','c.memos->TURNADO_DTA->NUMERO as nmemo_turnado_dta',
                'c.memos->TURNADO_PLANEACION->PLANEACION->MEMORANDUM as memo_turnado_planeacion','c.memos->TURNADO_PLANEACION->PLANEACION->NUMERO as nmemo_turnado_planeacion',
                'c.memos->CERRADO_PLANEACION->MEMORANDUM as memo_cerrado_planeacion','c.memos->CERRADO_PLANEACION->NUMERO as nmemo_cerrado_planeacion',
                'c.resumen_formatot_unidad as memo_turnado_unidad');
        }

        if(!is_null($mesSearch)) {
            // dd($mesSearch);
            $calendario_formatot = DB::Table('calendario_formatot')->Select('inicio','termino')->Where('mes_entrega',(int)$mesSearch)->Get();
            $var_cursos = $var_cursos->WhereDate('c.fecha_turnado', '>=', $calendario_formatot[0]->inicio)->WhereDate('c.fecha_turnado', '<=', $calendario_formatot[1]->termino);
            // $var_cursos = $var_cursos->whereMonth('c.fecha_turnado', $mesSearch); // new
        }
        if($mes) {
            $calendario_formatot = DB::Table('calendario_formatot')->Select('inicio','termino')->Where('mes_entrega',(int)$mes)->Get();
            $var_cursos = $var_cursos->WhereDate('c.fecha_turnado', '>=', $calendario_formatot[0]->inicio)->WhereDate('c.fecha_turnado', '<=', $calendario_formatot[1]->termino);
            // $var_cursos = $var_cursos->whereRaw("c.fecha_turnado::TEXT LIKE ?", [$mes.'%']);
        }
        elseif($fecha_turnado) {
            $var_cursos = $var_cursos->where('c.fecha_turnado', $fecha_turnado);
        }
        if(!is_null($turnado)){
            $var_cursos = $var_cursos->WHEREIN('c.turnado', $turnado);
        }

        if ($unidad != 'all' && $unidad != 'ALL') {
            $var_cursos2 = $var_cursos->WHERE('u.ubicacion', '=', $unidad)->get();
        } else {
            $var_cursos2 = $var_cursos->get();
        }

        if ($status != 'TURNADO_DTA') {
            foreach ($var_cursos2 as $value) {
                unset($value->folios);
                unset($value->movimientos);
            }
        }
    return $var_cursos2;
}
