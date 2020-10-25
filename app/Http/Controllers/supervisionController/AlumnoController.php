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
       $data = DB::table('tbl_inscripcion')->select('tbl_inscripcion.id','tbl_inscripcion.alumno','token_a.id as token_alumno');
       $data = $data->leftJoin('supervision_tokens as token_a' ,function($join)use($id){
                $join->on('tbl_inscripcion.id', '=', 'token_a.id_alumno'); 
                $join->where('token_a.id_curso',$id);                
            });
       $data = $data->where('tbl_inscripcion.id_curso',$id)->get();
            
       $str = "";
       foreach($data as $item){
            if($item->token_alumno)
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
        $data = alumno::Filtrar('id_tbl_cursos',$id)
        ->PAGINATE(5, ['id','nombre','apellido_paterno','apellido_materno','edad','escolaridad',
    'fecha_inscripcion','documentos','curso','numero_apertura','fecha_autorizacion','modalidad',
    'fecha_inicio','fecha_termino','hinicio','hfin','tipo','lugar','cuota',
    'ok_nombre','ok_edad','ok_escolaridad','ok_fecha_inscripcion','ok_documentos',
    'ok_curso','ok_numero_apertura','ok_fecha_autorizacion','ok_modalidad',
    'ok_fecha_inicio','ok_fecha_termino','ok_horario','ok_tipo','ok_lugar','ok_cuota',
    'obs_nombre','obs_edad','obs_escolaridad','obs_fecha_inscripcion','ok_documentos',
    'obs_curso','obs_numero_apertura','obs_fecha_autorizacion','obs_modalidad','obs_fecha_inicio',
    'obs_fecha_termino','obs_horario','obs_tipo','obs_lugar','obs_cuota','comentarios',
    'id_tbl_cursos','enviado','created_at']);
        $curso = DB::table('tbl_cursos')->where('id', $id)->first();

        //alumnos_pre.id=alumnos_registros.id_pre y alumnos_registro.no_control=tbl_inscripciones.matricula
        //$inscripcion = DB::table('tbl_inscripcion as i')->first();


        $inscripcion = DB::table('tbl_inscripcion as insc')->select(
            DB::raw("date_part('year',age(a_pre.fecha_nacimiento)) as edad"),'a_pre.ultimo_grado_estudios','insc.alumno','insc.costo',
            'a_pre.chk_acta_nacimiento','a_pre.chk_curp','a_pre.comprobante_domicilio','a_pre.chk_fotografia','a_pre.chk_ine',
            'a_pre.chk_pasaporte_licencia','a_pre.chk_comprobante_ultimo_grado','a_pre.chk_comprobante_calidad_migratoria'
            )
            ->leftjoin('alumnos_registro as a_reg', 'a_reg.no_control', '=', 'insc.matricula')
            ->leftjoin('alumnos_pre as a_pre', 'a_pre.id', '=', 'a_reg.id_pre')
            ->where('insc.id_curso', $id)
            ->groupby('insc.id')->groupby('a_pre.fecha_nacimiento')->groupby('a_pre.ultimo_grado_estudios')
            ->groupby('insc.alumno')->groupby('a_pre.chk_acta_nacimiento')->groupby('a_pre.chk_curp')->groupby('a_pre.comprobante_domicilio')->groupby('a_pre.chk_fotografia')
            ->groupby('a_pre.chk_ine')->groupby('a_pre.chk_pasaporte_licencia')->groupby('a_pre.chk_comprobante_ultimo_grado')
            ->groupby('a_pre.chk_comprobante_calidad_migratoria')->groupby('insc.costo')
            ->first();

        $anio = date("Y");
        $path_dir  =  storage_path().'/app/public/supervisiones/'.$anio.'/alumnos/';
        $path_file = 'storage/supervisiones/'.$anio.'/alumnos/';

        return view('supervision.escolar.alumno', compact('data','fecha','curso','inscripcion','path_dir','path_file'));
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
                'ok_numero_apertura','ok_fecha_autorizacion','ok_modalidad',
                'ok_fecha_inicio','ok_fecha_termino','ok_horario','ok_tipo',
                'ok_lugar','ok_cuota'];

                $fieldsOBS = ['obs_nombre','obs_edad',
                'obs_escolaridad','obs_fecha_inscripcion','obs_documentos',
                'obs_curso','obs_numero_apertura','obs_fecha_autorizacion',
                'obs_modalidad','obs_fecha_inicio','obs_fecha_termino',
                'obs_horario','obs_tipo','obs_lugar','obs_cuota','comentarios'];

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
