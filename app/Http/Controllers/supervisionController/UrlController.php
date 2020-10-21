<?php
namespace App\Http\Controllers\supervisionController;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\supervision\Token;
use App\Models\supervision\TokenTrait;

class UrlController extends Controller
{
    use TokenTrait;
    public function generarUrl(Request $request) {
       $id = $request->get("id"); 
       $tipo = $request->get("tipo");
       $id_instructor = $id_alumno = 0;
       $id_user = Auth::user()->id;
       $token = 0;
       $path = "/form/{$tipo}/";
       $url="";
       if($tipo=="instructor"){
             $curso = DB::table('tbl_cursos')->where('id',$id)->first();
             $id_curso = $curso->id;
             $id_instructor = $curso->id_instructor;
             ///comprobar si existe el token      
             $token = DB::table('supervision_tokens')->where('id_supervisor',$id_user)->where('id_curso',$id_curso)->where('id_instructor',$id_instructor)->value('url_token');
       }elseif($tipo=="alumno"){ //comprobar si existe el alumno
            $inscript = DB::table('tbl_inscripcion')->where('id',$id)->first();
            $id_curso = $inscript->id_curso;
            $id_alumno = $inscript->id;
            ///comprobar si existe el token
            $token = DB::table('supervision_tokens')->where('id_supervisor',$id_user)->where('id_curso',$id_curso)->where('id_alumno',$id_alumno)->value('url_token');
       }
              
       if($this->DeleteTokenExpired($token)){
            $currentTime = time();
            $t = new Token;
            $token = $t->url_token = hash('sha256', Str::random(60));
            $t->ttl = 0;
            $t->id_curso = $id_curso;
            $t->id_supervisor = $id_user;
            if($id_instructor) $t->id_instructor = $id_instructor;
            else $t->id_alumno = $id_alumno;
            $t->ttl = $currentTime + (1440 * 60); //24 horas            
            $t->save();
       }
       if($token)
            $url = url("{$path}{$token}");
       else $url = "Instrucción Inválida";
       
       return $url;
        
    }
    
    public function form($urltoken, Request $request) {
        $token = $this->generateTmpToken($urltoken, $request);
        
        if($token == null)  {
            return response()->json(["message" => "Not found"], 404);
        }
        if (!$request->session()->has('tmpToken')) {
            $request->session()->forget('tmpToken');
        }
        $request->session()->put('tmpToken', $token->tmp_token);        
        
        if($token->id_instructor)
            return view('supervision.client.frminstructor', ['tmpToken' => $token->tmp_token, "registered"=> false]);
        elseif($token->id_alumno){
            $escolaridad = [
                'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
                'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
                'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
                'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
                'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
                'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
                'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
                'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
                'POSTGRADO' => 'POSTGRADO'
                ];
            return view('supervision.client.frmalumno', ['tmpToken' => $token->tmp_token, "registered"=> false, 'escolaridad'=>$escolaridad]);
        }
    }
    
    public function msg($id)
    {        
        if($id) $msg = " OPERACI&Oacute;N EXITOSA! ";
        else $msg = " OPERACI&Oacute;N INV&Aacute;LIDA ";
        return view('supervision.client.msg', compact('msg'));
    }
    
}
