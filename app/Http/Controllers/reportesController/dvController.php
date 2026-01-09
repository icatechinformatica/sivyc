<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use DateTime;
class dvController extends Controller
{
    function __construct() {

    }

    public function index(Request $request){
        try {
            $id_user = Auth::user()->id;
            $data = $message = $anios = $meses = $array_meses = [];
            $array_meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05'=> 'Mayo',
            '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
            if(session('message')) $message = session('message');
            if(session('data')) $data = session('data');
            if(session('anios')) $anios = session('anios');
            if(session('meses')) $meses = session('meses');
            if(session('array_meses')) $array_meses = session('array_meses');

            if(session('fecha1')) $fecha1 = session('fecha1');
            else $fecha1 = $request->fecha1;

            if(session('fecha2')) $fecha2 = session('fecha2');
            else $fecha2 = $request->fecha2;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }

        return view('reportes.dv.index', compact('data','message','fecha1', 'fecha2', 'anios', 'meses', 'array_meses'));
    }

    public function generar(Request $request){
        $data = $message = $anios = $meses = [];
        $array_meses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05'=> 'Mayo',
        '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];
        $consulta = $this->data($request);
        list($data, $anios, $meses) = $consulta;

        if (is_array($data) && array_key_exists("ERROR", $data)){
            $message = $data;
            $data = [];
        }
        if($data){
            switch($request->opcion){
                case "FILTRAR":
                    return redirect('reportes/dv')->with([
                        'data'=>$data,
                        'message'=>$message,
                        'fecha1'=>$request->fecha1,
                        'fecha2' => $request->fecha2,
                        'anios'=>$anios,
                        'meses'=>$meses
                    ]);
                break;
                case "XLS":
                    $title = "DPA-Reporte de Operación con Convenios Generales Vigentes";
                    $name = $title."_".date('Ymd').".xlsx";
                    $view = 'reportes.dv.excel_convenios';
                    $data = ['data'=> $data, 'anios' => $anios, 'meses' => $meses, 'array_meses' => $array_meses];
                    return Excel::download(new ExportExcel($data,null, $title,$view), $name);
                break;
            }

        }else return redirect('reportes/dv')->with(['message'=>$message]);
    }

    private function data(Request $request){
        if($request->fecha1 and $request->fecha2){
            // $inicio_sexenio = new DateTime('2022-01-01');
            $fecha_ini_sexenio = DB::table('tbl_instituto')->value('fecha_ini_sexenio');
            $inicio_sexenio = new DateTime($fecha_ini_sexenio);
            $start_date = new DateTime($request->fecha1);
            $end_date = new DateTime($request->fecha2);

            ##Rango de años comenzando con el inicio de sexenio
            $anios = [];
            $current_year = $inicio_sexenio->format('Y');
            while ($current_year <= $end_date->format('Y')) {
                if (!in_array($current_year, $anios)) {
                    $anios[] = intval($current_year);
                }
                $current_year++;
            }

            ##Rango de meses de acuerdo a las fechas
            $meses = [];
            while ($start_date <= $end_date) {
                $mes = $start_date->format('Y-m');
                if (!in_array($mes, $meses)) {
                    $meses[] = $mes;
                }
                $start_date->modify('first day of next month');
            }

            ## Construir la consulta SQL dinámicamente
            $sql = 'WITH ';

            // Construir los CTEs (Common Table Expressions) para cada año
            foreach ($anios as $anio) {
                $sql .= "datos_$anio AS (
                    SELECT
                        c2.no_convenio,
                        tc2.unidad AS unidad,
                        COUNT(DISTINCT tc2.id) AS total_cursos_$anio,
                        SUM(CASE WHEN ti2.calificacion != 'NP' AND ti2.status = 'INSCRITO' AND ti2.sexo = 'M' THEN 1 ELSE 0 END) AS total_mujeres_$anio,
                        SUM(CASE WHEN ti2.calificacion != 'NP' AND ti2.status = 'INSCRITO' AND ti2.sexo = 'H' THEN 1 ELSE 0 END) AS total_hombres_$anio,
                        SUM(CASE WHEN ti2.calificacion != 'NP' AND ti2.status = 'INSCRITO' AND ti2.sexo IN ('M', 'H') THEN 1 ELSE 0 END) AS total_alumnos_$anio
                    FROM convenios AS c2
                    LEFT JOIN tbl_cursos AS tc2 ON c2.no_convenio = tc2.cgeneral
                    LEFT JOIN tbl_inscripcion AS ti2 ON ti2.id_curso = tc2.id
                    WHERE EXTRACT(YEAR FROM ((tc2.memos->'CERRADO_PLANEACION'->>'FECHA')::DATE)) = $anio
                    AND tc2.status_curso = 'AUTORIZADO'
                    AND tc2.proceso_terminado = true
                    GROUP BY c2.no_convenio, tc2.unidad
                ), ";
            }
            // WHERE EXTRACT(YEAR FROM tc2.inicio::DATE) = $anio

            // Construir los CTEs para cada mes
            foreach ($meses as $mes) {
                $mes_alias = str_replace('-', '_', $mes);
                $sql .= "datos_$mes_alias AS (
                    SELECT
                        c3.no_convenio,
                        tc3.unidad AS unidad,
                        COUNT(DISTINCT tc3.id) AS total_cursos_$mes_alias,
                        STRING_AGG(DISTINCT tc3.curso, '/ ') AS cursos_$mes_alias,
                        SUM(CASE WHEN ti3.calificacion != 'NP' AND ti3.status = 'INSCRITO' AND ti3.sexo = 'M' THEN 1 ELSE 0 END) AS total_mujeres_$mes_alias,
                        SUM(CASE WHEN ti3.calificacion != 'NP' AND ti3.status = 'INSCRITO' AND ti3.sexo = 'H' THEN 1 ELSE 0 END) AS total_hombres_$mes_alias,
                        SUM(CASE WHEN ti3.calificacion != 'NP' AND ti3.status = 'INSCRITO' AND ti3.sexo IN ('M', 'H') THEN 1 ELSE 0 END) AS total_alumnos_$mes_alias
                    FROM convenios AS c3
                    LEFT JOIN tbl_cursos AS tc3 ON c3.no_convenio = tc3.cgeneral
                    LEFT JOIN tbl_inscripcion AS ti3 ON ti3.id_curso = tc3.id
                    WHERE TO_CHAR((tc3.memos->'CERRADO_PLANEACION'->>'FECHA')::DATE, 'YYYY-MM') = '$mes'
                    AND tc3.status_curso = 'AUTORIZADO'
                    AND tc3.proceso_terminado = true
                    GROUP BY c3.no_convenio, tc3.unidad
                ), ";
            }

            // WHERE TO_CHAR(tc3.inicio::DATE, 'YYYY-MM') = '$mes'

            // Eliminar la última coma y espacio
            $sql = rtrim($sql, ', ');

            // Construir la consulta final
            $sql .= "
            SELECT
                c.no_convenio,
                c.institucion,
                c.tipo_sector,
                c.fecha_firma,
                c.fecha_vigencia,
                c.poblacion,
                c.municipio,
                tc.unidad, ";

            // Agregar las columnas dinámicas para cada año
            foreach ($anios as $anio) {
                $sql .= "
                COALESCE(d$anio.total_cursos_$anio, 0) AS total_cursos_$anio,
                COALESCE(d$anio.total_mujeres_$anio, 0) AS total_mujeres_$anio,
                COALESCE(d$anio.total_hombres_$anio, 0) AS total_hombres_$anio,
                COALESCE(d$anio.total_alumnos_$anio, 0) AS total_alumnos_$anio, ";
            }

            // Agregar las columnas dinámicas para cada mes
            foreach ($meses as $mes) {
                $mes_alias = str_replace('-', '_', $mes);
                $sql .= "
                COALESCE(d$mes_alias.cursos_$mes_alias, '') AS cursos_$mes_alias,
                COALESCE(d$mes_alias.total_cursos_$mes_alias, 0) AS total_cursos_$mes_alias,
                COALESCE(d$mes_alias.total_mujeres_$mes_alias, 0) AS total_mujeres_$mes_alias,
                COALESCE(d$mes_alias.total_hombres_$mes_alias, 0) AS total_hombres_$mes_alias,
                COALESCE(d$mes_alias.total_alumnos_$mes_alias, 0) AS total_alumnos_$mes_alias, ";
            }

            // Eliminar la última coma y espacio
            $sql = rtrim($sql, ', ');

            $sql .= "
            FROM convenios AS c
            LEFT JOIN tbl_cursos AS tc ON c.no_convenio = tc.cgeneral
            LEFT JOIN tbl_inscripcion AS ti ON ti.id_curso = tc.id ";

            // Agregar los JOINs dinámicos para cada año
            foreach ($anios as $anio) {
                $sql .= "
            LEFT JOIN datos_$anio AS d$anio
                ON c.no_convenio = d$anio.no_convenio
            AND tc.unidad = d$anio.unidad ";
            }

            // Agregar los JOINs dinámicos para cada mes
            foreach ($meses as $mes) {
                $mes_alias = str_replace('-', '_', $mes);
                $sql .= "
            LEFT JOIN datos_$mes_alias AS d$mes_alias
                ON c.no_convenio = d$mes_alias.no_convenio
            AND tc.unidad = d$mes_alias.unidad ";
            }

            $sql .= "
            WHERE (tc.memos->'CERRADO_PLANEACION'->>'FECHA')::DATE BETWEEN '$request->fecha1' AND '$request->fecha2'
            AND tc.status_curso = 'AUTORIZADO'
            AND tc.proceso_terminado = true

            GROUP BY c.no_convenio, c.institucion, c.tipo_sector, c.fecha_firma, c.fecha_vigencia, c.poblacion, c.municipio, tc.unidad, ";

            // Agregar las columnas dinámicas al GROUP BY
            foreach ($anios as $anio) {
                $sql .= "
            d$anio.total_cursos_$anio, d$anio.total_mujeres_$anio, d$anio.total_hombres_$anio, d$anio.total_alumnos_$anio, ";
            }

            foreach ($meses as $mes) {
                $mes_alias = str_replace('-', '_', $mes);
                $sql .= "
            d$mes_alias.cursos_$mes_alias, d$mes_alias.total_cursos_$mes_alias, d$mes_alias.total_mujeres_$mes_alias, d$mes_alias.total_hombres_$mes_alias, d$mes_alias.total_alumnos_$mes_alias, ";
            }

            // Eliminar la última coma y espacio
            $sql = rtrim($sql, ', ');

            // Ejecutar la consulta
            $resultados = DB::select($sql);

            return [$resultados, $anios, $meses];
        }else $message["ERROR"] = "SE REQUIERE QUE SELECCIONE LA FECHA INICIAL Y FECHA FINAL PARA GENERAR EL REPORTE.";
        //dd($message);
        if($message) return $message;


    }

    // function obtenerNumeroQuincena($fecha) {
    //     $date = new DateTime($fecha);
    //     $inicioAnio = new DateTime($date->format('Y') . '-01-01');
    //     $diasTranscurridos = $inicioAnio->diff($date)->days;
    //     $numeroQuincena = intdiv($diasTranscurridos, 15) + 1;
    //     return min($numeroQuincena, 24);
    // }



}
