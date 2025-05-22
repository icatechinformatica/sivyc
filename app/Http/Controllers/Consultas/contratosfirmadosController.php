<?php
namespace App\Http\Controllers\consultas;
use App\Http\Controllers\Controller;
use App\Models\instructor;
use App\Models\tbl_curso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Excel\xls;
use Maatwebsite\Excel\Facades\Excel;


class contratosfirmadosController extends Controller
{

    public function index(Request $request){

        $req = $request->all();        
        $unidades = DB::table('tbl_unidades')->pluck('unidad','unidad');         
        $estatus = ["ELECTRONICOS" => "ELECTRONICOS", "AUTOGRAFOS" => "AUTOGRAFOS"];
        list($consulta,$message) = $this->data($request); 
        return view('consultas.contratosfirmados',compact('consulta','unidades','req','message'));
    }

    public function xls(Request $request){
            $data = $this->data($request);            
            if(count($data)==0){ return "NO EXISTEN REGISTROS QUE MOSTRAR";exit;}
            else{
                foreach($data as $key => $value){
                    if($data[$key]->tdias <=0) $data[$key]->tdias = $data[$key]->dias;
                    $data[$key]->dias ="";
                }                
            }

            $head = ['INSTRUCTOR','UNIDAD','GRUPO','CLAVE','MEMO','CURSO','ESPECIALIDAD','SERVICIO','DURA','CAPACITACIÓN','ESTATUS',
            'INICIO','TERMINO','HINI','HFIN','DIAS','LABORADOS','ESPACIO FÍSICO','OBSERVACIONES'];

            $title = $request->busqueda;
            $name = "CONSULTA_INSTRUCTORES_ASIGNADOS_".$request->busqueda."_".date('Ymd').".xlsx";

            if(count($data)>0)return Excel::download(new xls($data,$head, $title), $name);
   }

   private function data(Request $request){
        $unidad = $request->unidad;
        $estatus = $request->estatus;
        $inicio = $request->fecha_inicio;
        $termino = $request->fecha_termino;
        $buscar= $request->busqueda;
        
        if($inicio and !$termino) $termino = $inicio;
        if(!$inicio and $termino) $inicio = $termino;

        $data = $message = null;
        if(($unidad OR $buscar OR $estatus) AND ($inicio AND $termino)){
            $data = DB::table('contratos as c',)
            ->select('numero_contrato','tc.munidad','tc.clave','tc.curso','tc.nombre as instructor','tc.unidad',
            'doc.id'
            );
            $data = $data->join('tbl_cursos as tc','tc.id','c.id_curso');
            if($inicio AND $termino) $data = $data->whereRaw('DATE(c.created_at) BETWEEN ? AND ?', [$inicio, $termino]);
            if($unidad) $data = $data->where('tc.unidad',$unidad);

            $joinMethod = $estatus === 'ELECTRONICOS' ? 'join' : 'leftJoin';
            $data = $data->{$joinMethod}('documentos_firmar as doc', function($join) {
                $join->on('doc.numero_o_clave', '=', 'tc.clave')
                    ->where('doc.tipo_archivo', 'Contrato');
            });            
       
            if($buscar) $data = $data->whereRaw("CONCAT(c.numero_contrato, tc.clave, tc.nombre) LIKE ?", ["%$buscar%"]);
            $data = $data->paginate(50);
            $data->appends($request->only(['unidad', 'estatus', 'fecha_inicio','fecha_termino','busqueda']));

        } elseif(!$inicio AND !$termino AND $request->all()) $message = "Por favor, ingrese un rango de fechas para filtrar los contratos.";
        
        return [$data, $message];

    }   

    public function eliminar_tildes($cadena){

        //Codificamos la cadena en formato utf8 en caso de que nos de errores
    $cadena = $cadena; //dd($cadena);

    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );

    $cadena = str_replace(
        array('ç', 'Ç'),
        array('c', 'C'),
        $cadena
    );

    return $cadena;

    }
}