<?php
//Creado por Romelia Pérez Nangüelú--rpnanguelu@gmail.com
namespace App\Http\Controllers\TableroControlller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MetasController extends Controller
{
    function __construct() {
      $this->meses = [1=>"ENERO",2=>"FEBRERO",3=>"MARZO",4=>"ABRIL",5=>"MAYO",6=>"JUNIO",7=>"JULIO",8=>"AGOSTO",9=>"SEPTIEMBRE", 10=>"OCTUBRE", 11=>"NOVIEMBRE",12=>"DICIEMBRE"];
      $this->field = [1=>"ene",2=>"feb",3=>"mar",4=>"abr",5=>"may",6=>"jun",7=>"jul",8=>"ago",9=>"sep", 10=>"oct", 11=>"nov",12=>"dic"];
    }
    
    public function index(Request $request)
    {       
        if($request->get('ejercicio'))$ejercicio = $request->get('ejercicio');
        else $ejercicio = date("Y");
        $sql= "";
        for($n=1;$n<=12; $n++){
            $sql .= "(SELECT count(c1.id) FROM tbl_cursos c1 WHERE c1.unidad = p.tbl_unidades_unidad AND
    			 	EXTRACT(MONTH FROM c1.fecha_apertura)='".str_pad($n, 2, "0", STR_PAD_LEFT)."' AND  EXTRACT(YEAR FROM c1.fecha_apertura)='$ejercicio' AND c1.clave!='0') as ".$this->field[$n]."_r,";
            $sql .= "(SELECT COALESCE(sum(c1.dura),0) FROM tbl_cursos c1 WHERE c1.unidad = p.tbl_unidades_unidad AND
    			 	EXTRACT(MONTH FROM c1.fecha_apertura)='".str_pad($n, 2, "0", STR_PAD_LEFT)."' AND  EXTRACT(YEAR FROM c1.fecha_apertura)='$ejercicio' AND c1.clave!='0') as hr_".$this->field[$n]."_r,";
            $sql .= "(SELECT COALESCE(sum(c1.hombre+c1.mujer),0)FROM tbl_cursos c1 WHERE c1.unidad = p.tbl_unidades_unidad AND
    			 	EXTRACT(MONTH FROM c1.fecha_apertura)='".str_pad($n, 2, "0", STR_PAD_LEFT)."' AND  EXTRACT(YEAR FROM c1.fecha_apertura)='$ejercicio' AND c1.clave!='0') as benef_".$this->field[$n]."_r,";
            $sql .= "promedio_benef*".$this->field[$n]." as benef_".$this->field[$n].",";
            $sql .= "(SELECT COALESCE(sum(f.importe_total),0) FROM folios f, tbl_cursos c1 WHERE f.id_cursos = c1.id  AND c1.unidad = p.tbl_unidades_unidad AND f.status = 'Finalizado' AND
    			 	EXTRACT(MONTH FROM c1.fecha_apertura)='".str_pad($n, 2, "0", STR_PAD_LEFT)."' AND  EXTRACT(YEAR FROM c1.fecha_apertura)='$ejercicio' AND c1.clave!='0') as inversion_".$this->field[$n].",";
        }
        
        $data=DB::select("SELECT ".$sql." p.*  FROM poa p WHERE p.id_plantel = 0 AND p.ejercicio='$ejercicio' order by p.id");
        $data = json_decode(json_encode($data), true);       
        
        $breadcrumb = "POA y MIR";        
        $lst_field = $this->field;
        
        foreach($lst_field as $f){
            $dataT[$f] = array_sum(array_column($data, $f));
            $dataT[$f.'_r'] = array_sum(array_column($data, $f.'_r'));
            $dataT['hr_'.$f] = array_sum(array_column($data, 'hr_'.$f));
            $dataT['hr_'.$f.'_r'] = array_sum(array_column($data, 'hr_'.$f.'_r'));
            $dataT['benef_'.$f] = array_sum(array_column($data, 'benef_'.$f));
            $dataT['benef_'.$f.'_r'] = array_sum(array_column($data, 'benef_'.$f.'_r'));
            $dataT['inversion_'.$f] = array_sum(array_column($data, 'inversion_'.$f));
        }
        
        $lst_ejercicio =  DB::table('poa')->where('id_plantel',0)->orderby('ejercicio','ASC')->pluck('ejercicio','ejercicio');
        return view('tablero.metas.index', compact('data','dataT','breadcrumb','lst_field','lst_ejercicio','ejercicio'));
    }
}
