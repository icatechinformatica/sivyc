<?php

namespace App\Http\Controllers\supervisionController;

use App\Http\Controllers\Controller;
use App\Models\supervision\instructor;
//use App\Models\tbl_curso;
//use App\Models\instructor;
use App\Models\curso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
   function __construct() {

    }

    public function index(Request $request){

    }

    public function revision($id)
    {
        session_start();
        $_SESSION["id"] = $id;
        $fecha = date('d/m/Y');
        $data = instructor::Filtrar('id_tbl_cursos',$id)
        ->PAGINATE(5, ['id', 'id_instructor','nombre','apellido_paterno','apellido_materno',
    'fecha_padron','fecha_contrato','cct','nombre_curso','fecha_autorizacion','horas_curso','inicio_curso',
    'termino_curso','modalidad_curso','hini_curso','hfin_curso','tipo_curso','total_mujeres','total_hombres',
    'lugar_curso','monto_honorarios',
    'lugar_curso','id_tbl_cursos', 'ok_nombre','obs_nombre','ok_fecha_contrato','obs_fecha_contrato','ok_fecha_padron',
    'obs_fecha_padron','ok_honorarios','obs_honorarios','ok_curso','obs_curso','ok_modalidad',
    'obs_modalidad','ok_horario','obs_horario','ok_horas_diarias','obs_horas_diarias','ok_horas_curso',
    'obs_horas_curso','ok_fecha_inicio','obs_fecha_inicio','ok_fecha_termino','obs_fecha_termino',
    'ok_mujeres','obs_mujeres','ok_hombres','obs_hombres','ok_tipo','obs_tipo','ok_lugar','obs_lugar',
    'ok_numero_apertura','obs_numero_apertura','ok_fecha_autorizacion','obs_fecha_autorizacion',
    'comentarios','enviado','created_at']);

        $curso = DB::table('tbl_cursos as c')
        ->LEFTJOIN('folios as f', 'f.id_cursos', '=', 'c.id')
        ->LEFTJOIN('contratos as co', 'f.id_folios', '=', 'co.id_folios')
        ->where('id', $id)->first();

        $anio = date("Y");
        $path_dir  =  storage_path().'/app/public/supervisiones/'.$anio.'/instructores/';
        $path_file = 'storage/supervisiones/'.$anio.'/instructores/';
        return view('supervision.escolar.instructor', compact('data','fecha','curso','path_dir','path_file'));
    }

    public function update(Request $request)
    {
        //$user = Auth::user();
        //$id_user = $user->id;
        session_start();
        $id = $_SESSION["id"];
        $idsupervision = $request->get('id_supervision');
        $boton = $request->get('boton');
        $app= instructor::find($idsupervision);
        switch($boton){
            case 'eliminar':/*
                if($app->delete())
                    ///eliminar imagenes
                    $mensaje = 'Operacion Exitosa!';
                else */
                    $mensaje = 'Operacion no se ha efectuado correctamente, por favor vuelver intentar.';
            break;
            case 'enviar':
                $fieldsOK = ['ok_nombre','ok_fecha_contrato','ok_fecha_padron',
                'ok_honorarios','ok_curso','ok_modalidad',
                'ok_horario','ok_horas_diarias','ok_horas_curso',
                'ok_fecha_inicio','ok_fecha_termino',
                'ok_mujeres','ok_hombres','ok_tipo','ok_lugar',
                'ok_fecha_autorizacion'];

                $fieldsOBS = ['obs_nombre','obs_fecha_contrato',
                'obs_fecha_padron','obs_honorarios','obs_curso',
                'obs_modalidad','obs_horario','obs_horas_diarias',
                'obs_horas_curso','obs_fecha_inicio','obs_fecha_termino',
                'obs_mujeres','obs_hombres','obs_tipo','obs_lugar',
                'obs_fecha_autorizacion','comentarios'];

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

      return redirect('supervision/instructor/revision/'.$id)->with(['mensaje'=>$mensaje]);
    }

}
