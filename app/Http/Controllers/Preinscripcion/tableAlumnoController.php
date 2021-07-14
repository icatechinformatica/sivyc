<?php

namespace App\Http\Controllers\Preinscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;

class tableAlumnoController extends Controller
{   
   
    public function delete(Request $request){ 
        $id_grupo = session('id_grupo');
        $id_user = Auth::user()->id;  
        $id = $request->id;
        if($id){
           $result = DB::table('alumnos_registro')->where('id_grupo',$id_grupo)->where('id',$id)->update(['eliminado'=>true,'iduser_updated'=>$id_user]);
           //$result = DB::table('alumnos_registro')->where('id',$id)->delete();
        }else $result = false;
        echo $result; exit;
        return $result;
    }   
    
}