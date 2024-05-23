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

        $this->cols = ["#","MEMORANDUMS","UNIDAD","CAPACITACIÓN","ESPECIALIDAD","CURSO","CLAVE","MOD","DURACIÓN HORAS","TURNO","DIA INI","MES INI","DIA TER","MES TER",
        "PERIODO","HORAS DIARIAS","DIAS","HORARIO","INSCRITOS",
        "FEM","MASC","EGRESADO","EGRESADO FEM","EGRESADO MASC","DESERCIÓN","C TOTAL CURSO PERSONA","INGRESO TOTAL","CUOTA MIXTA","EXO MUJER",
        "EXO HOMBRE","REDU MUJER","REDU HOMBRE","CONVENIO ESPEC.","MEMO VALIDA CURSO","ESPACIO FISICO","INSTRUCTOR","ESCOLARIDAD INSTRUCTOR",
        "DOCUMENTO ADQ","SEXO","MEMO VALIDACION","MEMO EXONERACION","EMPLEADOS","DESEMPLEADOS","DISCAPACITADOS","MIGRANTE","INDIGENA","ETNIA",
        "PROGRAMA ESTRAT","MUNICIPIO","ZE","REGION","DEPENDENCIA","CONVENIO GENERAL","CONV SEC PUB O PRIV","VALIDACION PAQUET","GRUPO VULNERABLE",                          
        "INSC EDAD M1","INSC EDAD H1","INSC EDAD M2","INSC EDAD H2","INSC EDAD M3","INSC EDAD H3","INSC EDAD M4","INSC EDAD H4",
        "INSC EDAD M5","INSC EDAD H5","INSC EDAD M6","INSC EDAD H6","INSC EDAD M7","INSC EDAD H7","INSC EDAD M8","INSC EDAD H8",
        "INSC ESCOL M1","INSC ESCOL H1","INSC ESCOL M2","INSC ESCOL H2","INSC ESCOL M3","INSC ESCOL H3","INSC ESCOL M4","INSC ESCOL H4",
        "INSC ESCOL M5","INSC ESCOL H5","INSC ESCOL M6","INSC ESCOL H6","INSC ESCOL M7","INSC ESCOL H7","INSC ESCOL M8","INSC ESCOL H8",
        "INSC ESCOL M9","INSC ESCOL H9","ACRE ESCOL M1","ACRE ESCOL H1","ACRE ESCOL M2","ACRE ESCOL H2","ACRE ESCOL M3","ACRE ESCOL H3",
        "ACRE ESCOL M4","ACRE ESCOL H4","ACRE ESCOL M5","ACRE ESCOL H5","ACRE ESCOL M6","ACRE ESCOL H6","ACRE ESCOL M7","ACRE ESCOL H7",
        "ACRE ESCOL M8","ACRE ESCOL H8","ACRE ESCOL M9","ACRE ESCOL H9","DESC ESCOL M1","DESC ESCOL H1","DESC ESCOL M2","DESC ESCOL H2",
        "DESC ESCOL M3","DESC ESCOL H3","DESC ESCOL M4","DESC ESCOL H4","DESC ESCOL M5","DESC ESCOL H5","DESC ESCOL M6","DESC ESCOL H6",
        "DESC ESCOL M7","DESC ESCOL H7","DESC ESCOL M8","DESC ESCOL H8","DESC ESCOL M9","DESC ESCOL H9","OBSERVACIONES","INSCRITOS","FEM",
        "MASC","LGBTTTI+","EGRESADO","EGRESADO FEM","EGRESADO MASC","EGRESADO LGBTTTI+","EXO MUJER","EXO HOMBRE","EXO LGBTTTI+","REDU MUJER",
        "REDU HOMBRE","REDU LGBTTTI+","INSC EDAD M1","INSC EDAD H1","INSC EDAD L1","INSC EDAD M2","INSC EDAD H2","INSC EDAD L2","INSC EDAD M3",
        "INSC EDAD H3","INSC EDAD L3","INSC EDAD M4","INSC EDAD H4","INSC EDAD L4","INSC ESCOL M1","INSC ESCOL H1","INSC ESCOL L1","INSC ESCOL M2",
        "INSC ESCOL H2","INSC ESCOL L2","INSC ESCOL M3","INSC ESCOL H3","INSC ESCOL L3","INSC ESCOL M4","INSC ESCOL H4","INSC ESCOL L4","INSC ESCOL M5",
        "INSC ESCOL H5","INSC ESCOL L5","INSC ESCOL M6","INSC ESCOL H6","INSC ESCOL L6","INSC ESCOL M7","INSC ESCOL H7","INSC ESCOL L7","INSC ESCOL M8",
        "INSC ESCOL H8","INSC ESCOL L8","INSC ESCOL M9","INSC ESCOL H9","INSC ESCOL L9","ACRE ESCOL M1","ACRE ESCOL H1","ACRE ESCOL L1","ACRE ESCOL M2",
        "ACRE ESCOL H2","ACRE ESCOL L2","ACRE ESCOL M3","ACRE ESCOL H3","ACRE ESCOL L3","ACRE ESCOL M4","ACRE ESCOL H4","ACRE ESCOL L4","ACRE ESCOL M5",
        "ACRE ESCOL H5","ACRE ESCOL L5","ACRE ESCOL M6","ACRE ESCOL H6","ACRE ESCOL L6","ACRE ESCOL M7","ACRE ESCOL H7","ACRE ESCOL L7","ACRE ESCOL M8",
        "ACRE ESCOL H8","ACRE ESCOL L8","ACRE ESCOL M9","ACRE ESCOL H9","ACRE ESCOL L9","DESC ESCOL M1","DESC ESCOL H1","DESC ESCOL L1","DESC ESCOL M2",
        "DESC ESCOL H2","DESC ESCOL L2","DESC ESCOL M3","DESC ESCOL H3","DESC ESCOL L3","DESC ESCOL M4","DESC ESCOL H4","DESC ESCOL L4","DESC ESCOL M5",
        "DESC ESCOL H5","DESC ESCOL L5","DESC ESCOL M6","DESC ESCOL H6","DESC ESCOL L6","DESC ESCOL M7","DESC ESCOL H7","DESC ESCOL L7","DESC ESCOL M8",
        "DESC ESCOL H8","DESC ESCOL L8","DESC ESCOL M9","DESC ESCOL H9","DESC ESCOL L9","GV AFROMEX M","GV AFROMEX H","GV AFROMEX L","GV DESPLAZADAS M",
        "GV DESPLAZADAS H","GV DESPLAZADAS L","GV EMBARAZADAS M","GV EMBARAZADAS H","GV EMBARAZADAS L","GV SIT CALLE M","GV SIT CALLE H","GV SIT CALLE L",
        "GV ESTUDIANTES M","GV ESTUDIANTES H","GV ESTUDIANTES L","GV FAM VIC M","GV FAM VIC H","GV FAM VIC L","GV INDIGENA M","GV INDIGENA H","GV INDIGENA L",
        "GV JEFA FAM M","GV JEFA FAM H","GV JEFA FAM L","GV MIGRANTE M","GV MIGRANTE H","GV MIGRANTE L","GV LESBIANA M","GV LESBIANA H","GV LESBIANA L",
        "GV CERSS M","GV CERSS H","GV CERSS L","GV TRANS M","GV TRANS H","GV TRANS L","GV TRAB HOGAR M","GV TRAB HOGAR H","GV TRAB HOGAR L","GV TRAB SEX M",
        "GV TRAB SEX H","GV TRAB SEX L","GV VICT VIOLENCIA M","GV VICT VIOLENCIA H","GV VICT VIOLENCIA L","GV DISC VISUAL M","GV DISC VISUAL H",
        "GV DISC VISUAL L","GV DISC AUDI M","GV DISC AUDI H","GV DISC AUDI L","GV DISC HABLA M","GV DISC HABLA H","GV DISC HABLA L","GV DISC MOTRIZ M",
        "GV DISC MOTRIZ H","GV DISC MOTRIZ L","GV DISC MENTAL M","GV DISC MENTAL H","GV DISC MENTAL L"
         ];
    }

    public function index(Request $request) {      // dd($request->unidad);
        $valor = $fecha = $message = $unidad = null;
        $data=[];        
        if($request->fecha){
            if($request->mes){
                $mes = date("Y-m", strtotime($request->fecha));
                $data = dataFormatoTold($request->unidad, ['REPORTADO'], null, $mes, true);
            }else
                $data = dataFormatoTold($request->unidad, ['REPORTADO'], $request->fecha, null, true);
        }
        if($request->valor){            
            $data = dataFormatoTold($request->unidad ['REPORTADO'], null, null, true, $request->valor);
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
                $mes = date("Y-m", strtotime($request->fecha));
                $data = dataFormatoT($request->unidad, ['REPORTADO'], null, $mes, false);
            }else
                $data = dataFormatoT($request->unidad, ['REPORTADO'], $request->fecha, null, false);
        }
        if($request->valor){            
            $data = dataFormatoT($request->unidad ['REPORTADO'], null, null, false, $request->valor);
        }
        
        foreach ($data as $value) {
            unset($value->id_tbl_cursos);
            unset($value->id_tbl_cursos);
            unset($value->estadocurso);
            unset($value->madres_solteras);
            unset($value->observaciones_firma);
            // unset($value->totalinscripciones);
            // unset($value->masculinocheck);
            // unset($value->femeninocheck);
            unset($value->sumatoria_total_ins_edad);
            unset($value->observaciones_enlaces);
            unset($value->termino);
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
