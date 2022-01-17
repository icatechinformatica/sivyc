<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class organismosController extends Controller
{
    public function index(Request $request){
        $busqueda= $request->busqueda;
        $busquedapor = $request->busqueda_por;
        $organismos = DB::table('organismos_publicos')->select('*');
        switch ($busqueda) {
            case 'nombre':
                $organismos = $organismos->whereRaw("organismo like '%$busquedapor%'");
                break;
            case 'area':
                $organismos = $organismos->whereRaw("poder_pertenece like '%$busquedapor%'");
                break;
            default:
                # code...
                break;
        }
        $organismos= $organismos->orderBy('organismo')->paginate(15);
        return view('organismos.index', compact('organismos'));
    }
    public function agregar(Request $request){
        $municipio = $localidad = [];
        $organismo = null;
        $areas = ["PODER LEGISLATIVO"=>"PODER LEGISLATIVO","PODER JUDICIAL"=>"PODER JUDICIAL","PODER EJECUTIVO"=>"PODER EJECUTIVO", 
                    "ORGANISMOS AUTONOMOS"=>"ORGANISMOS AUTONOMOS","PRIVADO"=>"PRIVADO"];
        $sector = ["PUBLICO"=>"PUBLICO","PRIVADO"=>"PRIVADO","SOCIAL"=>"SOCIAL"];
        $tipo = ["DEPENDENCIA"=>"DEPENDENCIA","EMPRESAS DE PARTICIPACION ESTATAL"=>"EMPRESAS DE PARTICIPACION ESTATAL",
                "ORGANISMOS"=>"ORGANISMOS","ORGANISMOS AUXILIARES DEL EJECUTIVO"=>"ORGANISMOS AUXILIARES DEL EJECUTIVO",
                "ORGANISMOS DEL PODER JUDICIAL"=>"ORGANISMOS DEL PODER JUDICIAL","ORGANOS DESCONCENTRADOS"=>"ORGANOS DESCONCENTRADOS",
                "ORGANISMOS DEL PODER LEGISLATIVO"=>"ORGANISMOS DEL PODER LEGISLATIVO","ORGANISMOS PUBLICOS DESCENTRALIZADOS SECTORIZADOS"=>"ORGANISMOS PUBLICOS DESCENTRALIZADOS SECTORIZADOS",
                "ORGANISMOS PUBLICOS DESCENTRALIZADOS DESECTORIZADOS"=>"ORGANISMOS PUBLICOS DESCENTRALIZADOS DESECTORIZADOS"];
        $status = ["ACTIVO"=>"ACTIVO","INACTIVO"=>"INACTIVO"];
        $estados = DB::table('estados')->select('id','nombre')->pluck('nombre','id');
        $update = false;
        $id = base64_decode($request->id);
        if ($id) {
            $organismo = DB::table('organismos_publicos')->select('*')->where('id',$id)->first();
            $municipio = DB::table('tbl_municipios')->select('muni','id')->where('id_estado','=', $organismo->id_estado)->pluck('muni','id');
            $clave_muni = DB::table('tbl_municipios')->select('clave')->where('id',$organismo->id_municipio)->value('clave');
            $localidad = DB::table('tbl_localidades')
                            ->select('localidad','clave')
                            ->where('clave_municipio','=', $clave_muni)->where('id_estado',$organismo->id_estado)
                            ->pluck('localidad','clave');
            $update = true;
        }
        return view('organismos.vstaformorganismo',compact('organismo','areas','estados','sector','tipo','status','municipio','localidad','update'));
    }
    public function store(Request $request){
        //dd($request->all());
        if ($request->status == 'ACTIVO') {
            $activo = true;
        } else {
            $activo = false;
        }
        $ID = DB::table('organismos_publicos')->value(DB::raw('max(id)+1'));
        $result = DB::table('organismos_publicos')->updateOrInsert(['id'=>$ID,'organismo'=>$request->nombre,'nombre_titular'=>$request->nombre_titular,
                                                    'telefono'=>$request->telefono,'correo'=>$request->correo_ins,'id_estado'=>$request->estado,
                                                    'id_municipio'=>$request->municipio,'clave_localidad'=>$request->localidad,'direccion'=>$request->direccion,
                                                    'poder_pertenece'=>$request->area,'activo'=>$activo,'sector'=>$request->sector,
                                                    'tipo'=>$request->tipo,'created_at' => date('Y-m-d H:i:s')]);
        
        return redirect()->route('organismos.index')->with('success', sprintf('Carga de organismo exitoso!'));
    }
    public function update(Request $request){
        $id=$request->id;
        if ($request->status == 'ACTIVO') {
            $activo = true;
        } else {
            $activo = false;
        }
        $result = DB::table('organismos_publicos')->where('id',$id)->update(['organismo'=>$request->nombre,'nombre_titular'=>$request->nombre_titular,
                                                                    'telefono'=>$request->telefono,'correo'=>$request->correo_ins,'id_estado'=>$request->estado,
                                                                    'id_municipio'=>$request->municipio,'clave_localidad'=>$request->localidad,'direccion'=>$request->direccion,
                                                                    'poder_pertenece'=>$request->area,'activo'=>$activo,'sector'=>$request->sector,
                                                                    'tipo'=>$request->tipo,'updated_at' => date('Y-m-d H:i:s')]);
        $id=base64_encode($id);
        return redirect()->route('organismos.agregar',compact('id'))->with('success', sprintf('Modificación exitosa!'));
    }
    public function muni(Request $request){
        if($request->ajax()){
            if ($request->municipio_id) {
                $clave = DB::table('tbl_municipios')->select('id_estado','clave')->where('id',$request->municipio_id)->first();
                $id= DB::table('tbl_localidades')
                    ->select('localidad','clave')
                    ->where('clave_municipio','=', $clave->clave)->where('id_estado',$clave->id_estado)
                    ->get(); 
                foreach($id as $titular){
                    $localidadArray[$titular->clave]= $titular->localidad;
                }
                return response()->json($localidadArray);
            }else {
                $id= DB::table('tbl_municipios')->select('muni','id')->where('id_estado','=', $request->estado_id)->get(); 
                foreach($id as $titular){
                    $localidadArray[$titular->id]= $titular->muni;
                }
                return response()->json($localidadArray);
            }
        }
    }
}
