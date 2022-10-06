<?php

use Illuminate\Support\Facades\DB;
use Mockery\Undefined;
use PhpParser\Node\Stmt\Foreach_;

function dataFormatoT($unidad, $status, $fecha) {
    $cad = DB::raw('SUM(CASE WHEN ins.id_gvulnerable ? \'1\' and ins.sexo=\'M\' and ins.lgbt = false or ins.id_gvulnerable ? \'1\' and ins.sexo=\'M\' and ins.lgbt is null THEN 1 ELSE 0 END) as gv1m');
    // dd($cad);
    $var_cursos = DB::table('tbl_cursos as c')
        ->select(
            'c.termino',
            'c.id AS id_tbl_cursos',
            'c.status AS estadocurso',
            'c.unidad',
            'c.plantel',
            'c.espe',
            'c.curso',
            'c.clave',
            'c.mod',
            'c.dura',
            DB::raw("case when extract(hour from to_timestamp(c.hini,'HH24:MI a.m.')::time)<14 then 'MATUTINO' else 'VESPERTINO' end as turno"),
            DB::raw('extract(day from c.inicio) as diai'),
            DB::raw('extract(month from c.inicio) as mesi'),
            DB::raw('extract(day from c.termino) as diat'),
            DB::raw('extract(month from c.termino) as mest'),
            DB::raw("case when EXTRACT(Month FROM c.termino) between '7' and '9' then '1' when EXTRACT(Month FROM c.termino) between '10' and '12' then '2' when EXTRACT(Month FROM c.termino) between '1' and '3' then '3' else '4' end as pfin"),
            'c.horas',
            'c.dia',
            DB::raw("concat(c.hini,' ', 'A', ' ',c.hfin) as horario"),
            DB::raw('count(distinct(ins.id)) as tinscritos'),
            // --- SUMA DE HOMBRES Y MUJERES SIN RESTAR EL GENERO LGBTTTI+ ---
            DB::raw("SUM(CASE WHEN ins.sexo='M' THEN 1 ELSE 0 END) as imujer"),
            DB::raw("SUM(CASE WHEN ins.sexo='H' THEN 1 ELSE 0 END) as ihombre"),

            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            // --- SUMA DE HOMBRES Y MUJERES EGRESADOS SIN RESTAR EL GENERO LGBTTTI+ ---
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='M' THEN 1 ELSE 0 END) as emujer"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='H' THEN 1 ELSE 0 END) as ehombre"),

            DB::raw("SUM(CASE WHEN ins.calificacion = 'NP' THEN 1 ELSE 0 END) as desertado"),
            DB::raw("ROUND(SUM(ins.costo) / COUNT(distinct(ins.id)), 2) as costo"),
            DB::raw("SUM(ins.costo) as ctotal"),
            DB::raw("CASE WHEN COUNT(distinct(ins.costo)) = 1 THEN 'NO' ELSE 'SI' END AS cuotamixta"),

            // SUMA DE HOMBRES Y MUJERES CON EXONERACION TOTAL SIN RESTAR EL GENERO LGBTTTI+ ---
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='M' then 1 else 0 end) as etmujer"),
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='H' then 1 else 0 end) as ethombre"),
            // SUMA DE HOMBRES Y MUJERES CON EXONERACION PARCIAL SIN RESTAR EL GENERO LGBTTTI+ ---
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
            DB::raw("sum(case when ins.id_gvulnerable::text like '%18%' or ins.id_gvulnerable::text like '%19%' or ins.id_gvulnerable::text like '%20%' or ins.id_gvulnerable::text like '%21%' or ins.id_gvulnerable::text like '%22%' then 1 else 0 end) as discapacidad"),
            DB::raw("0 as madres_solteras"), // debe ir madres solteras

            DB::raw("sum(case when ins.inmigrante = true then 1 else 0 end) as migrante"),
            DB::raw("sum(case when ins.id_gvulnerable::text like '%7%' then 1 else 0 end) as indigena"),
            DB::raw("sum(case when ins.etnia <> NULL then 1 else 0 end) as etnia"),
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
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='M' then 1 else 0 end) as iem1f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='H' then 1 else 0 end) as ieh1f"),
            DB::raw("sum( CASE WHEN EXTRACT(YEAR FROM (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 AND ins.sexo = 'M'  THEN 1  ELSE 0 END ) as iem2f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 and ins.sexo='H' then 1 else 0 end) as ieh2f"),
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 AND ins.sexo='M' THEN 1 ELSE 0  END ) as iem3f"),
            DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 and ins.sexo='H' then 1 else 0 end) as ieh3f"),
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34  AND ins.sexo='M' THEN 1 ELSE 0 END ) as iem4f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='H' then 1 else 0 end) as ieh4f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='M' then 1 else 0 end) as iem5f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='H' then 1 else 0 end) as ieh5f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='M' then 1 else 0 end) as iem6f"),
            db::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='H' then 1 else 0 end) as ieh6f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 AND ins.sexo='M' then 1 else 0 end) as iem7f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 and ins.sexo='H' then 1 else 0 end) as ieh7f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 AND ins.sexo='M' then 1 else 0 end) as iem8f"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 and ins.sexo='H' then 1 else 0 end) as ieh8f"),

            // --- RANGO DE EDADES EN RUBRO ESTATAL ---
            // --- *** SUMA DE HOMBRES Y MUJERES EN EDAD SIN RESTAR LGBT *** ---
            // DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 12 and 17 AND ins.sexo='M' THEN 1 ELSE 0  END ) as iem3"),
            // DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 12 and 17 and ins.sexo='H' then 1 else 0  end ) as ieh3"),
            // DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='M'  THEN 1 ELSE 0 END ) as iem4"),
            // DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='H'  then 1 else 0 end ) as ieh4"),
            // DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' then 1 else 0 end) as iem5"),
            // DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' then 1 else 0 end) as ieh5"),
            // DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' then 1 else 0 end) as iem6"),
            // DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' then 1 else 0 end) as ieh6"),

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

            DB::raw("case when c.arc='01' then nota else observaciones end as tnota"),
            DB::raw("c.observaciones_formato_t->'OBSERVACION_FIRMA' AS observaciones_firma"),
             // --- RUBRO ESTATAL ---
             DB::raw('count(distinct(ins.id)) as tinscritosest'),
             // --- SUMA DE HOMBRES Y MUJERES RESTANDOLE EL GENERO LGBTTTI+ ---
             DB::raw("SUM(CASE WHEN ins.sexo = 'M' and ins.lgbt = false or ins.sexo = 'M' and ins.lgbt is null THEN 1 ELSE 0 END) as imujerest"),
             DB::raw("SUM(CASE WHEN ins.sexo = 'H' and ins.lgbt = false or ins.sexo = 'H' and ins.lgbt is null THEN 1 ELSE 0 END) as ihombreest"),
             DB::raw("SUM(CASE WHEN ins.lgbt = true THEN 1 ELSE 0 END) as ilgbt"),

             DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresadoest"),
             // --- SUMA DE HOMBRES Y MUJERES EGRESADOS RESTANDOLE EL GENERO LGBTTTI+ ---
             DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo = 'M' and ins.lgbt = false or ins.calificacion <> 'NP' and ins.sexo = 'M' and ins.lgbt is null THEN 1 ELSE 0 END) as emujerest"),
             DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo = 'H' and ins.lgbt = false or ins.calificacion <> 'NP' and ins.sexo = 'H' and ins.lgbt is null THEN 1 ELSE 0 END) as ehombreest"),
             DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.lgbt = true THEN 1 ELSE 0 END) as elgbt"),

             // SUMA DE HOMBRES Y MUJERES CON EXONERACION TOTAL RESTANDOLE EL GENERO LGBTTTI+ ---
             DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='M' and ins.lgbt = false or ins.abrinscri='ET' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as etmujerest"),
             DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='H' and ins.lgbt = false or ins.abrinscri='ET' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ethombreest"),
             DB::raw("sum(case when ins.abrinscri='ET' and ins.lgbt = true then 1 else 0 end) as etlgbt"),
             // SUMA DE HOMBRES Y MUJERES CON EXONERACION PARCIAL RESTANDOLE EL GENERO LGBTTTI+ ---
             DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='M' and ins.lgbt = false or ins.abrinscri='EP' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as epmujerest"),
             DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='H' and ins.lgbt = false or ins.abrinscri='EP' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ephombreest"),
             DB::raw("sum(case when ins.abrinscri='EP' and ins.lgbt = true then 1 else 0 end) as eplgbt"),
             // --- *** SUMA DE HOMBRES Y MUJERES EN EDAD RESTANDOLE LGBT *** ---
             DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 AND ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0  END ) as iem1"),
             DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh1"),
             DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.lgbt = true then 1 else 0 end) as iel1"),
             DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END ) as iem2"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh2"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.lgbt = true then 1 else 0 end) as iel2"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iem3"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh3"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.lgbt = true then 1 else 0 end) as iel3"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iem4"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh4"),
             DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.lgbt then 1 else 0 end) as iel4"),

             // --- SUMA DE HOMBRES Y MUJERES EN ESCOLARIDAD RESTANDOLE LGBT ---
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true then 1 else 0 end) as ieslest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true then 1 else 0 end) as ieslest2"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true then 1 else 0 end) as ieslest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true then 1 else 0 end) as ieslest4"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true then 1 else 0 end) as ieslest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true then 1 else 0 end) as ieslest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true then 1 else 0 end) as ieslest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true then 1 else 0 end) as ieslest8"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true then 1 else 0 end) as ieslest9"),

             // --- SUMA DE HOMBRES Y MUJERES EN ACREDITADOS RESTANDOLE LGBT ---
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest2"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest4"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest8"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest9"),

             // --- SUMA DE HOMBRES Y MUJERES EN NO ACREDITO RESTANDOLE LGBT ---
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest1"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest2"),
             DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest2"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest3"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest4"),
             DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest4"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest5"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest6"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest7"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest8"),
             DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest8"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest9"),
             DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest9"),

             DB::raw('SUM(CASE WHEN ins.id_gvulnerable #-# \'1\' and ins.sexo=\'M\' and ins.lgbt = false or ins.id_gvulnerable #-# \'1\' and ins.sexo=\'M\' and ins.lgbt is null THEN 1 ELSE 0 END) as gv1m'),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '1' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '1' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv1h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '1' and ins.lgbt = true THEN 1 ELSE 0 END) as gv1l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '2' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv2m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '2' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv2h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.lgbt = true THEN 1 ELSE 0 END) as gv2l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '3' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv3m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '3' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv3h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.lgbt = true THEN 1 ELSE 0 END) as gv3l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '4' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv4m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '4' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv4h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.lgbt = true THEN 1 ELSE 0 END) as gv4l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '5' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv5m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '5' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv5h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.lgbt = true THEN 1 ELSE 0 END) as gv5l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '6' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv6m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '6' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv6h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.lgbt = true THEN 1 ELSE 0 END) as gv6l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '7' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv7m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '7' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv7h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.lgbt = true THEN 1 ELSE 0 END) as gv7l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '8' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv8m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '8' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv8h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.lgbt = true THEN 1 ELSE 0 END) as gv8l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '9' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv9m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '9' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv9h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.lgbt = true THEN 1 ELSE 0 END) as gv9l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '11' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv11m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '11' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv11h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.lgbt = true THEN 1 ELSE 0 END) as gv11l"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.sexo='M' and ins.lgbt = false or ins.id_cerss is not null and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv12m"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.sexo='H' and ins.lgbt = false or ins.id_cerss is not null and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv12h"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.lgbt = true THEN 1 ELSE 0 END) as gv12l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '13' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv13m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '13' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv13h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.lgbt = true THEN 1 ELSE 0 END) as gv13l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '15' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv15m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '15' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv15h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.lgbt = true THEN 1 ELSE 0 END) as gv15l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '16' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv16m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '16' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv16h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.lgbt = true THEN 1 ELSE 0 END) as gv16l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '17' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv17m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '17' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv17h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.lgbt = true THEN 1 ELSE 0 END) as gv17l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '18' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv18m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '18' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv18h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.lgbt = true THEN 1 ELSE 0 END) as gv18l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '19' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv19m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '19' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv19h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.lgbt = true THEN 1 ELSE 0 END) as gv19l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '20' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv20m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '20' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv20h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.lgbt = true THEN 1 ELSE 0 END) as gv20l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '21' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv21m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '21' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv21h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.lgbt = true THEN 1 ELSE 0 END) as gv21l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '22' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv22m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '22' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv22h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.lgbt = true THEN 1 ELSE 0 END) as gv22l"),
            // --- FIN RUBRO ESTATAL ---
            DB::raw("(c.hombre + c.mujer) AS totalinscripciones"),
            'c.hombre as masculinocheck',
            'c.mujer as femeninocheck',
            DB::raw("
                  COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 6 and ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 6 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 7 and 11 AND ins.sexo = 'M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 7 and 11 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 12 and 17 AND ins.sexo='M' then 1 else 0  end ))
                + COALESCE(sum(case When EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 12 and 17 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' then 1 else 0 end)) as sumatoria_total_ins_edad"
            ),

            DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD' AS observaciones_enlaces"),
            'c.status_solicitud_arc02',
            'c.arc',


        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->JOIN('tbl_municipios as m', 'm.id', '=', 'c.id_municipio')
        ->LEFTJOIN('grupos_vulnerables as gv', 'gv.id', '=', 'c.id_gvulnerable')
        ->WHERE('u.ubicacion', '=', $unidad)
        ->WHEREIN('c.status', $status)
        ->WHERE('c.status_curso', '=', 'AUTORIZADO')
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
        ->where('m.id_estado', '=', '7')
        // ->orwhere('c.arc', '=', '2')
        // ->where('c.status_solicitud_arc02', '=', 'VALIDADO')
        // ->WHERE('c.file_arc02', '!=', null)
        // ->WHERE('u.ubicacion', '=',  $unidad)
        // ->WHEREIN('c.status', $status)
        // ->WHERE('c.status_curso', '=', 'AUTORIZADO')
        // ->where('ins.status', '=', 'INSCRITO')
        // ->WHERE('c.clave', '!=', 'null')
        // ->where('ins.calificacion', '>', '0')
        // ->where('m.id_estado', '=', '7')
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
            'c.termino',
            'm.region',
            'gv.grupo'
        )
        ->orderBy('c.termino', 'asc')
        ->distinct()
        ->get();

    return $var_cursos;
}

function dataFormatoT2do($unidad, $turnado, $fecha, $mesSearch, $status) {

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
            DB::raw("sum(case when ins.discapacidad <> 'NINGUNA' then 1 else 0 end) as discapacidad"),
            DB::raw("0 as madres_solteras"), // debe ir madres solteras

            DB::raw("sum(case when ins.inmigrante = true then 1 else 0 end) as migrante"),
            DB::raw("sum(case when ins.indigena = true then 1 else 0 end) as indigena"),
            DB::raw("sum(case when ins.etnia <> NULL then 1 else 0 end) as etnia"),
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
            DB::raw("case when c.arc='01' then nota else observaciones end as tnota"),
            DB::raw("c.observaciones_formato_t->'OBSERVACION_FIRMA' AS observaciones_firma"),
            DB::raw("(c.hombre + c.mujer) AS totalinscripciones"),
            'c.hombre as masculinocheck',
            'c.mujer as femeninocheck',

            // --- RUBRO ESTATAL ---
            DB::raw('count(distinct(ins.id)) as tinscritosest'),
            // --- SUMA DE HOMBRES Y MUJERES RESTANDOLE EL GENERO LGBTTTI+ ---
            DB::raw("SUM(CASE WHEN ins.sexo = 'M' and ins.lgbt = false or ins.sexo = 'M' and ins.lgbt is null THEN 1 ELSE 0 END) as imujerest"),
            DB::raw("SUM(CASE WHEN ins.sexo = 'H' and ins.lgbt = false or ins.sexo = 'H' and ins.lgbt is null THEN 1 ELSE 0 END) as ihombreest"),
            DB::raw("SUM(CASE WHEN ins.lgbt = true THEN 1 ELSE 0 END) as ilgbt"),

            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresadoest"),
            // --- SUMA DE HOMBRES Y MUJERES EGRESADOS RESTANDOLE EL GENERO LGBTTTI+ ---
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo = 'M' and ins.lgbt = false or ins.calificacion <> 'NP' and ins.sexo = 'M' and ins.lgbt is null THEN 1 ELSE 0 END) as emujerest"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo = 'H' and ins.lgbt = false or ins.calificacion <> 'NP' and ins.sexo = 'H' and ins.lgbt is null THEN 1 ELSE 0 END) as ehombreest"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.lgbt = true THEN 1 ELSE 0 END) as elgbt"),

            // SUMA DE HOMBRES Y MUJERES CON EXONERACION TOTAL RESTANDOLE EL GENERO LGBTTTI+ ---
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='M' and ins.lgbt = false or ins.abrinscri='ET' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as etmujerest"),
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='H' and ins.lgbt = false or ins.abrinscri='ET' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ethombreest"),
            DB::raw("sum(case when ins.abrinscri='ET' and ins.lgbt = true then 1 else 0 end) as etlgbt"),
            // SUMA DE HOMBRES Y MUJERES CON EXONERACION PARCIAL RESTANDOLE EL GENERO LGBTTTI+ ---
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='M' and ins.lgbt = false or ins.abrinscri='EP' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as epmujerest"),
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='H' and ins.lgbt = false or ins.abrinscri='EP' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ephombreest"),
            DB::raw("sum(case when ins.abrinscri='EP' and ins.lgbt = true then 1 else 0 end) as eplgbt"),
            // --- *** SUMA DE HOMBRES Y MUJERES EN EDAD RESTANDOLE LGBT *** ---
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 AND ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0  END ) as iem1"),
            DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh1"),
            DB::raw("sum( Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 17 and ins.lgbt = true then 1 else 0 end) as iel1"),
            DB::raw("sum( CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29  AND ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END ) as iem2"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh2"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 18 and 29 AND ins.lgbt = true then 1 else 0 end) as iel2"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iem3"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh3"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 30 and 59 AND ins.lgbt = true then 1 else 0 end) as iel3"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 AND ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iem4"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' and ins.lgbt = false or EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieh4"),
            DB::raw("sum( case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 60 and ins.lgbt then 1 else 0 end) as iel4"),

            // --- SUMA DE HOMBRES Y MUJERES EN ESCOLARIDAD RESTANDOLE LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true then 1 else 0 end) as ieslest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true then 1 else 0 end) as ieslest2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true then 1 else 0 end) as ieslest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true then 1 else 0 end) as ieslest4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true then 1 else 0 end) as ieslest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true then 1 else 0 end) as ieslest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true then 1 else 0 end) as ieslest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true then 1 else 0 end) as ieslest8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.lgbt is null then 1 else 0 end) as iesmest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.lgbt is null then 1 else 0 end) as ieshest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true then 1 else 0 end) as ieslest9"),

            // --- SUMA DE HOMBRES Y MUJERES EN ACREDITADOS RESTANDOLE LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aesmest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion != 'NP' and ins.lgbt is null then 1 else 0 end) as aeshest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true and ins.calificacion != 'NP' then 1 else 0 end) as aeslest9"),

            // --- SUMA DE HOMBRES Y MUJERES EN NO ACREDITO RESTANDOLE LGBT ---
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest1"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='PRIMARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest2"),
            DB::raw("sum(case when ins.escolaridad='PRIMARIA TERMINADA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest2"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA INCONCLUSA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest3"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='SECUNDARIA TERMINADA' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest4"),
            DB::raw("sum(case when ins.escolaridad='SECUNDARIA TERMINADA' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest4"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest5"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL MEDIO SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest6"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR INCONCLUSO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest7"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest8"),
            DB::raw("sum(case when ins.escolaridad='NIVEL SUPERIOR TERMINADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest8"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='M' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naesmest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt = false or ins.escolaridad='POSTGRADO' and ins.sexo='H' and ins.calificacion = 'NP' and ins.lgbt is null then 1 else 0 end) as naeshest9"),
            DB::raw("sum(case when ins.escolaridad='POSTGRADO' and ins.lgbt = true and ins.calificacion = 'NP' then 1 else 0 end) as naeslest9"),

            DB::raw('SUM(CASE WHEN ins.id_gvulnerable #-# \'1\' and ins.sexo=\'M\' and ins.lgbt = false or ins.id_gvulnerable #-# \'1\' and ins.sexo=\'M\' and ins.lgbt is null THEN 1 ELSE 0 END) as gv1m'),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '1' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '1' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv1h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '1' and ins.lgbt = true THEN 1 ELSE 0 END) as gv1l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '2' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv2m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '2' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv2h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '2' and ins.lgbt = true THEN 1 ELSE 0 END) as gv2l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '3' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv3m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '3' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv3h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '3' and ins.lgbt = true THEN 1 ELSE 0 END) as gv3l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '4' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv4m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '4' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv4h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '4' and ins.lgbt = true THEN 1 ELSE 0 END) as gv4l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '5' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv5m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '5' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv5h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '5' and ins.lgbt = true THEN 1 ELSE 0 END) as gv5l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '6' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv6m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '6' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv6h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '6' and ins.lgbt = true THEN 1 ELSE 0 END) as gv6l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '7' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv7m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '7' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv7h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '7' and ins.lgbt = true THEN 1 ELSE 0 END) as gv7l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '8' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv8m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '8' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv8h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '8' and ins.lgbt = true THEN 1 ELSE 0 END) as gv8l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '9' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv9m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '9' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv9h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '9' and ins.lgbt = true THEN 1 ELSE 0 END) as gv9l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '11' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv11m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '11' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv11h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '11' and ins.lgbt = true THEN 1 ELSE 0 END) as gv11l"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.sexo='M' and ins.lgbt = false or ins.id_cerss is not null and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv12m"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.sexo='H' and ins.lgbt = false or ins.id_cerss is not null and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv12h"),
             DB::raw("SUM(CASE WHEN ins.id_cerss is not null and ins.lgbt = true THEN 1 ELSE 0 END) as gv12l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '13' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv13m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '13' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv13h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '13' and ins.lgbt = true THEN 1 ELSE 0 END) as gv13l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '15' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv15m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '15' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv15h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '15' and ins.lgbt = true THEN 1 ELSE 0 END) as gv15l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '16' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv16m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '16' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv16h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '16' and ins.lgbt = true THEN 1 ELSE 0 END) as gv16l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '17' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv17m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '17' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv17h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '17' and ins.lgbt = true THEN 1 ELSE 0 END) as gv17l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '18' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv18m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '18' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv18h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '18' and ins.lgbt = true THEN 1 ELSE 0 END) as gv18l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '19' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv19m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '19' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv19h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '19' and ins.lgbt = true THEN 1 ELSE 0 END) as gv19l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '20' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv20m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '20' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv20h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '20' and ins.lgbt = true THEN 1 ELSE 0 END) as gv20l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '21' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv21m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '21' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv21h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '21' and ins.lgbt = true THEN 1 ELSE 0 END) as gv21l"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.sexo='M' and ins.lgbt = false or ins.id_gvulnerable #-# '22' and ins.sexo='M' and ins.lgbt is null THEN 1 ELSE 0 END) as gv22m"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.sexo='H' and ins.lgbt = false or ins.id_gvulnerable #-# '22' and ins.sexo='H' and ins.lgbt is null THEN 1 ELSE 0 END) as gv22h"),
             DB::raw("SUM(CASE WHEN ins.id_gvulnerable #-# '22' and ins.lgbt = true THEN 1 ELSE 0 END) as gv22l"),
           // --- FIN RUBRO ESTATAL ---

            DB::raw("to_char(c.fecha_turnado, 'TMMONTH') AS fechaturnado"), // new
            DB::raw("c.observaciones_formato_t->'COMENTARIOS_UNIDAD' AS observaciones_unidad"), // new
            DB::raw("c.memos->'ENLACE_TURNADO_RETORNO'->>'NUMERO_MEMO' AS numero_memo_retorno1"), //new
            DB::raw("c.observaciones_formato_t->'OBSERVACION_ENLACES_RETORNO_UNIDAD' AS comentario_enlaces_retorno"), //new
            DB::raw("
                  COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 15 and ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 15 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 15 and 19 AND ins.sexo = 'M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 15 and 19 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 20 and 24 AND ins.sexo='M' then 1 else 0  end ))
                + COALESCE(sum(case When EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between '20' and '24' and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 55 and 64 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 55 and 64 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 65 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 65 and ins.sexo='H' then 1 else 0 end)) as sumatoria_total_ins_edad"
            ),
            /* DB::raw("COALESCE(sum( case when EXTRACT( year from (age(c.termino, ap.fecha_nacimiento))) < 15 and ap.sexo='   ' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) < 15 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(YEAR FROM (AGE(c.termino, ap.fecha_nacimiento))) between 15 and 19 AND ap.sexo = 'FEMENINO'
                THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 15 and 19 and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 20 and 24 AND ap.sexo='FEMENINO' THEN 1 ELSE 0  END )) + COALESCE(sum( Case When EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '20' and '24' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( CASE WHEN EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34  AND ap.sexo='FEMENINO' THEN 1 ELSE 0 END )) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 25 and 34
                AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 35 and 44 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54
                AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum(  case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 45 and 54 AND ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between 55 and 64 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) between '55' and '64' and ap.sexo='MASCULINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 AND ap.sexo='FEMENINO' then 1 else 0 end)) + COALESCE(sum( case when EXTRACT(year from (age(c.termino,ap.fecha_nacimiento))) >= 65 and ap.sexo='MASCULINO' then 1 else 0 end)) as sumatoria_total_ins_edad"), */
            $status == 'TURNADO_DTA'
                ? DB::raw("c.observaciones_formato_t->'OBSERVACION_RETORNO_UNIDAD' AS observaciones_enlaces")
                : ($status == 'REVISION_DTA'
                    ? DB::raw("c.observaciones_formato_t->'OBSERVACIONES_REVISION_DIRECCION_DTA'->>'OBSERVACION_REVISION_JEFE_DTA' AS observaciones_enlaces")
                    : DB::raw("c.observaciones_formato_t->'OBSERVACION_DIRECCIONDTA_TO_PLANEACION'->>'OBSERVACION_ENVIO_PLANEACION' AS observacion_envio_to_planeacion")),

        )
        // ->JOIN('instructores as i', 'c.id_instructor', '=', 'i.id')
        // ->JOIN('instructor_perfil as ip', 'i.id', '=', 'ip.numero_control')
        // ->JOIN('especialidad_instructores as ei', 'ip.id', '=', 'ei.perfilprof_id')
        /*->JOIN('especialidades as e', function ($join) {
            $join->on('ei.especialidad_id', '=', 'e.id');
            $join->on('c.espe', '=', 'e.nombre');
        })*/
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        /*->JOIN($temptblinner, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })*/
        // ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->JOIN('tbl_municipios as m', 'm.id', '=', 'c.id_municipio')
        ->LEFTJOIN('grupos_vulnerables as gv', 'gv.id', '=', 'c.id_gvulnerable')
        ->whereMonth('c.fecha_turnado', $mesSearch) // new
        ->WHERE('c.status', '=', $status) // new
        ->WHERE('c.status_curso', '=', 'AUTORIZADO')
        ->WHEREIN('c.turnado', $turnado)
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
            'gv.grupo'
        )
        ->distinct();

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

/* function dataFormatoTSaveData($id)
{

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
            DB::raw('count(distinct(ins.id)) as tinscritos'),
            DB::raw("SUM(CASE WHEN ins.sexo='M' THEN 1 ELSE 0 END) as imujer"),
            DB::raw("SUM(CASE WHEN ins.sexo='H' THEN 1 ELSE 0 END) as ihombre"),

            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='M' THEN 1 ELSE 0 END) as emujer"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' and ins.sexo='H' THEN 1 ELSE 0 END) as ehombre"),
            DB::raw("SUM(CASE WHEN ins.calificacion = 'NP' THEN 1 ELSE 0 END) as desertado"),
            DB::raw("SUM(DISTINCT(ins.costo)) as costo"),
            DB::raw("SUM(ins.costo) as ctotal"),

            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='M' then 1 else 0 end) as etmujer"),
            DB::raw("sum(case when ins.abrinscri='ET' and ins.sexo='H' then 1 else 0 end) as ethombre"),
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='M' then 1 else 0 end) as epmujer"),
            DB::raw("sum(case when ins.abrinscri='EP' and ins.sexo='H' then 1 else 0 end) as ephombre"),
            'c.cespecifico',
            'c.mvalida',
            'c.efisico',
            'c.nombre',
            'c.instructor_escolaridad as grado_profesional',
            'c.instructor_titulo as estatus',
            'c.instructor_sexo as sexo',
            'c.instructor_mespecialidad as memorandum_validacion',
            'c.mexoneracion',
            DB::raw("sum(case when ins.empleado = true then 1 else 0 end) as empleado"),
            DB::raw("sum(case when ins.empleado = false then 1 else 0 end) as desempleado"),
            DB::raw("sum(case when ins.discapacidad <> 'NINGUNA' then 1 else 0 end) as discapacidad"),
            DB::raw("0 as madres_solteras"), // debe ir madres solteras

            DB::raw("sum(case when ins.inmigrante = true then 1 else 0 end) as migrante"),
            DB::raw("sum(case when ins.indigena = true then 1 else 0 end) as indigena"),
            DB::raw("sum(case when ins.etnia <> NULL then 1 else 0 end) as etnia"),
            'c.programa',
            'c.muni',
            'c.ze',
            'c.depen',
            'c.cgeneral',
            'c.sector',
            'c.mpaqueteria',

            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='M' then 1 else 0 end) as iem1"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) < 15 and ins.sexo='H' then 1 else 0 end) as ieh1"),
            DB::raw("sum(CASE WHEN EXTRACT(YEAR FROM (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 AND ins.sexo = 'M'  THEN 1 ELSE 0 END) as iem2"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 15 and 19 and ins.sexo='H' then 1 else 0 end) as ieh2"),
            DB::raw("sum(CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 AND ins.sexo='M' THEN 1 ELSE 0 END) as iem3"),
            DB::raw("sum(Case When EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 20 and 24 and ins.sexo='H' then 1 else 0 end) as ieh3"),
            DB::raw("sum(CASE WHEN EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='M' THEN 1 ELSE 0 END) as iem4"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='H' then 1 else 0 end) as ieh4"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='M' then 1 else 0 end) as iem5"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='H' then 1 else 0 end) as ieh5"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='M' then 1 else 0 end) as iem6"),
            db::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='H' then 1 else 0 end) as ieh6"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 AND ins.sexo='M' then 1 else 0 end) as iem7"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) between 55 and 64 and ins.sexo='H' then 1 else 0 end) as ieh7"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 AND ins.sexo='M' then 1 else 0 end) as iem8"),
            DB::raw("sum(case when EXTRACT(year from (age(c.inicio, ins.fecha_nacimiento))) >= 65 and ins.sexo='H' then 1 else 0 end) as ieh8"),

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

            DB::raw("case when c.arc='01' then nota else observaciones end as tnota"),
            DB::raw("c.observaciones_formato_t->'OBSERVACION_FIRMA' AS observaciones_firma"),
            DB::raw("(c.hombre + c.mujer) AS totalinscripciones"),
            'c.hombre as masculinocheck',
            'c.mujer as femeninocheck',
            DB::raw("c.observaciones_formato_t->'COMENTARIOS_UNIDAD' AS observaciones_unidad"), // new
            DB::raw("c.memos->'ENLACE_TURNADO_RETORNO'->>'NUMERO_MEMO' AS numero_memo_retorno1"), //new
            DB::raw("c.observaciones_formato_t->'OBSERVACION_ENLACES_RETORNO_UNIDAD' AS comentario_enlaces_retorno"), //new
            DB::raw("
                  COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 15 and ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) < 15 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 15 and 19 AND ins.sexo = 'M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 15 and 19 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 20 and 24 AND ins.sexo='M' then 1 else 0  end ))
                + COALESCE(sum(case When EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between '20' and '24' and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='M' then 1 else 0 end ))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 25 and 34 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 35 and 44 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 45 and 54 AND ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 55 and 64 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) between 55 and 64 and ins.sexo='H' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 65 AND ins.sexo='M' then 1 else 0 end))
                + COALESCE(sum(case when EXTRACT(year from(age(c.termino, ins.fecha_nacimiento))) >= 65 and ins.sexo='H' then 1 else 0 end)) as sumatoria_total_ins_edad"
            ),
            DB::raw("c.observaciones_formato_t->'OBSERVACION_DIRECCIONDTA_TO_PLANEACION'->>'OBSERVACION_ENVIO_PLANEACION' AS observacion_envio_to_planeacion")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->WHERE('c.status', '=', 'REPORTADO') // new
        ->WHERE('c.status_curso', '=', 'AUTORIZADO')
        ->WHERE('c.turnado', '=', 'PLANEACION_TERMINADO')
        ->WHERE('c.id', '=', $id)
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
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
            'c.nota'
        )
        ->distinct()
        ->first();

    return $var_cursos;
}
*/
