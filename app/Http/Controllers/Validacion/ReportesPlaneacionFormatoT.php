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
        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");

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
            DB::raw("SUM(CASE WHEN ins.id_cerss IS NOT NULL THEN 1 ELSE 0 END) as cerrs"),
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

        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");

        $cursos = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ar.indigena = 'true' THEN 1 ELSE 0 END) as indigena"),
            DB::raw("SUM(CASE WHEN ap.discapacidad <> 'NINGUNA' THEN 1 ELSE 0 END) as discapacidad"),
            DB::raw("SUM(CASE WHEN ar.migrante = 'true' THEN 1 ELSE 0 END) as migrante"),
            DB::raw("SUM(CASE WHEN ins.id_cerss IS NOT NULL THEN 1 ELSE 0 END) as cerrs"),
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

        $subConsulta = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");
        $cursos = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresado"),
            DB::raw("SUM(CASE WHEN ar.indigena = 'true' THEN 1 ELSE 0 END) as indigena"),
            DB::raw("SUM(CASE WHEN ap.discapacidad <> 'NINGUNA' THEN 1 ELSE 0 END) as discapacidad"),
            DB::raw("SUM(CASE WHEN ar.migrante = 'true' THEN 1 ELSE 0 END) as migrante"),
            DB::raw("SUM(CASE WHEN ins.id_cerss IS NOT NULL THEN 1 ELSE 0 END) as cerrs"),
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

    // estadisticas
    public function indexEstadisticas(Request $request) {
        $fechaInicio = $request->fecha_inicio;
        $fechaTermino = $request->fecha_termino;
        if ($fechaInicio != null) {
            session(['fechaInicioEsta' => $fechaInicio]);
            session(['fechaTerminoEsta' => $fechaTermino]);
        }

        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");
        $cursosRealizados = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw('count(distinct(ins.id)) as inscritos'),
            'c.dura as horas',
            DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujeres"),
            DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombres"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresados"),
            'c.mod as modalidad',
            'c.muni as municipio'
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->LEFTJOIN($temptblinner, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->LEFTJOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        // ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_apertura', [$fechaInicio, $fechaTermino])
        ->where('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->where('c.proceso_terminado',true)
        ->groupBy('c.id')
        ->get();

        $totalCursos = count($cursosRealizados);
        $beneficiarios=0; $horas=0; $mujeres=0; $hombres=0; $egresados=0; $desercion=0; $ext=0; $cae=0; $emp=0; $municipios=[];
        foreach ($cursosRealizados as $value) {
            $beneficiarios += $value->inscritos;
            $horas += $value->horas;
            $mujeres += $value->imujeres;
            $hombres += $value->ihombres;
            $egresados += $value->egresados;
            switch ($value->modalidad) {
                case 'EXT': $ext += 1; break;
                case 'CAE': $cae += 1; break;
                case 'EMP': $emp += 1; break;
            }
            array_push($municipios, $value->municipio);
        }
        $desercion = $beneficiarios - $egresados;
        $totalMunicipios = count(array_unique($municipios));

        return view('reportes.vista_planeacion_reportes_estadisticas', compact('fechaInicio','fechaTermino', 'totalCursos',
            'beneficiarios', 'horas', 'mujeres', 'hombres', 'egresados', 'desercion', 'ext', 'cae', 'emp', 'totalMunicipios'));
    }

    public function estadisticasCreatePdf() {
        $fechaInicio = session('fechaInicioEsta');
        $fechaTermino = session('fechaTerminoEsta');

        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");
        $cursosRealizados = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw('count(distinct(ins.id)) as inscritos'),
            'c.dura as horas',
            DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujeres"),
            DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombres"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresados"),
            'c.mod as modalidad',
            'c.muni as municipio'
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN($temptblinner, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->where('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->groupBy('c.id')
        ->get();

        $totalCursos = count($cursosRealizados);
        $beneficiarios=0; $horas=0; $mujeres=0; $hombres=0; $egresados=0; $desercion=0; $ext=0; $cae=0; $emp=0; $municipios=[];
        foreach ($cursosRealizados as $value) {
            $beneficiarios += $value->inscritos;
            $horas += $value->horas;
            $mujeres += $value->imujeres;
            $hombres += $value->ihombres;
            $egresados += $value->egresados;
            switch ($value->modalidad) {
                case 'EXT': $ext += 1; break;
                case 'CAE': $cae += 1; break;
                case 'EMP': $emp += 1; break;
            }
            array_push($municipios, $value->municipio);
        }
        $desercion = $beneficiarios - $egresados;
        $totalMunicipios = count(array_unique($municipios));

        $pdf = PDF::loadView('reportes.reporteEstadisticas', compact('fechaInicio','fechaTermino', 'totalCursos',
        'beneficiarios', 'horas', 'mujeres', 'hombres', 'egresados', 'desercion', 'ext', 'cae', 'emp', 'totalMunicipios'));
        // $pdf->setPaper('A4', 'Landscape');
        return $pdf->stream('download.pdf');
    }

    public function estadisticasCreateXls() {
        $fechaInicio = session('fechaInicioEsta');
        $fechaTermino = session('fechaTerminoEsta');

        $temptblinner = DB::raw("(SELECT id_pre, no_control, id_curso, alumnos_registro.migrante, alumnos_registro.indigena, alumnos_registro.etnia FROM alumnos_registro GROUP BY id_pre, no_control, id_curso, alumnos_registro.migrante,alumnos_registro.indigena,alumnos_registro.etnia) as ar");
        $cursosRealizados = DB::table('tbl_cursos as c')->select(
            'c.id',
            'c.clave',
            'c.status',
            'c.fecha_turnado',
            DB::raw('count(distinct(ins.id)) as inscritos'),
            'c.dura as horas',
            DB::raw("SUM(CASE WHEN ap.sexo='FEMENINO' THEN 1 ELSE 0 END) as imujeres"),
            DB::raw("SUM(CASE WHEN ap.sexo='MASCULINO' THEN 1 ELSE 0 END) as ihombres"),
            DB::raw("SUM(CASE WHEN ins.calificacion <> 'NP' THEN 1 ELSE 0 END) as egresados"),
            'c.mod as modalidad',
            'c.muni as municipio'
        )->JOIN('tbl_inscripcion as ins', 'c.id', '=', 'ins.id_curso')
        ->JOIN($temptblinner, function ($join) {
            $join->on('ins.matricula', '=', 'ar.no_control');
            $join->on('c.id_curso', '=', 'ar.id_curso');
        })
        ->JOIN('alumnos_pre as ap', 'ar.id_pre', '=', 'ap.id')
        ->where('c.status', '=', 'REPORTADO')
        ->whereBetween('c.fecha_turnado', [$fechaInicio, $fechaTermino])
        ->where('c.clave', '!=', 'null')
        ->where('ins.status', '=', 'INSCRITO')
        ->groupBy('c.id')
        ->get();

        $totalCursos = count($cursosRealizados);
        $beneficiarios=0; $horas=0; $mujeres=0; $hombres=0; $egresados=0; $desercion=0; $ext=0; $cae=0; $emp=0; $municipios=[];
        foreach ($cursosRealizados as $value) {
            $beneficiarios += $value->inscritos;
            $horas += $value->horas;
            $mujeres += $value->imujeres;
            $hombres += $value->ihombres;
            $egresados += $value->egresados;
            switch ($value->modalidad) {
                case 'EXT': $ext += 1; break;
                case 'CAE': $cae += 1; break;
                case 'EMP': $emp += 1; break;
            }
            array_push($municipios, $value->municipio);
        }
        $desercion = $beneficiarios - $egresados;
        $totalMunicipios = count(array_unique($municipios));

        $data = collect();
        for ($i=0; $i < 11; $i++) {
            switch ($i) {
                case 0:
                    $dataTemp = collect([
                        'categoria' => 'Cursos Realizados',
                        'resultado' => $totalCursos,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 1:
                    $dataTemp = collect([
                        'categoria' => 'Total de Beneficiarios',
                        'resultado' => $beneficiarios,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 2:
                    $dataTemp = collect([
                        'categoria' => 'Total de Horas',
                        'resultado' => $horas,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 3:
                    $dataTemp = collect([
                        'categoria' => 'Total de Mujeres',
                        'resultado' => $mujeres,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 4:
                    $dataTemp = collect([
                        'categoria' => 'Total de Hombres',
                        'resultado' => $hombres,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 5:
                    $dataTemp = collect([
                        'categoria' => 'Total de Egresados',
                        'resultado' => $egresados,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 6:
                    $dataTemp = collect([
                        'categoria' => 'Total de Deserción',
                        'resultado' => $desercion,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 7:
                    $dataTemp = collect([
                        'categoria' => 'Cursos EXT',
                        'resultado' => $ext,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 8:
                    $dataTemp = collect([
                        'categoria' => 'Cursos CAE',
                        'resultado' => $cae,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 9:
                    $dataTemp = collect([
                        'categoria' => 'Cursos EMP',
                        'resultado' => $emp,
                    ]);
                    $data->push($dataTemp);
                    break;
                case 10:
                    $dataTemp = collect([
                        'categoria' => 'Municipios Atendidos',
                        'resultado' => $totalMunicipios,
                    ]);
                    $data->push($dataTemp);
                    break;
            }
        }

        $head = ['Categoria', 'Resultado'];
        $nombreLayout = "ESTADISTICAS DEL FORMATO T".".xlsx";
        $titulo = "REPORTE ESTADISTICO DEL FORMATO T";
        return Excel::download(new FormatoTReport($data, $head, $titulo), $nombreLayout);
    }

}
