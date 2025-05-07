<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ValidacionServicio
{
    protected $instructores;
    public function __construct($instructores) {
        $this->instructores = collect($instructores);
    }

    public function AlumntDisponibleFechaHora($qry) {
        // alumnos disponible TODO
        $id_curso     = $qry->id;
        $fechaInicio  = $qry->inicio;
        $fechaTermino = $qry->termino;
        $horaInicio = date('H:i:s', strtotime($qry->hini));
        $horaTermino = date('H:i:s', strtotime($qry->hfin));
        $disponibles = [];
        foreach ($this->instructores as $ins) {
            $hay_conflicto = DB::table('alumnos_registro as ar')
                ->select('ap.curp')
                ->leftJoin('alumnos_pre as ap', 'ar.id_pre', 'ap.id')
                ->leftJoin('agenda as a', 'ar.folio_grupo', 'a.id_curso')
                ->leftJoin('tbl_cursos as tc', 'ar.folio_grupo', 'tc.folio_grupo')
                ->where('tc.status', '<>', 'CANCELADO')
                ->where('ar.eliminado', false)
                ->where('ar.folio_grupo', '<>', $id_curso)
                ->whereRaw("((date(a.start) <= ? and date(a.end) >= ?) OR (date(a.start) <= ? and date(a.end) >= ?))", [
                    $fechaInicio, $fechaInicio, $fechaTermino, $fechaTermino
                ])
                ->whereRaw("((cast(a.start as time) <= ? and cast(a.end as time) > ?) OR (cast(a.start as time) < ? and cast(a.end as time) >= ?))", [
                    $horaInicio, $horaInicio, $horaTermino, $horaTermino
                ])
                ->whereIn('ar.id_pre', function ($query) use ($id_curso) {
                    $query->select('id_pre')
                          ->from('alumnos_registro')
                          ->where('folio_grupo', $id_curso)
                          ->where('eliminado', false);
                })
                ->exists();

                if (!$hay_conflicto) {
                    $disponibles[] = $ins;
                }
        }
        return $disponibles;
    }

    public function InstNoRebase8Horas($agenda) {
        $instructoresValidos = [];

        foreach ($this->instructores as $ins) {
            $excede = false;

            foreach ($agenda as $value) {
                $periodo = CarbonPeriod::create($value->start, $value->end);
                $horaInicio = date("H:i", strtotime($value->start));
                $horaTermino = date("H:i", strtotime($value->end));
                $minutos_curso = Carbon::parse($horaTermino)->diffInMinutes($horaInicio);

                foreach ($periodo as $fecha) {
                    $fechaStr = $fecha->format('Y-m-d');
                    // Obtener todas las actividades del instructor en esa fecha
                    $actividades = DB::table('agenda')
                        ->join('tbl_cursos', 'agenda.id_curso', '=', 'tbl_cursos.folio_grupo')
                        ->select(
                            DB::raw("CAST(agenda.start AS TIME) as hini"),
                            DB::raw("CAST(agenda.end AS TIME) as hfin")
                        )
                        ->where('tbl_cursos.status', '<>', 'CANCELADO')
                        ->where('agenda.id_instructor', $ins->id)
                        ->whereDate('agenda.start', '<=', $fechaStr)
                        ->whereDate('agenda.end', '>=', $fechaStr)
                        ->get();
                    $minutosTotales = 0;

                    foreach ($actividades as $act) {
                        $hiniAct = Carbon::parse($act->hini);
                        $hfinAct = Carbon::parse($act->hfin);
                        $minutosTotales += $hfinAct->diffInMinutes($hiniAct);
                    }

                    // Si rebasa las 8 horas con el curso nuevo
                    if (($minutosTotales + $minutos_curso) > 480) {
                        $excede = true;
                        break 2; // Rompe ambos foreach anidados (fecha y agenda)
                    }
                }
            }

            if (!$excede) {
                $instructoresValidos[] = $ins;
            }
        }
        return $instructoresValidos;
    }

    public function InstNoRebase40HorasSem($agenda){
        $instructoresValidos = [];

        foreach ($this->instructores as $instructor) {

            //agrupar minutos nuevos por semana
            $minutosNuevosPorSemana = [];
            foreach ($agenda as $item) {
                if ($item->id_instructor != $instructor->id) {
                    continue; // Solo evaluar actividades asignadas a este instructor
                }
                $periodo = CarbonPeriod::create($item->start, $item->end);
                $horaInicio = Carbon::parse($item->start);
                $horaTermino = Carbon::parse($item->end);
                $minutosCurso = $horaTermino->diffInMinutes($horaInicio);

                foreach ($periodo as $fecha) {
                    $semana = Carbon::parse($fecha)->startOfWeek()->format('Y-m-d');

                    if (!isset($minutosNuevosPorSemana[$semana])) {
                        $minutosNuevosPorSemana[$semana] = 0;
                    }

                    $minutosNuevosPorSemana[$semana] += $minutosCurso;
                }
            }

            $esValido = true;

            // 2. Validar minutos semanales contra lo ya registrado en BD

            foreach ($minutosNuevosPorSemana as $semanaInicioStr => $minutosNuevos) {
                $semanaInicio = Carbon::parse($semanaInicioStr);
                $semanaFin = $semanaInicio->copy()->endOfWeek();

                $minutosExistentes = DB::table(DB::raw("
                (
                    SELECT
                        generate_series(agenda.start, agenda.end, '1 day'::interval)::date as dia,
                        (CAST(agenda.end AS time) - CAST(agenda.start AS time))::time as duracion
                    FROM agenda
                    LEFT JOIN tbl_cursos ON agenda.id_curso = tbl_cursos.folio_grupo
                    WHERE agenda.id_instructor = {$instructor->id}
                        AND tbl_cursos.status != 'CANCELADO'
                ) as t"))
                ->whereBetween('dia', [$semanaInicio->format('Y-m-d'), $semanaFin->format('Y-m-d')])
                ->value(DB::raw('SUM(EXTRACT(hour FROM duracion) * 60 + EXTRACT(minute FROM duracion))'));

                $totalMinutos = ($minutosExistentes ?? 0) + $minutosNuevos;

                if ($totalMinutos > 2400) {
                    $esValido = false;
                    break;
                }
            }

            if ($esValido) {
                $instructoresValidos[] = $instructor;
            }
        }

        return $instructoresValidos;
    }

    public function InstActividadDescanso($folio_grupo) {
        // $array_instructores,
        ///VALIDACIÓN 150 dias de actividad y 30 días naturales de RECESO
        $grupo = DB::Table('agenda')->Where('id_curso',$folio_grupo)->Get();
        $newArray = [];
        foreach($this->instructores as $instructor) {
            $receso =  DB::table('tbl_cursos as tc')->where('id_instructor',$instructor->id)
            ->where(function($query) use ($folio_grupo){
                $query->where('tc.status_curso','<>','CANCELADO')->orWherenull('tc.status_curso')->OrWhere('folio_grupo',$folio_grupo);
            })
            ->where('tc.inicio','>',DB::raw("
            COALESCE(
                (select max(inicio) from tbl_cursos as c where c.id_instructor = $instructor->id
                    and COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp )
                    from tbl_cursos as tc where tc.id_instructor = $instructor->id and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0)>=30 )
                    , (select min(inicio)::timestamp - interval '1 day' from tbl_cursos where id_instructor = $instructor->id))
            "))
            ->value(DB::raw("DATE_PART('day', max(tc.termino)::timestamp - min(tc.inicio)::timestamp)+1"));

            if($receso<=150){
                array_push($newArray,$instructor);
            }
        }
        return $newArray;
    }

    public function InstNoTraslapeFechaHoraConOtroCurso($folio_grupo) {
        // $array_instructores,
        //DISPONIBILIDAD FECHA Y HORA
        $grupos = DB::Table('agenda')->Where('id_curso',$folio_grupo)->Get();
        $newArray = array();
        foreach($this->instructores as $instructor) {
            $traslape = false;
            foreach($grupos as $grupo) {
                $fechaInicio = date("Y-m-d", strtotime($grupo->start));
                $fechaTermino = date("Y-m-d", strtotime($grupo->end));
                $horaInicio = date("H:i", strtotime($grupo->start));
                $horaTermino = date("H:i", strtotime($grupo->end));
                $duplicado = DB::table('agenda as a')
                    ->leftJoin('tbl_cursos as tc','a.id_curso','tc.folio_grupo')
                    ->where('a.id_instructor', $grupo->id_instructor)
                    ->where('tc.status','<>','CANCELADO')
                    // ->Where('id_curso', $folio_grupo)
                    ->whereRaw("((date(a.start) <= '$fechaInicio' and date(a.end) >= '$fechaInicio') OR (date(a.start) <= '$fechaTermino' and date(a.end) >= '$fechaTermino'))")
                    ->whereRaw("((cast(a.start as time) <= '$horaInicio' and cast(a.end as time) > '$horaInicio') OR (cast(a.start as time) < '$horaTermino' and cast(a.end as time) >= '$horaTermino'))")
                    ->exists();
                if ($duplicado) {
                    $traslape = true;
                }
            }

            if(!$traslape) {
                array_push($newArray,$instructor);
            }
        }
        return $newArray;
    }
}
