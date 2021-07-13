<?php

namespace App\Http\Controllers\Validacion;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\FormatoTReport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ReportesPlaneacionFormatoT extends Controller {

    public function index(Request $request) {
        $fechaInicio = $request->fecha_inicio;
        $fechaTermino = $request->fecha_termino;
        if ($fechaInicio != null) {
            session(['fechaInicio' => $fechaInicio]);
            session(['fechaTermino' => $fechaTermino]);
        }
        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia, alumnos_registro.cerrs FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia, alumnos_registro.cerrs) as ar");

        // madres solteras, centras
        $cursos = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ar.indigena = 'true' THEN 1 ELSE 0 END) as indigena"),
            DB::raw("SUM(CASE WHEN ap.discapacidad <> 'NINGUNA' THEN 1 ELSE 0 END) as discapacidad"),
            DB::raw("SUM(CASE WHEN ar.migrante = 'true' THEN 1 ELSE 0 END) as migrante"),
            DB::raw("SUM(CASE WHEN ar.cerrs = 'true' THEN 1 ELSE 0 END) as cerrs"),
            DB::raw("SUM(CASE WHEN EXTRACT(year from (age(c.inicio,ap.fecha_nacimiento))) >= 65 THEN 1 ELSE 0 END) as adultosMayores"),
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN($subConsulta, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('c.id', 'c.clave', 'c.status', 'c.fecha_turnado')
        ->get();

        $egresados = 0; $indigenas = 0; $discapacitados = 0; $migrantes = 0; $ceresos = 0; $adultosMayores = 0;
        foreach ($cursos as  $value) {
            $egresados += $value->egresado;
            $indigenas += $value->indigena;
            $discapacitados += $value->discapacidad;
            $migrantes += $value->migrante;
            $ceresos += $value->cerrs;
            $adultosMayores += $value->adultosmayores;
        }

        return view('reportes.vista_planeacion_reportes_formatoT', compact('fechaInicio', 'fechaTermino', 'egresados', 'indigenas', 'discapacitados', 'migrantes', 'ceresos', 'adultosMayores'));
    }

    public function createPdf() {
        $fechaInicio = session('fechaInicio');
        $fechaTermino = session('fechaTermino');

        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia, alumnos_registro.cerrs FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia, alumnos_registro.cerrs) as ar");

        $cursos = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ar.indigena = 'true' THEN 1 ELSE 0 END) as indigena"),
            DB::raw("SUM(CASE WHEN ap.discapacidad <> 'NINGUNA' THEN 1 ELSE 0 END) as discapacidad"),
            DB::raw("SUM(CASE WHEN ar.migrante = 'true' THEN 1 ELSE 0 END) as migrante"),
            DB::raw("SUM(CASE WHEN ar.cerrs = 'true' THEN 1 ELSE 0 END) as cerrs"),
            DB::raw("SUM(CASE WHEN EXTRACT(year from (age(c.inicio,ap.fecha_nacimiento))) >= 65 THEN 1 ELSE 0 END) as adultosMayores"),
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN($subConsulta, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('c.id', 'c.clave', 'c.status', 'c.fecha_turnado')
        ->get();

        $egresados = 0; $indigenas = 0; $discapacitados = 0; $migrantes = 0; $ceresos = 0; $adultosMayores = 0;
        foreach ($cursos as  $value) {
            $egresados += $value->egresado;
            $indigenas += $value->indigena;
            $discapacitados += $value->discapacidad;
            $migrantes += $value->migrante;
            $ceresos += $value->cerrs;
            $adultosMayores += $value->adultosmayores;
        }

        $pdf = PDF::loadView('reportes.reporteGruposVulnerables', compact('fechaInicio', 'fechaTermino', 'egresados', 'indigenas', 'discapacitados', 'migrantes', 'ceresos', 'adultosMayores'));
        // $pdf->setPaper('A4', 'Landscape');
        return $pdf->stream('download.pdf');
    }

    public function gruposCreateXls() {
        $fechaInicio = session('fechaInicio');
        $fechaTermino = session('fechaTermino');

        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia, alumnos_registro.cerrs FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia, alumnos_registro.cerrs) as ar");
        $cursos = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ar.indigena = 'true' THEN 1 ELSE 0 END) as indigena"),
            DB::raw("SUM(CASE WHEN ap.discapacidad <> 'NINGUNA' THEN 1 ELSE 0 END) as discapacidad"),
            DB::raw("SUM(CASE WHEN ar.migrante = 'true' THEN 1 ELSE 0 END) as migrante"),
            DB::raw("SUM(CASE WHEN ar.cerrs = 'true' THEN 1 ELSE 0 END) as cerrs"),
            DB::raw("SUM(CASE WHEN EXTRACT(year from (age(c.inicio,ap.fecha_nacimiento))) >= 65 THEN 1 ELSE 0 END) as adultosMayores"),
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN($subConsulta, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->where('ins.status', '=', 'INSCRITO')
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('c.id', 'c.clave', 'c.status', 'c.fecha_turnado')
        ->get();

        $egresados = 0; $indigenas = 0; $discapacitados = 0; $migrantes = 0; $ceresos = 0; $adultosMayores = 0;
        foreach ($cursos as  $value) {
            $egresados += $value->egresado;
            $indigenas += $value->indigena;
            $discapacitados += $value->discapacidad;
            $migrantes += $value->migrante;
            $ceresos += $value->cerrs;
            $adultosMayores += $value->adultosmayores;
        }

        $data = collect();
        for ($i=0; $i < 6; $i++) {
            switch ($i) {
                case 0:
                    $dataTemp = collect([
                        'grupo' => 'Indigenas',
                        'egresados' => $egresados,
                        'capacitados' => $indigenas,
                        'percent' => $egresados != 0 ? ($indigenas/$egresados)*100 : 0,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 1:
                    $dataTemp = collect([
                        'grupo' => 'Discapacitados',
                        'egresados' => $egresados,
                        'capacitados' => $discapacitados,
                        'percent' => $egresados != 0 ? ($discapacitados/$egresados)*100 : 0,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 2:
                    $dataTemp = collect([
                        'grupo' => 'Migrantes',
                        'egresados' => $egresados,
                        'capacitados' => $migrantes,
                        'percent' => $egresados != 0 ? ($migrantes/$egresados)*100 : 0,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 3:
                    $dataTemp = collect([
                        'grupo' => 'Ceresos',
                        'egresados' => $egresados,
                        'capacitados' => $ceresos,
                        'percent' => $egresados != 0 ? ($ceresos/$egresados)*100 : 0,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 4:
                    $dataTemp = collect([
                        'grupo' => 'Tercera Edad',
                        'egresados' => $egresados,
                        'capacitados' => $adultosMayores,
                        'percent' => $egresados != 0 ? ($adultosMayores/$egresados)*100 : 0,
                    ]);
                    $data->push($dataTemp);
                    break;
            }  
        }

        $head = ['Grupos', 'Egresados', 'Capacitados', '%'];
        $nombreLayout = "GRUPOS VULNERABLES".".xlsx";
        $titulo = "GRUPOS VULNERABLES";
        return Excel::download(new FormatoTReport($data,$head, $titulo), $nombreLayout);
    }

    public function indexIngresos(Request $request) {
        $fechaInicio = $request->fecha_inicio;
        $fechaTermino = $request->fecha_termino;
        $datePeriodoAntInicio = null;
        $datePeriodoAntTermino = null;

        if ($fechaInicio != null) {
            session(['fechaInicioIngresos' => $fechaInicio]);
            session(['fechaTerminoIngresos' => $fechaTermino]);

            $datePeriodoAntInicio = date("Y-m-d", strtotime($fechaInicio."- 1 year"));
            $datePeriodoAntTermino = date("Y-m-d", strtotime($fechaTermino."- 1 year"));
        }

        $unidades = ['COMITAN', 'TAPACHULA', 'TUXTLA', 'SAN CRISTOBAL', 'TONALA', 'JIQUIPILAS', 'VILLAFLORES', 'REFORMA', 'YAJALON', 'CATAZAJA', 'OCOSINGO'];
        // $unidad = 'TAPACHULA';
        $totalesPeriodoActual = DB::table('tbl_cursos as c')->select(
            // 'c.id',
            // 'c.fecha_turnado',
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        // ->WHERE('u.ubicacion', '=', $unidad)
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        // ->groupBy('c.id', 'c.fecha_turnado', 'c.unidad')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $totalesPeriodoAnterior = DB::table('tbl_cursos as c')->select(
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$datePeriodoAntInicio, $datePeriodoAntTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $year = ''; $yearAnt = '';
        if ($fechaInicio != null) {
            $fecha = Carbon::parse($fechaInicio);
            $year = $fecha->year;
            $fechaAnt = Carbon::parse($datePeriodoAntInicio);
            $yearAnt = $fechaAnt->year;
        }
        
        return view('reportes.vista_planeacion_reportes_ingresosPropios', compact('fechaInicio', 'fechaTermino', 'totalesPeriodoActual', 'totalesPeriodoAnterior', 'year', 'yearAnt'));
    }

    public function ingresosCreatePdf() {
        $fechaInicio = session('fechaInicioIngresos');
        $fechaTermino = session('fechaTerminoIngresos');
        $datePeriodoAntInicio = date("Y-m-d", strtotime($fechaInicio."- 1 year"));
        $datePeriodoAntTermino = date("Y-m-d", strtotime($fechaTermino."- 1 year"));

        $unidades = ['COMITAN', 'TAPACHULA', 'TUXTLA', 'SAN CRISTOBAL', 'TONALA', 'JIQUIPILAS', 'VILLAFLORES', 'REFORMA', 'YAJALON', 'CATAZAJA', 'OCOSINGO'];
        $totalesPeriodoActual = DB::table('tbl_cursos as c')->select(
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $totalesPeriodoAnterior = DB::table('tbl_cursos as c')->select(
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$datePeriodoAntInicio, $datePeriodoAntTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $fecha = Carbon::parse($fechaInicio);
        $year = $fecha->year;
        $fechaAnt = Carbon::parse($datePeriodoAntInicio);
        $yearAnt = $fechaAnt->year;

        $pdf = PDF::loadView('reportes.reporteIngresosPropios', compact('fechaInicio', 'fechaTermino', 'totalesPeriodoActual', 'totalesPeriodoAnterior', 'year', 'yearAnt'));
        // $pdf->setPaper('A4', 'Landscape');
        return $pdf->stream('download.pdf');
    }

    public function ingresosCreateXls() {
        $fechaInicio = session('fechaInicioIngresos');
        $fechaTermino = session('fechaTerminoIngresos');
        $datePeriodoAntInicio = date("Y-m-d", strtotime($fechaInicio."- 1 year"));
        $datePeriodoAntTermino = date("Y-m-d", strtotime($fechaTermino."- 1 year"));

        $unidades = ['COMITAN', 'TAPACHULA', 'TUXTLA', 'SAN CRISTOBAL', 'TONALA', 'JIQUIPILAS', 'VILLAFLORES', 'REFORMA', 'YAJALON', 'CATAZAJA', 'OCOSINGO'];
        $totalesPeriodoActual = DB::table('tbl_cursos as c')->select(
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $totalesPeriodoAnterior = DB::table('tbl_cursos as c')->select(
            'u.ubicacion',
            DB::raw("SUM(ins.costo) as total")
        )
        ->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN('tbl_unidades as u', 'u.unidad', '=', 'c.unidad')
        ->whereIn('u.ubicacion', $unidades)
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$datePeriodoAntInicio, $datePeriodoAntTermino])
        ->WHERE('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('ins.calificacion', '>', '0')
        ->groupBy('u.ubicacion')
        ->orderBy('u.ubicacion','DESC')
        ->get();

        $totalPeriodoAnt = 0; $totalPeriodoAct = 0; $diferencia = 0;
        foreach ($totalesPeriodoActual as $key => $value) {
            $totalPeriodoAct += $value->total;
            if (isset($totalesPeriodoAnterior[$key]->total)) {
                $totalPeriodoAnt += $totalesPeriodoAnterior[$key]->total;
                $value->tAnterior = $totalesPeriodoAnterior[$key]->total;
                $value->diferencia = $value->total - $totalesPeriodoAnterior[$key]->total;
                $diferencia += ($value->total - $totalesPeriodoAnterior[$key]->total);
            } else {
                $value->tAnterior = 0.0;
                $value->diferencia = $value->total;
                $diferencia += $value->total;
            }
            $temp = $value->total;
            $value->total = $value->tAnterior;
            $value->tAnterior = $temp;
        }
        $temp = [
            'ubicacion' => 'Total',
            'total' => $totalPeriodoAnt,
            'tAnterior' => $totalPeriodoAct,
            'diferencia' => $diferencia
        ];
        $totalesPeriodoActual->push($temp);

        $fecha = Carbon::parse($fechaInicio);
        $year = $fecha->year;
        $fechaAnt = Carbon::parse($datePeriodoAntInicio);
        $yearAnt = $fechaAnt->year;
        
        $head = ['Unidad', $yearAnt, $year, 'Diferencia vs '.$yearAnt];
        $nombreLayout = "INGRESOS PROPIOS".".xlsx";
        $titulo = "INGRESOS PROPIOS";
        return Excel::download(new FormatoTReport($totalesPeriodoActual,$head, $titulo), $nombreLayout);
    }

}
