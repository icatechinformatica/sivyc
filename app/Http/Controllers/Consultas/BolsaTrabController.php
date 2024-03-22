<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\xlsConvenios;

class BolsaTrabController extends Controller
{
    public function index(Request $request) {

        $textcurso = $request->text_buscar_curso;
        $nacionalidad =  $request->sel_nacionalidad;
        $fecha_inicio = $request->fechaIniV;
        $fecha_fin = $request->fechaFinV;

        ##Consulta general sin filtrado
        $query = DB::table('tbl_inscripcion as ti')
        ->join('alumnos_pre as ap', 'ap.curp', '=', 'ti.curp')
        ->join('tbl_cursos as tc', 'tc.id', '=', 'ti.id_curso')
        ->whereIn('ti.calificacion', ['6', '7', '8', '9', '10'])
        ->where('ap.check_bolsa', true)
        ->where('tc.status_curso', 'AUTORIZADO')
        ->groupBy('ap.curp')
        ->select(
            'ap.curp',
            DB::raw('MAX(ti.id) as id'),
            DB::raw('MAX(ti.alumno) as alumno'),
            DB::raw('MAX(ti.fecha_nacimiento) as fecha_nacimiento'),
            DB::raw('EXTRACT(YEAR FROM AGE(MAX(ti.fecha_nacimiento))) as edad'), // Calcular edad
            DB::raw('MAX(ap.telefono_personal) as telefono'),
            DB::raw('MAX(ap.correo) as correo'),
            DB::raw('MAX(ap.nacionalidad) as nacionalidad'),
            DB::raw('MAX(ap.sexo) as sexo'),
            DB::raw('MAX(ap.colonia) as colonia'),
            DB::raw('MAX(ap.municipio) as municipio'),
            DB::raw('MAX(ap.estado) as estado'),
            DB::raw('MAX(ap.domicilio) as domicilio'),
            DB::raw('MAX(ap.estado_civil) as estado_civil'),
            DB::raw('MAX(ap.ultimo_grado_estudios) as ultimo_grado_est')
        );

        ##CURSO
        if ($textcurso != null) {
            $query->where('ti.curso', $textcurso);
        }
        ##NACIONALIDAD
        if ($nacionalidad != null) {
            if($nacionalidad == 'MEXICANA'){
                $query->whereIn('ap.nacionalidad', ['MEXICANA', 'MEXICANO']);
            }else{
                $query->whereNotIn('ap.nacionalidad', ['MEXICANA', 'MEXICANO'])->whereNotNull('ap.nacionalidad');
            }
        }
        ##FECHA DE INICIO Y FIN
        if($fecha_inicio != null && $fecha_fin != null){
            $query->where('ti.termino', '>=', $fecha_inicio)
                    ->where('ti.termino', '<=', $fecha_fin);
        }else{
            $fechaActual = Date::now()->format('Y-m-d');
            $query->where('ti.termino', '<=', $fechaActual);
        }

        $total_reg = $query->get()->count();
        $results = $query->paginate(15);

        return view('consultas.bolsatrabajo', compact('textcurso', 'nacionalidad', 'fecha_inicio', 'fecha_fin', 'results', 'total_reg'));
    }

    ## AUTOCOMPLETADO DE LISTA DE CURSOS
    public function autocomplete_cursos (Request $request) {
        $search = $request->search;

        if (isset($search) && $search != '') {
            $data = tbl_curso::select('curso')
                ->where('curso', 'ilike', '%'.$search.'%')
                ->limit(7)
                ->distinct()
                ->get();
        }
        $response = array();
        foreach ($data as $value) {
            $response[] = array('label' => $value->curso);
        }
        return json_encode($response);
    }

    ## CREAR REPORTE DE EXCEL
    public function crear_reporte_excel(Request $request){
        $textcurso = $request->text_buscar_curso;
        $nacionalidad =  $request->sel_nacionalidad;
        $fecha_inicio = $request->fechaIniV;
        $fecha_fin = $request->fechaFinV;

        ##Consulta general sin filtrado
        $query = DB::table('tbl_inscripcion as ti')
        ->join('alumnos_pre as ap', 'ap.curp', '=', 'ti.curp')
        ->join('tbl_cursos as tc', 'tc.id', '=', 'ti.id_curso')
        ->whereIn('ti.calificacion', ['6', '7', '8', '9', '10'])
        ->where('ap.check_bolsa', true)
        ->where('tc.status_curso', 'AUTORIZADO')
        ->groupBy('ap.curp')
        ->select(
            'ap.curp',
            // DB::raw('MAX(ti.id) as id'),
            DB::raw('MAX(ti.alumno) as alumno'),
            // DB::raw('MAX(ti.fecha_nacimiento) as fecha_nacimiento'),
            DB::raw('EXTRACT(YEAR FROM AGE(MAX(ti.fecha_nacimiento))) as edad'), // Calcular edad
            DB::raw('MAX(ap.nacionalidad) as nacionalidad'),
            DB::raw('MAX(ap.sexo) as sexo'),
            DB::raw('MAX(ap.colonia) as colonia'),
            DB::raw('MAX(ap.municipio) as municipio'),
            DB::raw('MAX(ap.estado) as estado'),
            DB::raw('MAX(ap.domicilio) as domicilio'),
            DB::raw('MAX(ap.estado_civil) as estado_civil'),
            DB::raw('MAX(ap.ultimo_grado_estudios) as ultimo_grado_est'),
            DB::raw('MAX(ap.telefono_personal) as telefono'),
            DB::raw('MAX(ap.correo) as correo')
        );

        ##CURSO
        if ($textcurso != null) {
            $query->where('ti.curso', $textcurso);
        }
        ##NACIONALIDAD
        if ($nacionalidad != null) {
            if($nacionalidad == 'MEXICANA'){
                $query->whereIn('ap.nacionalidad', ['MEXICANA', 'MEXICANO']);
            }else{
                $query->whereNotIn('ap.nacionalidad', ['MEXICANA', 'MEXICANO'])->whereNotNull('ap.nacionalidad');
            }
        }
        ##FECHA DE INICIO Y FIN
        if($fecha_inicio != null && $fecha_fin != null){
            $query->where('ti.termino', '>=', $fecha_inicio)
                    ->where('ti.termino', '<=', $fecha_fin);
        }else{
            $fechaActual = Date::now()->format('Y-m-d');
            $query->where('ti.termino', '<=', $fechaActual);
        }

        // $total_reg = $query->get()->count();
        $results = $query->get();

        ##Excel
        $head = ['CURP', 'ALUMNO', 'EDAD', 'NACIONALIDAD', 'SEXO',
                'COLONIA', 'MUNICIPIO', 'ESTADO', 'DOMICILIO', 'ESTADO CIVIL', 'GRADO DE ESTUDIOS', 'TELEFONO', 'CORREO'];

        $title = "BOLSA DE TRABAJO";
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.reportes.excel_bolsa_trabajo';
        $datos_vista = [
            'data' => $results,
            'curso' => $request->text_buscar_curso,
            'sel_nacionalidad' => $request->sel_nacionalidad,
            'fecha1' => $request->fechaIniV,
            'fecha2' => $request->fechaFinV
        ];


        if(count($results)>0)return Excel::download(new xlsConvenios($datos_vista,$head, $title,$view), $name);
        // dd($results);
    }
}
