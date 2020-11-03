<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use App\Models\supervision\alumno;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct() {

    }

    public function index(Request $request){

    }

    public function lista(Request $request){
       $id = $request->get("id");
       $data = DB::table('tbl_inscripcion')->select('tbl_inscripcion.id','tbl_inscripcion.alumno','token_a.id as token_alumno','token_a.ttl');
       $data = $data->leftJoin('supervision_tokens as token_a' ,function($join)use($id){
                $join->on('tbl_inscripcion.id', '=', 'token_a.id_alumno'); 
                $join->where('token_a.id_curso',$id);                
            });
       $data = $data->where('tbl_inscripcion.id_curso',$id)->get();
            
       $str = "";
       foreach($data as $item){
            if($item->token_alumno)
                if(time() > $item->ttl )
                    $str .= "<a href='#' onclick='generarURL(".$item->id.",\"alumno\");' class='list-group-item list-group-item-action bg-danger' data-toggle='modal'  data-target='#modalURL'>".$item->alumno."</a>";
                else
                    $str .= "<a href='#' onclick='generarURL(".$item->id.",\"alumno\");' class='list-group-item list-group-item-action bg-warning' data-toggle='modal'  data-target='#modalURL'>".$item->alumno."</a>";
            else
                $str .= "<a href='#' onclick='generarURL(".$item->id.",\"alumno\");' class='list-group-item list-group-item-action' data-toggle='modal'  data-target='#modalURL'>".$item->alumno."</a>";
       }
       if(!$str) $str = "NO SE ENCONTR&Oacute; REGISTROS QUE MOSTRAR";
       return $str;
    }


    public function revision($id)
    {
        
        session_start();
        $_SESSION["id"] = $id;
        $fecha = date('d/m/Y');
        $data = alumno::Filtrar('supervision_alumnos.id_tbl_cursos',$id)
        ->LEFTJOIN('tbl_inscripcion as insc','insc.id','=', 'supervision_alumnos.id_alumno')
        //->LEFTJOIN('alumnos_registro as a_reg', 'a_reg.no_control', '=', 'insc.matricula')
        ->LEFTJOIN('alumnos_registro as a_reg', function($join){
            $join->on('a_reg.no_control', '=', 'insc.matricula');            
            $join->on('a_reg.id_curso','=','supervision_alumnos.id_curso');
        })
        ->LEFTJOIN('alumnos_pre as a_pre', 'a_pre.id', '=', 'a_reg.id_pre')        
        ->PAGINATE(5, ['supervision_alumnos.id','supervision_alumnos.nombre','supervision_alumnos.apellido_paterno','supervision_alumnos.apellido_materno','supervision_alumnos.edad','supervision_alumnos.escolaridad',
            'supervision_alumnos.fecha_inscripcion','supervision_alumnos.documentos','supervision_alumnos.curso','supervision_alumnos.fecha_autorizacion',
            'supervision_alumnos.fecha_inicio','supervision_alumnos.fecha_termino','supervision_alumnos.hinicio','supervision_alumnos.hfin','supervision_alumnos.tipo','supervision_alumnos.lugar','supervision_alumnos.cuota',
            'supervision_alumnos.ok_nombre','supervision_alumnos.ok_edad','supervision_alumnos.ok_escolaridad','supervision_alumnos.ok_fecha_inscripcion','supervision_alumnos.ok_documentos',
            'supervision_alumnos.ok_curso','supervision_alumnos.ok_numero_apertura','supervision_alumnos.ok_fecha_autorizacion','supervision_alumnos.ok_modalidad',
            'supervision_alumnos.ok_fecha_inicio','supervision_alumnos.ok_fecha_termino','supervision_alumnos.ok_horario','supervision_alumnos.ok_tipo','supervision_alumnos.ok_lugar','supervision_alumnos.ok_cuota',
            'supervision_alumnos.obs_nombre','supervision_alumnos.obs_edad','supervision_alumnos.obs_escolaridad','supervision_alumnos.obs_fecha_inscripcion','supervision_alumnos.ok_documentos',
            'supervision_alumnos.obs_curso','supervision_alumnos.obs_fecha_autorizacion','supervision_alumnos.obs_fecha_inicio',
            'supervision_alumnos.obs_fecha_termino','supervision_alumnos.obs_horario','supervision_alumnos.obs_tipo','supervision_alumnos.obs_lugar','supervision_alumnos.obs_cuota','supervision_alumnos.comentarios',
            'supervision_alumnos.id_tbl_cursos','supervision_alumnos.enviado','supervision_alumnos.created_at',
            DB::raw("date_part('year',age(a_pre.fecha_nacimiento)) as sivyc_edad"),'a_pre.ultimo_grado_estudios as sivyc_escolaridad','insc.alumno','insc.costo',
            'a_pre.chk_acta_nacimiento','a_pre.chk_curp','a_pre.comprobante_domicilio','a_pre.chk_fotografia','a_pre.chk_ine',
            'a_pre.chk_pasaporte_licencia','a_pre.chk_comprobante_ultimo_grado','a_pre.chk_comprobante_calidad_migratoria']);
            
        
        $curso = DB::table('tbl_cursos')->where('id', $id)->first();
        $anio = date("Y");
        $path_dir  =  storage_path().'/app/public/supervisiones/'.$anio.'/alumnos/';
        $path_file = 'storage/supervisiones/'.$anio.'/alumnos/';

        return view('supervision.escolar.alumno', compact('data','fecha','curso','path_dir','path_file'));
    }

    public function update(Request $request)
    {
         session_start();
        $id = $_SESSION["id"];
        $idsupervision = $request->get('id_supervision');
        $boton = $request->get('boton');
        $app= alumno::find($idsupervision);
        switch($boton){
            case 'eliminar':/*
                if($app->delete())
                    ///eliminar imagenes
                    $mensaje = 'Operacion Exitosa!';
                else */
                    $mensaje = 'Operacion no se ha efectuado correctamente, por favor vuelver intentar.';

            break;
            case 'enviar':
                $fieldsOK = ['ok_nombre','ok_edad','ok_escolaridad',
                'ok_fecha_inscripcion','ok_documentos','ok_curso',
                'ok_fecha_autorizacion','ok_fecha_inicio',
                'ok_fecha_termino','ok_horario','ok_tipo',
                'ok_lugar','ok_cuota'];

                $fieldsOBS = ['obs_nombre','obs_edad',
                'obs_escolaridad','obs_fecha_inscripcion','obs_documentos',
                'obs_curso','obs_fecha_autorizacion','obs_fecha_inicio',
                'obs_fecha_termino', 'obs_horario','obs_tipo','obs_lugar',
                'obs_cuota','comentarios'];

                foreach ($fieldsOK as $i => $value) {
                    if($request->get($value)) $app->$value = $request->get($value);
                    else $app->$value = false;
                }
                foreach ($fieldsOBS as $i => $value2) {
                    $app->$value2 = strtoupper($request->get($value2));
                }


                $app->enviado = 1;
                if($app->save())$mensaje = 'Operacion Exitosa!';
                else $mensaje = 'Operacion no se ha efectuado correctamente, por favor vuelver intentar.';
            break;
        }

      return redirect('supervision/alumno/revision/'.$id)->with(['mensaje'=>$mensaje]);
    }

}
