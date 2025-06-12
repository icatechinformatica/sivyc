<?php

namespace App\Http\Controllers\Consultas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\xls;

//use Maatwebsite\Excel\Facades\Excel;
//use App\Excel\xlsConvenios;

class BolsaTrabController extends Controller
{
     function __construct() {        
        $this->AnioActual = date('Y');
        $this->AnioAnterior = $this->AnioActual - 1;
    }

    public function index(Request $request) {
        $textcurso = $request->text_buscar_curso;
        $nacionalidad =  $request->sel_nacionalidad;
        $fecha_inicio = $request->fechaIniV;
        $fecha_fin = $request->fechaFinV;
        list($results, $total_reg) = $this->data($request);        
        return view('consultas.bolsatrabajo', compact('textcurso', 'nacionalidad', 'fecha_inicio', 'fecha_fin', 'results', 'total_reg'));
    }

    public function ver(Request $request){ 
        $curp = $request->curp;
        $data = null;
        if($curp)
            $data = DB::table('alumnos_pre')->where('curp',$curp)->value('datos_incorporacion');
        
        return $data;
    }

    public function guardar(Request $request){ 
        $msg = "LA OPERACIÓN HA FALADO, FAVOR DE INTENTAR DE NUEVO.";
        $empresa =  $datos = null;
        if($request->curp){
            $fecha = $request->fecha;
            $empresa = $request->empresa;
            $direccion = $request->direccion;
            $puesto = $request->puesto;
            $realizo = Auth::user()->name;
            $created = date('Y-m-d H:i:s');
            $datos = [
                    'fecha' => $fecha,
                    'empresa' => $empresa,
                    'direccion' => $direccion,
                    'puesto' => $puesto,
                    'iduser_created'=> $realizo,
                    'created_at' => $created
            ];

            $result = DB::table('alumnos_pre')->where('curp',$request->curp)
            ->update([
                'datos_incorporacion' => $datos
            ]);            
            if($result) $msg = "OPERACIÓN EXITOSA.";            
        }
        
        return response()->json([
            'mensaje' => $msg,
            'empresa' => $empresa,
            'datos' =>  json_encode($datos)
        ]);
    }

    private function data(Request $request){       
        $textcurso = $request->text_buscar_curso;
        $nacionalidad =  $request->sel_nacionalidad;
        $fecha_inicio = $request->fechaIniV;
        $fecha_fin = $request->fechaFinV;

        $query = DB::table('tbl_inscripcion as ti')
        ->join('alumnos_pre as ap', 'ap.curp', '=', 'ti.curp')
        ->join('tbl_cursos as tc', 'tc.id', '=', 'ti.id_curso')        
        ->where('ap.check_bolsa', true)
        ->where('ti.status', 'INSCRITO')->where('ti.calificacion', '<>', 'NP')->whereNotNull('ti.calificacion')
        ->where('tc.status_curso', 'AUTORIZADO')
        ->orderby(DB::raw('MAX(tc.inicio)'), 'DESC') 
        ->groupBy('ap.curp','ap.datos_incorporacion')
        ->select(
            DB::raw('EXTRACT(YEAR FROM max(ti.inicio)) as ejercicio'),
            'ap.curp',
            DB::raw('MAX(ti.id) as id'),
            DB::raw('MAX(ti.alumno) as alumno'),
            DB::raw('MAX(ti.fecha_nacimiento) as fecha_nacimiento'),
            DB::raw('EXTRACT(YEAR FROM AGE(MAX(ti.fecha_nacimiento))) as edad'),
            DB::raw('MAX(ap.telefono_personal) as telefono'),
            DB::raw('MAX(ap.correo) as correo'),
            DB::raw('MAX(ti.nacionalidad) as nacionalidad'),
            DB::raw('MAX(ti.sexo) as sexo'),
            DB::raw('MAX(ap.colonia) as colonia'),
            DB::raw('MAX(ap.municipio) as municipio'),
            DB::raw('MAX(ap.estado) as estado'),
            DB::raw('MAX(ap.domicilio) as domicilio'),
            DB::raw('MAX(ap.estado_civil) as estado_civil'),
            DB::raw('MAX(ap.ultimo_grado_estudios) as ultimo_grado_est'),
            DB::raw('ap.datos_incorporacion as datos')
        )
        ->selectRaw("STRING_AGG( DISTINCT tc.espe,  '\n' ORDER BY tc.espe ASC ) as especialidades")
        ->selectRaw("STRING_AGG( CONCAT( TO_CHAR(tc.inicio, 'DD/MM/YYYY'),' - ', tc.curso),  '\n' ORDER BY tc.inicio DESC) as grupos")
        ->selectRaw("CASE 
                WHEN ap.datos_incorporacion->>'empresa' IS NOT NULL  THEN CONCAT('INCORPORADO A:  ', ap.datos_incorporacion->>'empresa') 
                ELSE '' END AS incorporadoa");
     
        ##CURSO
        if ($textcurso != null) {   
            $textcurso = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'],['A', 'E', 'I', 'O', 'U'] , $textcurso);            
            $query->where(DB::raw("translate(CONCAT (tc.curso,tc.espe), 'ÁÉÍÓÚ', 'AEIOU')"),'like', "%$textcurso%");            
        }
        ##NACIONALIDAD
        if ($nacionalidad != null) {
            if($nacionalidad == 'MEXICANA'){
                $query->whereIn('ti.nacionalidad', ['MEXICANA', 'MEXICANO']);
            }else{
                $query->whereNotIn('ti.nacionalidad', ['MEXICANA', 'MEXICANO'])->whereNotNull('ap.nacionalidad');
            }
        }
        ##FECHA DE INICIO Y FIN
        if($fecha_inicio != null && $fecha_fin != null){
            $query->where('ti.inicio', '>=', $fecha_inicio)
                    ->where('ti.inicio', '<=', $fecha_fin);
        }else{
            $query->whereYear('ti.inicio', '>=', $this->AnioAnterior);            
        }

        $total_reg = $query->get()->count();
        $results = $query->paginate(15);
        //dd($results);
        return [$results, $total_reg];
    }

    ## AUTOCOMPLETADO DE LISTA DE CURSOS
    public function autocomplete_cursos (Request $request) { 
        $buscar = $request->search;
         $this->AnioActual = date('Y');
        $this->AnioAnterior = $this->AnioActual - 1;

        if (isset($buscar) && $buscar != '') {

            $data = DB::table('tbl_cursos')
                ->selectRaw("
                    CASE 
                        WHEN curso LIKE ? THEN translate(curso, 'ÁÉÍÓÚ', 'AEIOU')
                        WHEN espe LIKE ? THEN translate(espe, 'ÁÉÍÓÚ', 'AEIOU')
                        ELSE NULL
                    END AS curso", ["%$buscar%", "%$buscar%"])
                ->where('status_curso','AUTORIZADO')                
                ->where(function ($q){
                    $q->whereYear('inicio', $this->AnioActual)
                    ->orWhereYear('inicio', $this->AnioAnterior);
                })  
                ->where(function ($query) use ($buscar) {
                    $query->where('curso','like', "%$buscar%")
                        ->orWhere('espe','like', "%$buscar%");
                })                
                ->distinct('curso')                                
                ->limit(10)
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
        ->where('ap.check_bolsa', true)
        ->where('ti.status', 'INSCRITO')->where('ti.calificacion', '<>', 'NP')->whereNotNull('ti.calificacion')
        ->where('tc.status_curso', 'AUTORIZADO')
        ->orderby(DB::raw('MAX(tc.inicio)'), 'DESC') 
        ->groupBy('ap.curp','ap.datos_incorporacion')
        ->select(
            DB::raw('EXTRACT(YEAR FROM max(ti.inicio)) as ejercicio'),
            'ap.curp',            
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
            DB::raw('MAX(ap.correo) as correo'),
        )
        // string_agg(elem, ', ') AS empresas
        ->selectRaw("CASE 
                WHEN ap.datos_incorporacion->>'empresa' IS NOT NULL  THEN CONCAT('EMPRESA:  ', ap.datos_incorporacion->>'empresa',', FECHA: ', ap.datos_incorporacion->>'fecha',', DIRECCIÓN: ', ap.datos_incorporacion->>'direccion', ', PUESTO: ', ap.datos_incorporacion->>'puesto') 
                ELSE '' END AS incorporadoa")
        ->selectRaw("STRING_AGG( DISTINCT tc.espe,  '\n' ORDER BY tc.espe ASC ) as especialidades")
        ->selectRaw("STRING_AGG( CONCAT( TO_CHAR(tc.inicio, 'DD/MM/YYYY'),' - ', tc.curso),  '\n' ORDER BY tc.inicio DESC) as grupos");

        ##CURSO
        if ($textcurso != null) {
            $textcurso = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'],['A', 'E', 'I', 'O', 'U'] , $textcurso);            
            $query->where(DB::raw("translate(CONCAT (tc.curso,tc.espe), 'ÁÉÍÓÚ', 'AEIOU')"),'like', "%$textcurso%");    
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
        $data = $query->get();

        ##Excel
        $head = ['EJERCICIO','CURP', 'ALUMNO', 'EDAD', 'NACIONALIDAD', 'SEXO',
                'COLONIA', 'MUNICIPIO', 'ESTADO', 'DOMICILIO', 'ESTADO CIVIL', 'GRADO DE ESTUDIOS', 'TELEFONO', 'CORREO','INCORPORACIÓN LABORAL', 'ESPECIALIDAD','CURSOS'];

        $title = "BOLSA DE TRABAJO";
        $name = $title."_".date('Ymd').".xlsx";
        //$view = 'layouts.pages.reportes.excel_bolsa_trabajo';
        /*
        $datos_vista = [
            'data' => $results,
            'curso' => $request->text_buscar_curso,
            'sel_nacionalidad' => $request->sel_nacionalidad,
            'fecha1' => $request->fechaIniV,
            'fecha2' => $request->fechaFinV
        ];
*/
            $title = "INCORPORACION LABORAL";
            $name = $title."_".date('Ymd-s').".xlsx";

            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);

        //if(count($results)>0)return Excel::download(new xlsConvenios($datos_vista,$head, $title,$view), $name);
        // dd($results);
    }
}
