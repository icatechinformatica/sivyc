<?php

namespace App\Http\Controllers\reportesController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportExcel;
use App\Utilities\MyUtility;
use PDF;
class patController extends Controller
{
    function __construct() {
        $this->meses = ["enero"=>"ENERO","febrero"=>"FEBRERO","marzo"=>"MARZO","abril"=>"ABRIL",
            "mayo"=>"MAYO","junio"=>"JUNIO","julio"=>"JULIO","agosto"=>"AGOSTO","septiembre"=>"SEPTIEMBRE",
            "octubre"=>"OCTUBRE","nomviembre"=>"NOVIEMBRE","diciembre"=>"DICIEMBRE"];
        $this->abrev_org = ['1'=>'DIRECCIONES_UC','2'=>'DELEG_ADTVAS', '3'=>'DEPTOS_ACADEMICOS','4'=>'DEPTOS_VINCULACION'];
        $this->organismos = ['1'=>'DIRECCIONES DE UNIDADES DE CAPACITACIÓN','2'=>'DELEGACIONES ADMINISTRATIVAS', '3'=>'DEPARTAMENTOS ACADEMICOS','4'=>'DEPARTAMENTOS DE VINCULACIÓN'];


    }

    public function index(Request $request){
        $id_user = Auth::user()->id;
        $anios = MyUtility::ejercicios();
        $meses = $this->meses;

        $organismo = $mes = null;
        $data = $message = [];
        $ejercicio = date('Y');
        $organismos = $this->organismos;
        if(session('message')) $message = session('message');
        if(session('data')) $data = session('data');
        if(session('organismo')) $organismo = session('organismo');
        if(session('mes')) $mes = session('mes');

        return view('reportes.pat.index', compact('data','message','organismos','organismo','anios','ejercicio','meses','mes'));
    }

    public function generar(Request $request){    //dd($request->request->parameters);
        $data = $message = [];
        $data = $this->data($request);
        if (array_key_exists("ERROR", $data)){
            $message = $data;
            $data = [];
        }
        //dd($request->opcion);
        if($data){
            switch($request->opcion){
                case "FILTRAR":
                    return redirect('reportes/pat')->with(['data'=>$data,'message'=>$message, 'organismo'=>$request->organismo, 'mes' => $request->mes]);
                break;
                case "XLS":
                    $a_org = $this->abrev_org[$request->organismo];
                    $title = "PAT-Concentrado_".$a_org;
                    $name = $title."_".date('Ymd').".xlsx";
                    $view = 'reportes.pat.excel_concentrado';
                    return Excel::download(new ExportExcel($data,null, $title,$view), $name);
                break;
                case "PDF":      // dd(Auth::user()->id_organismo);
                    $firmante1 = $firmante2 = null;
                    if(Auth::user()->id_organismo){
                        $firmante1 = DB::table('tbl_organismos as org')->where('org.id',Auth::user()->id_organismo)
                        ->select('func.nombre','func.cargo','func.titulo','org.id_parent')
                        ->leftjoin('tbl_funcionarios as func','func.id','org.id')
                        ->where('func.titular',true)
                        ->first();
                        if($firmante1){
                            $firmante2 = DB::table('tbl_organismos as org')->where('org.id',$firmante1->id_parent)
                            ->select('func.nombre','func.cargo','func.titulo','org.id_parent')
                            ->leftjoin('tbl_funcionarios as func','func.id','org.id')
                            ->where('func.titular',true)
                            ->first(); //dd($firmante2);
                        }
                    }
                    $ejercicio = $request->ejercicio;
                    $padre = "UNIDADES DE CAPACITACIÓN";
                    $a_org = $this->abrev_org[$request->organismo];
                    if($request->organismo == "1") $hijo = "DIRECCIONES";
                    else $hijo = $this->organismos[$request->organismo];
                    $file_name = "PAT-Concentrado_".$a_org."_".date('Ymd').".pdf";
                    $pdf = PDF::loadView('reportes.pat.pdf-concentrado',compact('data','ejercicio', 'padre', 'hijo', 'firmante1','firmante2'));
                    $pdf->setpaper('letter','Landscape');
                    return $pdf->stream($file_name, ['Content-Type' => 'application/pdf']);
                break;
            }

        }else return redirect('reportes/pat')->with(['message'=>$message]);
    }

    private function data(Request $request){
        if($request->organismo and $request->mes and $request->ejercicio){
            $ids_org = [];
            $query_meta = $query_avance = $otro = null;
            switch($request->organismo){
                case "1"://DIRECCIONES DE UNIDADES DE CAPACITACIÓN
                    $ids_org = DB::table('tbl_organismos as org')
                        ->join('tbl_unidades as tu','org.id_unidad','tu.id')
                        ->where('org.nombre','like', '%UNIDAD%')->where('org.id_parent','1')->where('org.id_unidad','>','0')->orderby('org.nombre')
                            ->pluck('tu.unidad','org.id');
                break;
                default://DIRECCIONES DE UNIDADES DE CAPACITACIÓN
                    if($request->organismo=="2") $otro = "DELEGACIÓN ADMINISTRATIVA";
                    elseif($request->organismo=="3") $otro = "DEPARTAMENTO ACADEMICO";
                    elseif($request->organismo=="4") $otro = "DEPARTAMENTO DE VINCULACION";
                    if($otro){
                        $ids_org = DB::table('tbl_organismos as org')
                            ->join('tbl_unidades as tu','org.id_unidad','tu.id')
                            ->whereRaw("unaccent(lower(org.nombre)) LIKE unaccent(lower('%$otro%'))") ///CREA LA EXTENSiÓN EN LA BASE DE DATOS (CREATE EXTENSION IF NOT EXISTS unaccent;)
                            ->where('org.nombre','like', '%UNIDAD%')->where('org.id_parent','>','1')->where('org.id_unidad','>','0')->orderby('org.nombre')
                            ->pluck('tu.unidad','org.id');
                    }
                break;
            }
            //dd($ids_org);
            foreach($this->meses as $mes => $nombre){
                if($request->mes == $mes){
                    $query_meta .= "SUM(($mes->>'meta')::integer)";
                    $query_avance .= "SUM(($mes->>'avance')::integer)";
                    break;
                }else{
                     $query_meta .= "SUM(($mes->>'meta')::integer)+";
                     $query_avance .= "SUM(($mes->>'avance')::integer)+";
                }
            }

            if(count($ids_org)>0){
                $data = DB::table("funciones_proced as fp")->select(
                    DB::raw('MAX(fp.id) as max_id'),
                    DB::raw('MAX(fp.id_parent) as idparent'),
                    DB::raw('(SELECT count(id) FROM funciones_proced WHERE id_parent = MAX(fp.id_parent) group by id_parent) as rowspan'),
                    'ma.ejercicio','um.unidadm','um.tipo_unidadm',
                    DB::raw('(SELECT fun_proc FROM funciones_proced WHERE id = MAX(fp.id_parent)) as funcion'),
                    'fp.fun_proc as procedimiento',
                    DB::raw("($query_meta) as programada"),
                    DB::raw("($query_avance) as alcanzada")
                )
                ->join('metas_avances_pat as ma', 'fp.id', '=', 'ma.id_proced')
                ->leftjoin('unidades_medida as um','um.id','fp.id_unidadm')
                ->whereIn('fp.id_org', function($query) use ($otro) {
                    $query->select('id')
                        ->from('tbl_organismos')
                        ->where('nombre', 'like', '%UNIDAD%')
                        ->where('id_unidad', '>', 0);
                        if($otro){
                            $query = $query->whereRaw("unaccent(lower(nombre)) LIKE unaccent(lower('%$otro%'))") ///CREA LA EXTENSiÓN EN LA BASE DE DATOS (CREATE EXTENSION IF NOT EXISTS unaccent;)
                            ->where('id_parent','>','1');
                        }else $query = $query->where('id_parent', 1);

                })
                ->where('ma.ejercicio', $request->ejercicio);
                //dd(count($ids_org));
                if(count($ids_org)){
                    foreach($ids_org as $idorg => $nombre){
                        //dd($nombre);
                        $nombre = str_replace(' ', '_', $nombre);

                        $data = $data->addSelect(
                            DB::raw("(SELECT ".$query_meta." FROM funciones_proced, metas_avances_pat WHERE funciones_proced.id = metas_avances_pat.id_proced AND fun_proc = fp.fun_proc AND id_org = ".$idorg." AND ejercicio = '".$request->ejercicio."') as prog_".$idorg),
                            DB::raw("(SELECT ".$query_avance." FROM funciones_proced, metas_avances_pat WHERE funciones_proced.id = metas_avances_pat.id_proced AND fun_proc = fp.fun_proc AND id_org = ".$idorg." AND ejercicio = '".$request->ejercicio."') as alc_".$idorg),
                            DB::raw($idorg." as id_org")
                        );
                    }
                }

                $data = $data->groupBy('fp.fun_proc', 'ma.ejercicio','um.id')
                ->orderBy('idparent','ASC')
                ->orderBy('max_id','ASC')
                ->get();
                 //dd($data);
                return [$data, $ids_org];
            }else $message["ERROR"] = "NO EXISTEN REGISTROS DEL ORGANISMO SELECCIONADO.";

        }else $message["ERROR"] = "SE REQUIERE QUE SELECCIONE UN ORANISMO Y EL MES PARA EJECUTAR FILTRADO.";

        if($message) return $message;
    }
}
