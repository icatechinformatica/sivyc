<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ValidacionServicioVb
{
    // protected $instructores;
    public function __construct() {
        // $this->instructores = collect($instructores);
    }

    // public function InstNoRebase8Horas($instructores, $agenda) {
    //     $instructoresValidos = [];

    //     foreach ($instructores as $ins) {
    //         $excede = false;

    //         foreach ($agenda as $value) {
    //             $periodo = CarbonPeriod::create($value->start, $value->end);
    //             $horaInicio = date("H:i", strtotime($value->start));
    //             $horaTermino = date("H:i", strtotime($value->end));
    //             $minutos_curso = Carbon::parse($horaTermino)->diffInMinutes($horaInicio);

    //             foreach ($periodo as $fecha) {
    //                 $fechaStr = $fecha->format('Y-m-d');
    //                 // Obtener todas las actividades del instructor en esa fecha
    //                 $actividades = DB::table('agenda')
    //                     ->join('tbl_cursos', 'agenda.id_curso', '=', 'tbl_cursos.folio_grupo')
    //                     ->select(
    //                         DB::raw("CAST(agenda.start AS TIME) as hini"),
    //                         DB::raw("CAST(agenda.end AS TIME) as hfin")
    //                     )
    //                     ->where('tbl_cursos.status', '<>', 'CANCELADO')
    //                     ->where('agenda.id_instructor', $ins->id)
    //                     ->whereDate('agenda.start', '<=', $fechaStr)
    //                     ->whereDate('agenda.end', '>=', $fechaStr)
    //                     ->get();
    //                 $minutosTotales = 0;

    //                 foreach ($actividades as $act) {
    //                     $hiniAct = Carbon::parse($act->hini);
    //                     $hfinAct = Carbon::parse($act->hfin);
    //                     $minutosTotales += $hfinAct->diffInMinutes($hiniAct);
    //                 }

    //                 // Si rebasa las 8 horas con el curso nuevo
    //                 if (($minutosTotales + $minutos_curso) > 480) {
    //                     $excede = true;
    //                     break 2; // Rompe ambos foreach anidados (fecha y agenda)
    //                 }
    //             }
    //         }

    //         if (!$excede) {
    //             $instructoresValidos[] = $ins;
    //         }
    //     }
    //     return $instructoresValidos;
    // }

    ##Nuevo codigo optimizado
    public function InstNoRebase8Horas($instructores, $agenda)
    {
        $instructoresValidos = [];

        // 1. Extraer fechas únicas del periodo total del nuevo curso
        $fechasPeriodo = [];
        foreach ($agenda as $value) {
            $periodo = CarbonPeriod::create($value->start, $value->end);
            foreach ($periodo as $fecha) {
                $fechasPeriodo[$fecha->format('Y-m-d')] = true;
            }
        }

        $fechasPeriodo = array_keys($fechasPeriodo);

        // 2. Obtener todas las actividades de todos los instructores en esas fechas
        $idsInstructores = collect($instructores)->pluck('id');
        $agendaExistente = DB::table('agenda')
            ->join('tbl_cursos', 'agenda.id_curso', '=', 'tbl_cursos.folio_grupo')
            ->select(
                'agenda.id_instructor',
                DB::raw("DATE(agenda.start) as fecha"),
                DB::raw("CAST(agenda.start AS TIME) as hini"),
                DB::raw("CAST(agenda.end AS TIME) as hfin")
            )
            ->where('tbl_cursos.status', '<>', 'CANCELADO')
            ->whereIn('agenda.id_instructor', $idsInstructores)
            ->whereIn(DB::raw("DATE(agenda.start)"), $fechasPeriodo)
            ->get();

        // 3. Agrupar por instructor y fecha para saber cuánto tiempo ya tienen ocupado
        $minutosOcupados = [];
        foreach ($agendaExistente as $item) {
            $hini = Carbon::parse($item->hini);
            $hfin = Carbon::parse($item->hfin);
            $minutos = $hfin->diffInMinutes($hini);

            $minutosOcupados[$item->id_instructor][$item->fecha] =
                ($minutosOcupados[$item->id_instructor][$item->fecha] ?? 0) + $minutos;
        }

        // 4. Revisar si el nuevo curso rebasa el límite en alguna fecha
        foreach ($instructores as $ins) {
            $excede = false;

            foreach ($agenda as $value) {
                $periodo = CarbonPeriod::create($value->start, $value->end);
                $horaInicio = Carbon::parse($value->start);
                $horaTermino = Carbon::parse($value->end);
                $minutosCurso = $horaTermino->diffInMinutes($horaInicio);

                foreach ($periodo as $fecha) {
                    $fechaStr = $fecha->format('Y-m-d');
                    $minutosExistentes = $minutosOcupados[$ins->id][$fechaStr] ?? 0;

                    if (($minutosExistentes + $minutosCurso) > 480) {
                        $excede = true;
                        break 2; // Rompe ambos foreach
                    }
                }
            }

            if (!$excede) {
                $instructoresValidos[] = $ins;
            }
        }
        return $instructoresValidos;
    }



    public function InstNoRebase40HorasSem($instructores, $agenda){
        $instructoresValidos = [];

        foreach ($instructores as $instructor) {

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

    // public function InstValida150Dias($instructores, $folio_grupo) {
    //     ///VALIDACIÓN 150 dias de actividad y 30 días naturales de RECESO
    //     $newArray = [];
    //     foreach($instructores as $instructor) {
    //         $receso =  DB::table('tbl_cursos as tc')->where('id_instructor',$instructor->id)
    //         ->where(function($query) use ($folio_grupo){
    //             $query->where('tc.status_curso','<>','CANCELADO')->orWherenull('tc.status_curso')->OrWhere('folio_grupo',$folio_grupo);
    //         })
    //         ->where('tc.inicio','>',DB::raw("
    //         COALESCE(
    //             (select max(inicio) from tbl_cursos as c where c.id_instructor = $instructor->id
    //                 and COALESCE((select DATE_PART('day', tc.inicio::timestamp - c.termino::timestamp )
    //                 from tbl_cursos as tc where tc.id_instructor = $instructor->id and tc.inicio>c.inicio order by tc.inicio ASC limit 1  )-1,0)>=30 )
    //                 , (select min(inicio)::timestamp - interval '1 day' from tbl_cursos where id_instructor = $instructor->id))
    //         "))
    //         ->value(DB::raw("DATE_PART('day', max(tc.termino)::timestamp - min(tc.inicio)::timestamp)+1"));

    //         if($receso<=150){
    //             array_push($newArray,$instructor);
    //         }
    //     }
    //     return $newArray;
    // }


    ## Nuevo codigo optimizado
    public function InstValida150Dias($instructores, $folio_grupo)
    {
        $validados = [];

        foreach ($instructores as $instructor) {
            // Obtener cursos del instructor filtrados
            $cursos = DB::table('tbl_cursos as tc')
                ->where('tc.id_instructor', $instructor->id)
                ->where(function ($query) use ($folio_grupo) {
                    $query->where('tc.status_curso', '<>', 'CANCELADO')
                        ->orWhereNull('tc.status_curso')
                        ->orWhere('tc.folio_grupo', $folio_grupo);
                })
                ->orderBy('tc.inicio')
                ->get(['tc.inicio', 'tc.termino']);

            if ($cursos->isEmpty()) continue;

            // Calcular diferencia de fechas y buscar recesos >= 30 días
            $max_inicio = null;
            for ($i = 0; $i < count($cursos) - 1; $i++) {
                $diff = Carbon::parse($cursos[$i+1]->inicio)->diffInDays($cursos[$i]->termino);
                if ($diff >= 30) {
                    $max_inicio = Carbon::parse($cursos[$i]->inicio);
                }
            }

            // Si no se encontró un receso válido, usar el inicio del primer curso -1 día
            $inicio_limite = $max_inicio ?? Carbon::parse($cursos->min('inicio'))->subDay();

            // Filtrar cursos después del receso
            $filtrados = $cursos->filter(function ($curso) use ($inicio_limite) {
                return Carbon::parse($curso->inicio)->gt($inicio_limite);
            });

            // Calcular duración total de cursos activos posteriores al receso
            $dias = $filtrados->reduce(function ($carry, $curso) {
                return $carry + Carbon::parse($curso->termino)->diffInDays(Carbon::parse($curso->inicio)) + 1;
            }, 0);

            if ($dias <= 150) {
                $validados[] = $instructor;
            }
        }

        return $validados;
    }


    // public function InstNoTraslapeFechaHoraConOtroCurso($instructores, $grupos) {
    //     //DISPONIBILIDAD FECHA Y HORA
    //     $newArray = array();
    //     foreach($instructores as $instructor) {
    //         $traslape = false;
    //         foreach($grupos as $grupo) {
    //             $fechaInicio = date("Y-m-d", strtotime($grupo->start));
    //             $fechaTermino = date("Y-m-d", strtotime($grupo->end));
    //             $horaInicio = date("H:i", strtotime($grupo->start));
    //             $horaTermino = date("H:i", strtotime($grupo->end));
    //             $duplicado = DB::table('agenda as a')
    //                 ->leftJoin('tbl_cursos as tc','a.id_curso','tc.folio_grupo')
    //                 ->where('a.id_instructor', $instructor->id)
    //                 ->where('tc.status','<>','CANCELADO')
    //                 // ->Where('id_curso', $folio_grupo)
    //                 ->whereRaw("((date(a.start) <= '$fechaInicio' and date(a.end) >= '$fechaInicio') OR (date(a.start) <= '$fechaTermino' and date(a.end) >= '$fechaTermino'))")
    //                 ->whereRaw("((cast(a.start as time) <= '$horaInicio' and cast(a.end as time) > '$horaInicio') OR (cast(a.start as time) < '$horaTermino' and cast(a.end as time) >= '$horaTermino'))")
    //                 ->exists();
    //             if ($duplicado) {
    //                 $traslape = true;
    //             }
    //         }

    //         if(!$traslape) {
    //             array_push($newArray,$instructor);
    //         }
    //     }
    //     return $newArray;
    // }

    ### Nuevo codigo para optimizar
    public function InstNoTraslapeFechaHoraConOtroCurso($instructores, $grupos)
    {
        $newArray = [];

        $idsInstructores = collect($instructores)->pluck('id')->all();

        // Preconsulta todas las agendas relevantes
        $agendaExistente = DB::table('agenda as a')
            ->leftJoin('tbl_cursos as tc', 'a.id_curso', '=', 'tc.folio_grupo')
            ->select(
                'a.id_instructor',
                'a.start as start',
                'a.end as end'
            )
            ->whereIn('a.id_instructor', $idsInstructores)
            ->where('tc.status', '<>', 'CANCELADO')
            ->get()
            ->groupBy('id_instructor');

        foreach ($instructores as $instructor) {
            $traslape = false;
            $actividades = $agendaExistente->get($instructor->id, collect());

            foreach ($grupos as $grupo) {
                $grupoInicio = Carbon::parse($grupo->start);
                $grupoFin = Carbon::parse($grupo->end);

                foreach ($actividades as $actividad) {
                    $actividadInicio = Carbon::parse($actividad->start);
                    $actividadFin = Carbon::parse($actividad->end);

                    // Validación de traslape: [start1 < end2] && [start2 < end1]
                    if ($grupoInicio < $actividadFin && $actividadInicio < $grupoFin) {
                        $traslape = true;
                        break 2; // Salir de los dos ciclos
                    }
                }
            }

            if (!$traslape) {
                $newArray[] = $instructor;
            }
        }

        return $newArray;
    }


    public function InstAlfaNoBecados($instructores){
        $instructoresValidos = [];
        foreach ($instructores as $instructor) {
            # ciclo para recorrer los registros vamos a descartar a los que no son alfa
            $alfa = DB::table('instructores')
            ->where('id', $instructor->id)
            ->where('instructor_alfa', true)
            ->whereRaw("datos_alfa->'subproyectos'->>'chiapas puede' = ?", ['voluntario']) // Condición de "voluntario"
            ->exists();

            if ($alfa) {
                $instructoresValidos[] = $instructor;
                break;
            }
        }
        return $instructoresValidos;
    }
}
