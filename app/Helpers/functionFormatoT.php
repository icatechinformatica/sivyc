<?php
    use Illuminate\Support\Facades\DB;

    function dataFormatoT($unidad, $status, $fecha) {
        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");
        
        $var_cursos = DB::table('tbl_cursos as c')
            ->select('c.id AS id_tbl_cursos', 'c.status AS estadocurso' ,'c.unidad','c.plantel','c.espe','c.curso','c.clave','c.mod','c.dura',
                DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
                DB::raw('extract(day from c.inicio) as diai'),DB::raw('extract(month from c.inicio) as mesi'),
                DB::raw('extract(day from c.termino) as diat'),DB::raw('extract(month from c.termino) as mest'),
                DB::raw("case when EXTRACT( Month FROM c.termino) between '7' and '9' then '1' when EXTRACT( Month FROM c.termino) between '10' and '12' then '2' when EXTRACT( Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
                'c.horas','c.dia',
                DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
                DB::raw('count(distinct(ins.id)) as tinscritos'),
                DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujer"),
                DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombre"),

                DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
                DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as emujer"),
                DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ehombre"),
                DB::raw("SUM(CASE WHEN ins.calificacion = 'NP' THEN 1 ELSE 0 END) as desertado"),
                DB::raw("SUM(DISTINCT(ins.costo)) as costo"),DB::raw("SUM(ins.costo) as ctotal"),
                
                DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='FEMENINO' then 1 else 0 end) as etmujer"),
                DB::raw("sum(case when ins.abrinscri='ET' and ap.sexo='MASCULINO' then 1 else 0 end) as ethombre"),
                DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='FEMENINO' then 1 else 0 end) as epmujer"),
                DB::raw("sum(case when ins.abrinscri='EP' and ap.sexo='MASCULINO' then 1 else 0 end) as ephombre"),'c.cespecifico','c.mvalida','c.efisico','c.nombre','ip.grado_profesional','ip.estatus','i.sexo','ei.memorandum_validacion','c.mexoneracion',
                DB::raw("sum(case when ap.empresa_trabaja<>'DESEMPLEADO' then 1 else 0 end) as empleado"),
                DB::raw("sum(case when ap.empresa_trabaja='DESEMPLEADO' then 1 else 0 end) as desempleado"),
                DB::raw("sum(case when ap.discapacidad <> 'NINGUNA' then 1 else 0 end) as discapacidad"),
                DB::raw("0 as madres_solteras"), // debe ir madres solteras 
                
                DB::raw("sum(case when ar.migrante = 'true' then 1 else 0 end) as migrante"),
                DB::raw("sum(case when ar.indigena = 'true' then 1 else 0 end) as indigena"),
                DB::raw("sum(case when ar.etnia <> NULL then 1 else 0 end) as etnia"),
                'c.programa','c.muni','c.depen','c.cgeneral','c.sector','c.mpaqueteria',

                DB::raw("sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='FEMENINO' then 1 else 0 end) as iem1"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh1"),
                DB::raw("sum( CASE  WHEN  EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'  THEN 1  ELSE 0 END ) as iem2"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh2"),
                DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END ) as iem3"),
                DB::raw("sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh3"),
                DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END ) as iem4"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh4"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem5"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh5"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem6"),
                db::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end) as ieh6"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem7"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh7"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end) as iem8"),
                DB::raw("sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end) as ieh8"),

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' then 1 else 0 end) as iesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' then 1 else 0 end) as iesh9"),

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ins.calificacion != 'NP' then 1 else 0 end) as aesh9"),
                

                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA INCONCLUSA' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh1"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='PRIMARIA TERMINADA' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh2"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA INCONCLUSA' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh3"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='SECUNDARIA TERMINADA' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh4"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh5"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL MEDIO SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh6"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR INCONCLUSO' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh7"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='NIVEL SUPERIOR TERMINADO' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh8"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='FEMENINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesm9"),
                DB::raw("sum(case when ap.ultimo_grado_estudios='POSTGRADO' and ap.sexo='MASCULINO' and ins.calificacion = 'NP' then 1 else 0 end) as naesh9"),
                
                DB::raw("case when c.arc='01' then nota else observaciones end as tnota"),
                // DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD'->>'OBSERVACION_RETORNO' AS observaciones_enlaces"),
                DB::raw("c.observaciones_formato_t->'OBSERVACION_FIRMA' AS observaciones_firma"),
                DB::raw("count( ar.id_pre) AS totalinscripciones"),
                DB::raw("count( CASE  WHEN  ap.sexo ='MASCULINO' THEN ar.id_pre END ) AS masculinocheck"),
                DB::raw("count( CASE  WHEN ap.sexo ='FEMENINO' THEN ar.id_pre END ) AS femeninocheck"),
                DB::raw("COALESCE(sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='   ' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO' 
                THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END )) + COALESCE(sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34 
                AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end)) as sumatoria_total_ins_edad"),
                DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD' AS observaciones_enlaces")
            )
            ->JOIN('instructores as i', 'c.id_instructor', '=', 'i.id')
            ->JOIN('instructor_perfil as ip', 'i.id', '=', 'ip.numero_control')
            ->JOIN('especialidad_instructores as ei', 'ip.id', '=', 'ei.perfilprof_id')
            ->JOIN('especialidades as e', function($join)
                {
                    $join->on('ei.especialidad_id', '=', 'e.id');                
                    $join->on('c.espe', '=', 'e.nombre');
                })
            ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
            ->JOIN($temptblinner ,function($join)
            {
                $join->on('ins.matricula', '=', 'ar.no_control');                
                $join->on('c.id_curso','=','ar.id_curso');
            })
            ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
            ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')  
            ->WHERE('u.ubicacion', '=', $unidad)
            ->WHEREIN('c.status', $status)
            ->where('ins.status', '=', 'INSCRITO')
            ->WHERE('c.clave', '!=', 'null')
            ->where('ins.calificacion', '>', '0')
            ->groupby('c.id', 'c.status', 'c.unidad', 'c.nombre', 'c.clave', 'c.mod', 'c.espe', 'c.curso', 'c.inicio', 'c.termino', 'c.dia', 'c.dura', 'c.hini', 'c.hfin', 'c.horas', 'c.plantel',
                'c.programa', 'c.muni', 'c.depen', 'c.cgeneral', 'c.mvalida', 'c.efisico', 'c.cespecifico', 'c.sector', 'c.mpaqueteria', 'c.mexoneracion',
                'c.nota', 'i.sexo', 'ei.memorandum_validacion', 'ip.grado_profesional', 'ip.estatus')
            ->distinct()
            ->get();

        return $var_cursos;
    }