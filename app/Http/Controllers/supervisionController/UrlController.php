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
             $instructor = DB::table('instructores')->select('telefono','correo')->where('id',$id_instructor)->first();
             //var_dump($instructor);
             $datos = "\n\n** DATOS DEL INSTRUCTOR **\nTELEFONO: ".$instructor->telefono."\n CORREO: ".$instructor->correo;

             $token = DB::table('supervision_tokens')->where('id_supervisor',$id_user)->where('id_curso',$id_curso)->where('id_instructor',$id_instructor)->value('url_token');
       }elseif($tipo=="alumno"){ //comprobar si existe el alumno
            //$inscript = DB::table('tbl_inscripcion')->where('id',$id)->first();
            $inscript = DB::table('tbl_inscripcion as i')->select('i.id','i.id_curso','a_pre.telefono','a_pre.correo')
                ->where('i.id',$id)->where('i.status','INSCRITO')
                ->Join('alumnos_registro as a_reg', function($join){
                    $join->on('a_reg.no_control', '=', 'i.matricula');
                    $join->groupby('a_reg.no_control');
                })
                ->Join('alumnos_pre as a_pre', function($join){
                    $join->on('a_pre.id', '=', 'a_reg.id_pre');
                })->groupby('i.id','i.id_curso','a_pre.telefono','a_pre.correo')->first();
            $id_curso = $inscript->id_curso;
            $id_alumno = $inscript->id;
            $datos = "\n\n** DATOS DEL INSTRUCTOR **\nTELEFONO: ".$inscript->telefono."\n CORREO: ".$inscript->correo;
            ///comprobar si existe el token
            $token = DB::table('supervision_tokens')->where('id_supervisor',$id_user)->where('id_curso',$id_curso)->where('id_alumno',$id_alumno)->value('url_token');
       }


       if($token){
            if($this->TokenExpired($token))return "CADUCADA";
       }else{
            $currentTime = time();
            $t = new Token;
            $token = $t->url_token = hash('sha256', Str::random(60));
            $t->ttl = 0;
            $t->id_curso = $id_curso;
            $t->id_supervisor = $id_user;
            if($id_instructor) $t->id_instructor = $id_instructor;
            else $t->id_alumno = $id_alumno;
            $t->ttl = $currentTime + (1440 * 60); //24 horas 1440
            $t->save();
       }

       $url = url("{$path}{$token}".$datos);
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
