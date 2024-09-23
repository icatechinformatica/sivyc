<?php
/**
 * DESARROLLADO POR MIS LSC DANIEL MÉNDEZ CRUZ
 */
namespace App\Http\Controllers\FormatoT;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport
use App\Models\Unidad;

class Consultaftcontroller extends Controller {
    function __construct(Request $request) {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion');
            if (strpos($this->user->roles[0]->slug, 'unidad') === false){
                $this->unidades = Unidad::orderby('unidad')->where('cct','like','07EI%')->pluck('unidad','unidad');
                $this->ubicacion = $request->unidad;
           }else
                $this->unidades = Unidad::where('ubicacion',$this->ubicacion)->where('cct','like','07EI%')->orderby('unidad')->pluck('unidad','unidad');
            return $next($request);
        });

        $this->cols = ["#","MEMORANDUMS","UNIDAD DE CAPACITACION","PLANTEL","ESPECIALIDAD","CURSO","CLAVE","MOD","DURACIÓN TOTAL EN HORAS",
            "TURNO","DIA INICIO","MES INICIO","DIA TERMINO","MES TERMINO","PERIODO","HORAS DIARIAS","DIAS","HORARIO","INSCRITOS","FEM","MASC",
            "EGRESADO","EGRESADO FEM","EGRESADO MASC","DESERCIÓN","C TOTAL CURSO PERSONA","INGRESO TOTAL","EXONERACION MUJER",
            "EXONERACION HOMBRE","REDUCCION CUOTA MUJER","REDUCCION CUOTA HOMBRE","CONVENIO ESPECIFICO","MEMO VALIDA CURSO","ESPACIO FISICO",
            "INSTRUCTOR","ESCOLARIDAD INSTRUCTOR","DOCUMENTO ADQ","SEXO","MEMO VALIDACION","MEMO EXONERACION","EMPLEADOS","DESEMPLEADOS",
            "DISCAPACITADOS","MIGRANTE","ADOLESCENTES EN CONDICION DE CALLE","MUJERES JEFAS DE FAMILIA","INDIGENA","RECLUSOS",
            "PROGRAMA ESTRATEGICO","MUNICIPIO","ZE","REGION","DEPENDENCIA BENEFICIADA","CONVENIO GENERAL","CONV SEC PUB O PRIV",
            "VALIDACION PAQUETERIA","GRUPO VULNERABLE","INSC EDAD M1","INSC EDAD H1","INSC EDAD M2","INSC EDAD H2","INSC EDAD M3",
            "INSC EDAD H3","INSC EDAD M4","INSC EDAD H4","INSC EDAD M5","INSC EDAD H5","INSC EDAD M6","INSC EDAD H6","INSC EDAD M7",
            "INSC EDAD H7","INSC EDAD M8","INSC EDAD H8","INSC ESCOL M1","INSC ESCOL H1","INSC ESCOL M2","INSC ESCOL H2","INSC ESCOL M3",
            "INSC ESCOL H3","INSC ESCOL M4","INSC ESCOL H4","INSC ESCOL M5","INSC ESCOL H5","INSC ESCOL M6","INSC ESCOL H6","INSC ESCOL M7",
            "INSC ESCOL H7","INSC ESCOL M8","INSC ESCOL H8","INSC ESCOL M9","INSC ESCOL H9","ACRE ESCOL M1","ACRE ESCOL H1","ACRE ESCOL M2",
            "ACRE ESCOL H2","ACRE ESCOL M3","ACRE ESCOL H3","ACRE ESCOL M4","ACRE ESCOL H4","ACRE ESCOL M5","ACRE ESCOL H5","ACRE ESCOL M6",
            "ACRE ESCOL H6","ACRE ESCOL M7","ACRE ESCOL H7","ACRE ESCOL M8","ACRE ESCOL H8","ACRE ESCOL M9","ACRE ESCOL H9","DESER ESCOL M1",
            "DESER ESCOL H1","DESER ESCOL M2","DESER ESCOL H2","DESER ESCOL M3","DESER ESCOL H3","DESER ESCOL M4","DESER ESCOL H4",
            "DESER ESCOL M5","DESER ESCOL H5","DESER ESCOL M6","DESER ESCOL H6","DESER ESCOL M7","DESER ESCOL H7","DESER ESCOL M8",
            "DESER ESCOL H8","DESER ESCOL M9","DESER ESCOL H9"
         ];
    }

    public function index(Request $request) {      // dd($request->unidad);
        // dd($request);
        $valor = $fecha = $message = $unidad = null;
        $data=[];
        if($request->fecha){
            if($request->mes){
                $mes = date("m", strtotime($request->fecha));
                $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], null, $mes, true);
            }else{
                $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], $request->fecha, null, true);
            }
        }
        if($request->valor){
            $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], null, null, true, $request->valor);
        }

        $unidades = $this->unidades;
        $unidad = $this->ubicacion;
        $fecha = $request->fecha;
        $mes = $request->mes;
        $valor = $request->valor;
        $cols = $this->cols;
        return view('formatot.consultaft',compact('data', 'fecha', 'unidades','unidad','valor','mes','message','cols'));
    }

    protected function xls(Request $request){
        $valor = $fecha = $message = $unidad = null;
        $data=[];
        if($request->fecha){
            if($request->mes){
                $mes = date("m", strtotime($request->fecha));
                $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], null, $mes, false);
            }else{
                $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], $request->fecha, null, false);
            }
        }
        if($request->valor){
            $data = dataFormatoT($request->unidad, null, null, null, ['REPORTADO'], null, null, false, $request->valor);
        }

        foreach ($data as $key => $value) {
            unset($value->id_tbl_cursos);
            unset($value->id_tbl_cursos);
            unset($value->estadocurso);
            unset($value->madres_solteras);
            unset($value->observaciones_firma);
            unset($value->fechaturnado);
            // unset($value->totalinscripciones);
            // unset($value->masculinocheck);
            // unset($value->femeninocheck);
            unset($value->sumatoria_total_ins_edad);
            unset($value->observaciones_enlaces);
            unset($value->termino);
            unset($value->turnados_enlaces);
            unset($value->cuotamixta);
            unset($value->etnia);
            unset($value->arc);
            unset($value->tnota);
            unset($value->comentario_enlaces_retorno);
            unset($value->observaciones_unidad);
            unset($value->status_solicitud_arc02);
            if(is_null($value->grupo)) { $data[$key]->grupo = 'NINGUNO'; }
            // dd($value);
        }

        // 'id curso', 'ESTADO DEL CURSO', discapacitados, ->, madres solteras
        $head = $this->cols;
        unset($head[0]);
        unset($head[1]);

        $nombreLayout = "Reporte_FormatoT_".date("Ymd").".xlsx";
        $titulo = "FORMATOT_".date("Ymd");

        if(count($data)>0) {
            return Excel::download(new FormatoTReport($data,$head, $titulo), $nombreLayout);
        }
    }
}
