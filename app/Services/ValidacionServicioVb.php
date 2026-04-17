<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ValidacionServicioVb
{
    // protected $instructores;
    public function __construct()
    {
        // $this->instructores = collect($instructores);
    }

    ##Funcion para verificar que el instructor no rebase las 8 horas diarias
    public function InstNoRebase8Horas($instructores, $agenda)
    {
        $instructoresValidos = [];

        foreach ($instructores as $ins) {
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
                        ->where('tbl_cursos.id_instructor', '=', $ins->id)
                        ->where('agenda.id_instructor', $ins->id)
                        ->whereDate('agenda.start', '<=', $fechaStr)
                        ->whereDate('agenda.end', '>=', $fechaStr)
                        ->where('agenda.id_curso', '<>', $value->id_curso)
                        ->where(function ($query) use ($value) {
                            $query->where('tbl_cursos.status_curso', '<>', 'CANCELADO')
                                ->orWhereNull('tbl_cursos.status_curso');
                        })
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

    ##Funcion para verificar que el instructor no rebase las 40 horas a la semana
    public function InstNoRebase40HorasSem($instructores, $folio_grupo)
    {
        $instructoresValidos = [];
        // Obtener la agenda del nuevo curso con Carbon ya parseado
        $nuevasActividades = DB::table('agenda')
            ->where('id_curso', $folio_grupo)
            ->get()
            ->map(function ($item) {
                $item->start = Carbon::parse($item->start);
                $item->end   = Carbon::parse($item->end);
                return $item;
            });

        foreach ($instructores as $instructor) {

            // 1. Obtener actividades actuales del instructor (no canceladas, excluyendo el curso actual)
            $actividadesExistentes = DB::table('agenda')
                ->join('tbl_cursos', 'agenda.id_curso', '=', 'tbl_cursos.folio_grupo')
                ->where('agenda.id_instructor', $instructor->id)
                ->where('agenda.id_curso', '<>', $folio_grupo)  // excluir el curso actual para no doble-contar
                ->where(function ($q) {
                    $q->where('tbl_cursos.status_curso', '<>', 'CANCELADO')
                        ->orWhereNull('tbl_cursos.status_curso');
                })
                ->select('agenda.start', 'agenda.end')
                ->get()
                ->map(function ($item) {
                    $item->start = Carbon::parse($item->start);
                    $item->end   = Carbon::parse($item->end);
                    return $item;
                });

            // 2. Combinar actividades existentes + nuevas (simulando asignación)
            $todasActividades = $actividadesExistentes->concat($nuevasActividades);

            if ($todasActividades->isEmpty()) {
                $instructoresValidos[] = $instructor;
                continue;
            }

            // 3. Determinar rango global de fechas
            $fechaMin = $todasActividades->min('start')->copy()->startOfDay();
            $fechaMax = $todasActividades->max('end')->copy()->endOfDay();
            // return "Fecha max: " . $fechaMax . " Fecha min: " . $fechaMin;

            // 4. Recorrer día por día y acumular minutos por semana
            //    NOTA: start/end en agenda codifican el rango de FECHAS + el HORARIO diario.
            //    Ej: 2026-04-20 09:00 → 2026-04-24 13:00 significa "09:00–13:00 cada día del 20 al 24".
            //    Por eso usamos solo la parte de hora (H:i:s) para calcular los minutos diarios.
            $minutosPorSemana = [];
            $periodoDias = CarbonPeriod::create($fechaMin->copy()->startOfDay(), $fechaMax->copy()->startOfDay(), '1 day');

            foreach ($periodoDias as $dia) {
                $fechaDiaStr = $dia->format('Y-m-d');
                $minutosDia  = 0;

                foreach ($todasActividades as $act) {
                    // Verificar si la actividad cubre este día (comparación solo de fecha)
                    $fechaInicioAct = $act->start->format('Y-m-d');
                    $fechaFinAct    = $act->end->format('Y-m-d');

                    if ($fechaInicioAct <= $fechaDiaStr && $fechaFinAct >= $fechaDiaStr) {
                        // Aplicar solo la parte de hora del start/end al día actual
                        $horaInicio = Carbon::parse($fechaDiaStr . ' ' . $act->start->format('H:i:s'));
                        $horaFin    = Carbon::parse($fechaDiaStr . ' ' . $act->end->format('H:i:s'));

                        if ($horaFin > $horaInicio) {
                            $minutosDia += $horaInicio->diffInMinutes($horaFin);
                        }
                    }
                }

                if ($minutosDia > 0) {
                    // Clave de semana: lunes de esa semana (lunes a domingo)
                    $inicioSemana = $dia->copy()->startOfWeek()->format('Y-m-d');
                    $minutosPorSemana[$inicioSemana] = ($minutosPorSemana[$inicioSemana] ?? 0) + $minutosDia;
                }
            }

            // 5. Obtener las semanas que toca el nuevo curso
            $semanasCursoNuevo = [];
            foreach ($nuevasActividades as $act) {
                $periodo = CarbonPeriod::create($act->start->format('Y-m-d'), $act->end->format('Y-m-d'), '1 day');
                foreach ($periodo as $dia) {
                    $semana = $dia->copy()->startOfWeek()->format('Y-m-d');
                    $semanasCursoNuevo[$semana] = true;
                }
            }


            // 6. Validar SOLO las semanas del nuevo curso (no las históricas)
            $esValido = true;
            foreach ($semanasCursoNuevo as $semana => $_) {
                $minutosSemana = $minutosPorSemana[$semana] ?? 0;
                if ($minutosSemana > 2400) {
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


    ## Funcion para validar si el instructor rebasa los 150 dias impartiendo cursos
    public function InstValida150Dias($instructores, $folio_grupo)
    {
        $validados = [];

        foreach ($instructores as $instructor) {
            // Obtener cursos del instructor filtrados
            $cursos = DB::table('tbl_cursos as tc')
                ->where('tc.id_instructor', $instructor->id)
                ->where(function ($query) {
                    $query->where('tc.status_curso', '<>', 'CANCELADO')
                        ->orWhereNull('tc.status_curso');
                })
                // ->where(function ($query) use ($folio_grupo) {
                //     $query->where('tc.status_curso', '=', 'VALIDADO')
                //         ->orWhereNull('tc.status_curso')
                //         ->orWhere('tc.folio_grupo', $folio_grupo);
                // })
                ->orderBy('tc.inicio')
                ->get(['tc.inicio', 'tc.termino']);

            // if ($cursos->isEmpty()) continue;

            // Calcular diferencia de fechas y buscar recesos >= 30 días
            if (!empty($cursos)) {
                $max_inicio = null;
                for ($i = 0; $i < count($cursos) - 1; $i++) {
                    $diff = Carbon::parse($cursos[$i + 1]->inicio)->diffInDays($cursos[$i]->termino);
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
            } else {
                $dias = 0;
            }

            if ($dias <= 150) {
                $validados[] = $instructor;
            }
        }

        return $validados;
    }





    public function InstNoTraslapeFechaHoraConOtroCurso($instructores, $grupos)
    {
        $newArray = [];
        $idsInstructores = collect($instructores)->pluck('id')->all();

        // Actividades validadas
        $agendaExistente = DB::table('agenda as a')
            ->leftJoin('tbl_cursos as tc', 'a.id_curso', '=', 'tc.folio_grupo')
            ->select(
                'a.id_instructor',
                'a.start',
                'a.end',
                'a.id_curso'
            )
            ->whereIn('a.id_instructor', $idsInstructores)
            ->where(function ($query) {
                $query->where('tc.status_curso', '<>', 'CANCELADO')
                    ->orWhereNull('tc.status_curso');
            })
            ->get()
            ->groupBy('id_instructor');

        foreach ($instructores as $instructor) {
            $traslape = false;
            $actividades = $agendaExistente->get($instructor->id, collect());

            foreach ($grupos as $grupo) {
                $grupoInicio = Carbon::parse($grupo->start);
                $grupoFin = Carbon::parse($grupo->end);

                foreach ($actividades as $actividad) {
                    if ($grupo->id_curso == $actividad->id_curso) {
                        continue;
                    }

                    $actividadInicio = Carbon::parse($actividad->start);
                    $actividadFin = Carbon::parse($actividad->end);

                    // Rango de fechas que se superponen (para iterar día por día)
                    $fechaInicioMax = $grupoInicio->copy()->greaterThan($actividadInicio) ? $grupoInicio->copy() : $actividadInicio->copy();
                    $fechaFinMin = $grupoFin->copy()->lessThan($actividadFin) ? $grupoFin->copy() : $actividadFin->copy();

                    // Solo si hay días en común, se evalúa día por día
                    if ($fechaInicioMax->lte($fechaFinMin)) {
                        // Comparar día por día
                        $fechaActual = $fechaInicioMax->copy();

                        while ($fechaActual->lte($fechaFinMin)) {
                            // Hora del grupo
                            $horaGrupoInicio = $grupoInicio->format('H:i:s');
                            $horaGrupoFin = $grupoFin->format('H:i:s');

                            // Hora de la actividad
                            $horaActividadInicio = $actividadInicio->format('H:i:s');
                            $horaActividadFin = $actividadFin->format('H:i:s');

                            // Validar traslape en horas del mismo día
                            if ($horaGrupoInicio < $horaActividadFin && $horaActividadInicio < $horaGrupoFin) {
                                $traslape = true;
                                break 2;
                            }

                            $fechaActual->addDay();
                        }
                    }
                }
            }

            if (!$traslape) {
                $newArray[] = $instructor;
            }
        }

        return $newArray;
    }


    ##Funcion para validar si el instructor ALFA
    public function InstAlfaNoBecados($instructores)
    {
        $instructoresValidos = [];
        foreach ($instructores as $instructor) {
            # ciclo para recorrer los registros vamos a descartar a los que no son alfa
            $alfa = DB::table('instructores')
                ->where('id', $instructor->id)
                ->where('instructor_alfa', true)
                // ->whereRaw("datos_alfa->'subproyectos'->>'chiapas puede' = ?", ['voluntario']) // Condición de "voluntario"
                ->exists();

            if ($alfa) {
                $instructoresValidos[] = $instructor;
            }
        }
        return $instructoresValidos;
    }

    ##Funcion de consulta general de instructores validados
    public function consulta_general_instructores($curso, $ejercicio)
    {
        $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c', 'c.id_instructor', 'i.id')
            ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra', false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($curso->inicio)))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($curso->inicio)))
            ->havingRaw('count(*) >= 2')
            ->groupby('i.id');

        $query = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'instructores.id',
                'instructores.telefono',
                'tbl_unidades.unidad',
                'especialidad_instructores.fecha_validacion', // fecha de validacion ya no se ocupa ya que se obtiene de hvalidacion jsonb
                DB::raw("(SELECT COUNT(tc.id) FROM tbl_cursos AS tc WHERE tc.id_instructor = instructores.id and tc.status_curso = 'AUTORIZADO' AND EXTRACT(YEAR FROM tc.created_at) = {$ejercicio}) AS total_cursos")
            ) //DB::raw('count(id_curso) as total')
            ->rightJoin('instructores', 't.id_instructor', '=', 'instructores.id')
            ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')

            // ->JOIN('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
            // ->WHERE('especialidad_instructor_curso.curso_id',$data->id_curso)
            //Nueva linea para filtrar por cursos a impartir, no por especialidad
            ->whereJsonContains('especialidad_instructores.cursos_impartir', (string) $curso->id_curso)

            ->WHERE('estado', true)
            ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre', '!=', '')
            ->WHERE('especialidad_instructores.especialidad_id', $curso->id_especialidad)
            // ->WHERE('fecha_validacion','<',$curso->inicio)
            ->whereRaw("
                (
                    SELECT MAX((elem->>'fecha_val')::date)
                    FROM jsonb_array_elements(especialidad_instructores.hvalidacion) AS elem
                    WHERE (elem->>'fecha_val')::date < ?
                ) IS NOT NULL
            ", [$curso->inicio])
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"), '>=', $curso->termino)
            ->whereNotIn('instructores.id', $internos);

        if ($curso->curso_alfa == true) {
            $query->WHERE('instructores.instructor_alfa', '=', true);
        }

        $instructores = $query
            ->groupBy('t.id_instructor', 'instructores.id', 'instructores.telefono', 'tbl_unidades.unidad', 'especialidad_instructores.fecha_validacion')
            ->orderBy('instructor')
            ->get();

        return $instructores;
    }


    ## Validacion de instructores pasando por varios filtros
    public function data_validacion_instructores($data, $agenda, $ejercicio)
    {
        try {

            ## Consulta general de instructores
            $instructores = $this->consulta_general_instructores($data, $ejercicio);

            //Validar si el curso es ALFA
            // if ($data->curso_alfa == true) {
            //     $instructores = $this->InstAlfaNoBecados($instructores);
            //     if (count($instructores) == 0) {
            //         return [[], 'No se encontraron Instructores Alfa'];
            //     }
            // }

            if (count($instructores) == 0) {
                return [[], 'No se encontraron instructores disponibles para este curso'];
            }

            //Primer criterio
            $respuesta8Horas = $this->InstNoRebase8Horas($instructores, $agenda);
            if (count($respuesta8Horas) > 0) {

                //Segundo Criterio
                $respuesta40Horas = $this->InstNoRebase40HorasSem($respuesta8Horas, $data->folio_grupo);
                if (count($respuesta40Horas) > 0) {

                    //Tercer criterio
                    $respuestaTraslape = $this->InstNoTraslapeFechaHoraConOtroCurso($respuesta40Horas, $agenda);
                    if (count($respuestaTraslape) > 0) {

                        //Cuarto Criterio
                        $respuesta150dias = $this->InstValida150Dias($respuestaTraslape, $data->folio_grupo);
                        if (count($respuesta150dias) > 0) {

                            return [$respuesta150dias, '']; //Retornamos la respuesta

                        } else {
                            return [[], 'No se encontraron Instructores, Rebasan los 150 dias'];
                        }
                    } else {
                        return [[], 'No se encontraron Instructores, Traslapa con otros cursos'];
                    }
                } else {
                    return [[], 'No se encontraron Instructores, Rebasan las 40 Horas por Semana'];
                }
            } else {
                return [[], 'No se encontraron Instructores, Rebasan las 8 Horas Diarias'];
            }
        } catch (\Throwable $th) {
            return [[], 'Error: ' . $th->getMessage()];
        }
    }


    ## Validacion de instructor unico
    public function consulta_instructor_unico($curso, $ejercicio)
    {
        $query = DB::table('instructores')
            ->select(
                'instructores.id',
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
            )
            // ->rightJoin('instructores','t.id_instructor','=','instructores.id')
            ->join('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            // ->join('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->join('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')

            ->where('instructores.id', $curso->id_instructor)
            ->whereJsonContains('especialidad_instructores.cursos_impartir', (string) $curso->id_curso)
            ->where('estado', true)
            ->where('instructores.status', '=', 'VALIDADO')
            ->where('instructores.nombre', '!=', '')
            ->where('especialidad_instructores.especialidad_id', $curso->id_especialidad)
            // ->where('fecha_validacion', '<', $curso->inicio)
            ->whereRaw("
                (
                    SELECT MAX((elem->>'fecha_val')::date)
                    FROM jsonb_array_elements(especialidad_instructores.hvalidacion) AS elem
                    WHERE (elem->>'fecha_val')::date < ?
                ) IS NOT NULL
            ", [$curso->inicio])
            ->where(DB::raw("(fecha_validacion + INTERVAL '1 year')::timestamp::date"), '>=', $curso->termino);

        if ($curso->curso_alfa == true) {
            $query->where('instructores.instructor_alfa', '=', true);
        }
        return $query->get();
    }

    public function filtros_instructor($data, $agenda, $ejercicio)
    {
        try {

            ## Consulta general de instructores
            $instructor = $this->consulta_instructor_unico($data, $ejercicio);

            if (count($instructor) == 0) {
                return [[], 'EL INSTRUCTOR NO CUMPLE CON LOS REQUISITOS PARA IMPARTIR EL CURSO'];
            }

            //Primer criterio
            $respuesta8Horas = $this->InstNoRebase8Horas($instructor, $agenda);
            if (count($respuesta8Horas) > 0) {

                //Segundo Criterio
                $respuesta40Horas = $this->InstNoRebase40HorasSem($respuesta8Horas, $data->folio_grupo);
                if (count($respuesta40Horas) > 0) {

                    //Tercer criterio
                    $respuestaTraslape = $this->InstNoTraslapeFechaHoraConOtroCurso($respuesta40Horas, $agenda);
                    if (count($respuestaTraslape) > 0) {

                        //Cuarto Criterio
                        $respuesta150dias = $this->InstValida150Dias($respuestaTraslape, $data->folio_grupo);
                        if (count($respuesta150dias) > 0) {

                            return [$respuesta150dias, '']; //Retornamos la respuesta

                        } else {
                            return [[], 'INSTRUCTOR NO VALIDO, REBASA LOS 150 DIAS'];
                        }
                    } else {
                        return [[], 'INSTRUCTOR NO VALIDO, TRASLAPA CON OTROS CURSOS ASIGNADOS'];
                    }
                } else {
                    return [[], 'INSTRUCTOR NO VALIDO, REBASA LAS 40 HORAS POR SEMANA'];
                }
            } else {
                return [[], 'INSTRUCTOR NO VALIDO, REBASA LAS 8 HORAS DIARIAS'];
            }
        } catch (\Throwable $th) {
            return [[], 'Error: ' . $th->getMessage()];
        }
    }
}
