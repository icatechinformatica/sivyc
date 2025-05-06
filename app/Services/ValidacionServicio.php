<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class ValidacionServicio
{
    protected $instructores;
    public function __construct($instructores) {
        $this->instructores = collect($instructores);
    }

    public function InstDisponibleFechaHora() {
        $disponibles = [];
        foreach ($this->instructores as $ins) {

            $id_curso     = $ins['id_curso'];
            $fechaInicio  = $ins['fechaInicio'];
            $fechaTermino = $ins['fechaTermino'];
            $horaInicio   = $ins['horaInicio'];
            $horaTermino  = $ins['horaTermino'];

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
                    $disponibles[] = $ins['id_instructor'];
                }
        }
        return $disponibles;
    }

    public function InstNoRebase8Horas() {
        foreach ($period as $fecha) {
            $f = date($fecha->format('Y-m-d'));
            $suma = 0;
            $horas_dia = DB::table('agenda')->select(DB::raw('cast(agenda.start as time) as hini'),DB::raw('cast(agenda.end as time) as hfin'))
                ->join('tbl_cursos','agenda.id_curso','=','tbl_cursos.folio_grupo')
                ->where('tbl_cursos.status','<>','CANCELADO')
                ->where('agenda.id_instructor','=',$id_instructor)
                ->whereRaw("(date(agenda.start)<='$f' AND date(agenda.end)>='$f')")
                ->get();
            foreach ($horas_dia as $value) {
                $minutos = Carbon::parse($value->hfin)->diffInMinutes($value->hini);
                $suma += $minutos;
                if (($suma + $minutos_curso) > 480) {
                    return "El instructor no debe de exceder las 8hrs impartidas.";
                }
            }
        }
    }
}
