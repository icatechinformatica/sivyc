<?php
namespace App\Http\Controllers\Solicitudes;
use App\Http\Controllers\Controller;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\ValidacionServicioVb;

class vbgruposController extends Controller
{
    function __construct() {
        $this->ejercicio = date("Y");
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->id_user = Auth::user()->id;
            return $next($request);
        });
    }

    public function index(Request $request){
        list($data, $status, $message) = $this->data($request);
        $estatus = ['PENDIENTES' => 'PENDIENTES', 'AUTORIZADOS' => 'AUTORIZADOS'];
        return view('solicitudes.vbgrupos.index', compact('message','data','estatus','status'));
    }

    private function data(Request $request){
        $message = NULL;
        $clave = $request->clave;
        $data = [];
        if($request->estatus) $status = $request->estatus;
        else $status = "PENDIENTES";

        $data = DB::table('tbl_cursos')->whereYear('inicio',$this->ejercicio);
        if($status == "AUTORIZADOS") $data = $data->where('vb_dg', true);
        else  $data = $data->where('clave','0')->where('turnado','VoBo')->where('vb_dg', false);

        if($clave) $data = $data->where(DB::raw("CONCAT(nombre,curso,unidad)"),'like','%'.$clave.'%');
        $data = $data->orderby('inicio','DESC')->paginate(15);

        if(!$data) $message = "No se encontraron registros.";
        return [$data, $status, $message, $clave];
    }

    public function vistobueno(Request $request){
        if($request->id and $request->estado){
            $id = $request->id;

            if($request->estado == "true") $estado = true;
            else $estado = false;

            if (is_numeric($id)){
                $result =  DB::table('tbl_cursos')->where('id',$id)->whereNull('status_curso');
                if($estado == true)
                    $result->update(['vb_dg' => $estado, 'turnado' => 'UNIDAD']);
                else
                    $result->update(['vb_dg' => $estado, 'turnado' => 'VoBo']);
                if($result){
                    if($estado == "true") $msg = "ACTIVADO";
                    else $msg = "DESACTIVADO";
                }else $msg = "Actualizar la página con F5 y por favor, vuelver a intentar.";
            }
        }else{
            $msg = "Operación no valida.";
        }
        return $msg;
    }

    public function rechazar(Request $request){   // dd($request->motivo);
        $id_curso = $request->id_curso;
        $motivo = $request->motivo;
        if($id_curso and $motivo){
            $result = DB::table('tbl_cursos')->where('id',$id_curso)
                ->update([ 'turnado' => 'UNIDAD',
                'movimientos' => DB::raw("
                    COALESCE(movimientos, '[]'::jsonb) || jsonb_build_array(
                        jsonb_build_object(
                            'fecha', '".Carbon::now()->format('Y-m-d H:i:s')."',
                            'usuario', '".Auth::user()->name."',
                            'operacion', 'RECHAZO VISTO BUENO',
                            'motivo', '$motivo',
                            'vb_dg' => false
                        )
                    )
                ")
            ]);
            if($result) $message = "Operación Exitosa!";
        }

        return redirect()->route('solicitudes.vb.grupos')->with(['message' => $message]);
    }

    public function autodata(Request $request){
        list($data, $status, $message, $clave) = $this->data($request);

        if($data){
            $filas = $checked = $show_btninst = "";
            foreach ($data as $item){
                if($item->vb_dg == true) {$checked = 'checked'; $show_btninst = 'd-none';}
                else {$checked = ''; $show_btninst = '';}

                if(strlen($item->curso) >= 18) $curso = mb_substr($item->curso, 0, 18, 'UTF-8')."..";
                else $curso = $item->curso;

                $modal_listinst = 'seleccion_instructor("'.$item->folio_grupo.'" )';
                $modal_curso = 'ver_modal("CURSO", "'.$item->folio_grupo.'" )';
                $modal_instructor = 'ver_modal("INSTRUCTOR", "'.$item->folio_grupo.'" )';
                $modal_motivo =  'modal_motivo("'.$curso.'", "'.$item->id_curso.'" )';

                $filas .= "
                    <tr>
                        <td class='text-center'>
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' value='".$item->id."' name='activo_curso'   onchange='cambia_estado(".$item->id.",$(this))' $checked>
                            </div>
                        </td>
                        <td>
                            <a onclick='".$modal_curso."' style='color:rgb(1, 95, 84);'>
                                <b>".$item->curso."</b>
                            </a>
                        </td>
                        <td>
                            <a onclick='".$modal_listinst."' title='Seleccionar Instructor'>
                                <i class='fa fa-address-book mr-2 $show_btninst' aria-hidden='true' style='color:rgb(1, 95, 84);'></i>
                            </a>
                            <a onclick='".$modal_instructor."' style='color:rgb(1, 95, 84);'>
                                <b>".$item->nombre."</b>
                            </a>
                        </td>
                        <td>".$item->inicio."</td>
                        <td>".$item->termino."</td>
                        <td>".$item->unidad."</td>
                        <td class='text-center'>";
                        if($item->clave==0){
                            $filas .= "
                                <a onclick='".$modal_motivo."' >
                                    <i class='fas fa-window-close fa-2x fa-danger'></i>
                                </a>";
                        }else{
                            $filas .= $item->clave;
                        }
                        $filas .= "
                        </td>
                    </tr>
                ";
            }
        } else $filas = "Dato no encontrado, por favor intente de nuevo.";

        return $filas;
    }

    public function modal_datos(Request $request){
        $data = null;
        switch($request->tipo){
            case "CURSO":
                $data = $this->detalles_curso($request);
            break;
            case "INSTRUCTOR":
                $data = $this->detalles_instructor($request);
            break;
        }
        return $data;
    }

    private function detalles_curso(Request $request){
        $folio = $request->folio_grupo;
        $head = null;
        $body = "Datos no encontrado.";
        if($folio){
            $result = DB::table('tbl_cursos')->select('muni', 'hini', 'hfin', 'efisico', 'curso')->where('folio_grupo', $folio)->first();
            if($result){
                if (strlen($result->curso) > 25) $head =  mb_substr($result->curso, 0, 25, 'UTF-8') . " ...";
                else $head = $result->curso;
                $body = "
                    <ul>
                        <li> <b> Municipio: </b>".$result->muni."</li>
                        <li> <b> Horario: </b>De ".$result->hini." A ".$result->hfin."</li>
                        <li> <p> <b> Lugar: </b>".$result->efisico."</p></li>
                    </ul>
                    ";
            }
        }
        return [$head, $body];
    }


    private function detalles_instructor(Request $request){
        $folio = $request->folio_grupo;
        $head = null;
        $body = "Datos no encontrado.";
        if($folio){
            $result =  DB::table('tbl_cursos as tc')
                ->select([ 'ins.id as id_instructor',
                    DB::raw('CONCAT(ins.nombre, \' \', ins."apellidoPaterno", \' \', ins."apellidoMaterno") AS instructor'),
                    'ins.telefono',
                    'insper.grado_profesional as escolaridad',
                    'insper.carrera',
                    DB::raw("(SELECT STRING_AGG(espe.nombre, '|' ORDER BY espe.nombre)
                    FROM especialidad_instructores esin_sub
                    JOIN especialidades espe ON espe.id = esin_sub.especialidad_id
                    WHERE esin_sub.id_instructor = ins.id AND esin_sub.status = 'VALIDADO') as especialidades"),
                    DB::raw("(esin.hvalidacion::json->0->>'fecha_val')::date as fecha_ingreso"),
                ])->where('tc.folio_grupo', $folio)
                ->join('instructores as ins','ins.id','tc.id_instructor')
                ->join('especialidad_instructores as esin', function($join) {
                    $join->on('esin.id_instructor', '=', 'ins.id')
                        ->where('esin.status', '=', 'VALIDADO');
                })
                ->join('especialidades as espe', 'espe.id', '=', 'esin.especialidad_id')
                ->join('instructor_perfil as insper', 'insper.id', '=', 'esin.perfilprof_id')
                ->groupBy([
                    'ins.id',
                    'ins.nombre',
                    'ins.apellidoPaterno',
                    'ins.apellidoMaterno',
                    'ins.telefono',
                    'insper.grado_profesional',
                    'insper.carrera',
                    'esin.hvalidacion',
                ])
                ->first();

            $total_cursos = DB::table('tbl_cursos as tc')
                ->join('instructores as ins', 'ins.id', '=', 'tc.id_instructor')
                ->where('ins.id', $result->id_instructor)
                ->whereYear('tc.created_at', $this->ejercicio)
                ->count('tc.id');
            $unidadSolicita = DB::table('especialidad_instructores')->where('id_instructor', $result->id_instructor)->latest('fecha_validacion')->value('unidad_solicita');

            $consulta_pago = DB::table('tbl_cursos')
                ->join('criterio_pago', 'tbl_cursos.cp', '=', 'criterio_pago.id')
                ->select(
                    'tbl_cursos.inicio',
                    'tbl_cursos.dura',
                    'tbl_cursos.cp',
                    'tbl_cursos.id_instructor',
                    'tbl_cursos.modinstructor',
                     DB::raw("CASE tbl_cursos.ze
                        WHEN 'II' THEN criterio_pago.ze2
                        WHEN 'III' THEN criterio_pago.ze3
                        ELSE NULL
                        END as ze_valor")
                    )
                ->where('tbl_cursos.folio_grupo', '=', $folio)
                ->first();

            $json_ze = json_decode($consulta_pago->ze_valor, true);
            $monto_pago = 0;
            if (!empty($json_ze['vigencias'])) {
                foreach ($json_ze['vigencias'] as $key => $value) {
                    if (Carbon::parse($consulta_pago->inicio)->gte(Carbon::parse($value['fecha']))) {
                        $monto_pago = intval($value['monto']);
                    }
                }
            }

            if($monto_pago > 0) $monto_pago = $monto_pago * $consulta_pago->dura;

            if($result){
                if (strlen($result->instructor) > 25) $head = substr($result->instructor, 0, 25) . " ...";
                else $head = $result->instructor;
                $body = "
                    <ul>
                        <li> <b> Importe: </b> $ ". number_format($monto_pago, 2, '.', ',')."</li>
                        <li> <b> Escolaridad: </b>".$result->escolaridad."</li>
                        <li> <b> Carrera: </b>".$result->carrera."</li>
                        <li> <b> Teléfono: </b>".$result->telefono."</li>
                        <li> <b> Unidad: </b>".$unidadSolicita."</li>
                        <li> <b> Especialidades Validadas: </b> <p>".$result->especialidades."</p></li>
                        <li> <b> Cursos Impartidos: </b> ".$total_cursos."</li>
                    </ul>
                    ";
            }
        }
        return [$head, $body];
    }

    ## Made by Jose Luis
    public function modal_instructores(Request $request) {
        $folio_grupo = $request->folio_grupo;
        $agenda = DB::Table('agenda')->Where('id_curso', $folio_grupo)->get();
        $grupo = DB::table('tbl_cursos')->select('inicio', 'id_especialidad', 'termino', 'folio_grupo')->where('folio_grupo', $folio_grupo)->first();
        list($instructores, $mensaje) = $this->data_instructores($grupo, $agenda);

        return response()->json([
            'instructores' => $instructores,
            'mensaje' => $mensaje
        ]);
    }

    public function data_instructores($data, $agenda){
        try {
            $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id')
            ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($data->inicio)))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($data->inicio)))
            ->havingRaw('count(*) >= 2')
            ->groupby('i.id');

            $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id') //DB::raw('count(id_curso) as total')
            ->rightJoin('instructores','t.id_instructor','=','instructores.id')
            ->JOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->JOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->JOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            //->JOIN('especialidad_instructor_curso','especialidad_instructor_curso.id_especialidad_instructor','=','especialidad_instructores.id')
            //->WHERE('especialidad_instructor_curso.curso_id',$data->id_curso)
            ->WHERE('estado',true)
            ->WHERE('instructores.status', '=', 'VALIDADO')->where('instructores.nombre','!=','')
            ->WHERE('especialidad_instructores.especialidad_id',$data->id_especialidad)
            //->where('especialidad_instructor_curso.activo', true)
            ->WHERE('fecha_validacion','<',$data->inicio)
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$data->termino)
            ->whereNotIn('instructores.id', $internos)
            ->groupBy('t.id_instructor','instructores.id')
            ->orderBy('instructor')
            ->get();


            #### Validacion de criterios de instructor
            $servicio = (new ValidacionServicioVb());

            //Primer criterio
            $respuesta8Horas = $servicio->InstNoRebase8Horas($instructores, $agenda);
            if (count($respuesta8Horas) > 0) {

                //Segundo Criterio
                $respuesta40Horas = $servicio->InstNoRebase40HorasSem($respuesta8Horas, $agenda);
                if (count($respuesta40Horas) > 0) {

                    //Tercer criterio
                    $respuestaTraslape = $servicio->InstNoTraslapeFechaHoraConOtroCurso($respuesta40Horas, $agenda);
                    if (count($respuestaTraslape) >0 ) {

                        //Cuarto Criterio
                        $respuesta150dias = $servicio->InstValida150Dias($respuestaTraslape, $data->folio_grupo);
                        if (count($respuesta150dias) > 0 ) {

                            return [$respuesta150dias , '']; //Retornamos la respuesta

                        }else{
                            return [[], 'No se encontraron Instructores, Rebasan los 150 dias'];
                        }
                    }else{
                        return [[], 'No se encontraron Instructores, Traslapa con otros cursos'];
                    }

                }else{
                    return [[], 'No se encontraron Instructores, Rebasan las 40 Horas por Semana'];
                }

            }else{
                return [[], 'No se encontraron Instructores, Rebasan las 8 Horas Diarias'];
            }

        } catch (\Throwable $th) {
            return [[], 'Error: '.$th->getMessage()];
        }
    }

    public function guardar_instructor(Request $request) {
        //En espera de soltar el guardado de datos
        return redirect()->route('solicitudes.vb.grupos')->with('success', 'En espera de autorización, para habilitar esta función');


        $folio_grupo = $request->val_folio_grupo;
        $id_instructor = $request->sel_instructor;
        $message = '';

        //Pruebas
        // $folio_grupo = '6Y-250063'; // de este grupo tiene como instructor al id 919 ZUART PONCE JOSE EDGARDO, cambiaremos de instructor al 1189  LOPEZ PASCACIO GILBERTO ANTONIO
        // $id_instructor = 1189;

        ### Obtener los datos del curso y del instructor
        $dataCurso = DB::table('tbl_cursos')->where('folio_grupo', $folio_grupo)->first();
        $dataInstructor = $this->InstObtenerDatos($id_instructor, $dataCurso);

        if ($dataInstructor) {
            ## Realizar el guardado de datos a las tablas alumnos_registro, tbl_cursos, agenda
            $respuesta = $this->InstUpdateDatos($dataInstructor, $dataCurso);
            if ($respuesta) {
                $message = 'Instructor actualizado => '.$dataInstructor->instructor. ' | Curso => '.$dataCurso->curso;
                return redirect()->route('solicitudes.vb.grupos')->with('success', $message);
            }
        }else{
            $message = 'Error al actualizar datos del instructor';
            return redirect()->route('solicitudes.vb.grupos')->with('error', $message);
        }

    }

    ###Funcion de obtener datos del instructor
    public function InstObtenerDatos($idInstrcutor, $dataCurso){
        try {
            $id_especialidad = DB::table('cursos')->where('estado', true)->where('id', $dataCurso->id_curso)->value('id_especialidad');
            $instructor = DB::table('instructores')->select(
                'instructores.id',
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'curp',
                'rfc',
                'sexo',
                'tipo_honorario',
                'instructor_perfil.grado_profesional as escolaridad',
                'instructor_perfil.estatus as titulo',
                'especialidad_instructores.memorandum_validacion as mespecialidad',
                'especialidad_instructores.criterio_pago_id as cp',
                'tipo_identificacion',
                'folio_ine','domicilio','archivo_domicilio','archivo_ine','archivo_bancario','rfc','archivo_rfc',
                'banco','no_cuenta','interbancaria','tipo_honorario'
                )
            ->WHERE('instructores.status', '=', 'VALIDADO')
            ->where('instructores.nombre', '!=', '')
            ->where('instructores.id', $idInstrcutor)
            ->WHERE('especialidad_instructores.especialidad_id', $id_especialidad)
            ->WHERE('especialidad_instructores.activo', 'true')
            ->WHERE('fecha_validacion','<',$dataCurso->inicio)
            ->WHERE(DB::raw("(fecha_validacion + INTERVAL'1 year')::timestamp::date"),'>=',$dataCurso->termino)
            ->LEFTJOIN('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            ->LEFTJOIN('criterio_pago', 'criterio_pago.id', '=', 'especialidad_instructores.criterio_pago_id')
            ->ORDERBY('fecha_validacion','DESC')
            ->first();

            return $instructor;

        } catch (\Throwable $th) {
            $message = 'Error al obtener datos de instructores => '.$th->getMessage();
            return redirect()->route('solicitudes.vb.grupos')->with('success', $message);
        }

    }

    ## Funcion de guardado de datos del instrcutor
    public function InstUpdateDatos($dataInstructor, $dataCurso) {
        try {
            DB::beginTransaction(); //Inicia la transacción

            $cp = null;
            $curso = DB::table('cursos as c')->select('c.id','c.nombre_curso','c.horas','c.rango_criterio_pago_maximo as cp','c.costo','e.nombre as espe',
                    'a.formacion_profesional as area','c.memo_validacion as mpaqueteria','e.clave as clave_especialidad')
                    ->join('especialidades as e','e.id','c.id_especialidad') ->join('area as a','a.id','c.area')
                    ->where('c.id',$dataCurso->id_curso)->first();

            //Validar criterio de pago
            $cp = ($dataInstructor->cp > $curso->cp) ? $curso->cp : $dataInstructor->cp;

            //Validar Honorario
            $tipo_honorario = ($dataInstructor->tipo_honorario == 'ASIMILADOS A SALARIOS') ? 'ASIMILADOS A SALARIOS' : 'HONORARIOS';

            $soportes_instructor = ["domicilio"=>$dataInstructor->domicilio, "archivo_domicilio"=>$dataInstructor->archivo_domicilio,
                                    "archivo_ine"=>$dataInstructor->archivo_ine,"archivo_bancario"=>$dataInstructor->archivo_bancario,"archivo_rfc"=>$dataInstructor->archivo_rfc,
                                    'banco'=>$dataInstructor->banco,'no_cuenta'=>$dataInstructor->no_cuenta,'interbancaria'=>$dataInstructor->interbancaria,'tipo_honorario'=>$dataInstructor->tipo_honorario];

            //Guarda datos del instructor en tbl_cursos
            $result_curso = DB::table('tbl_cursos')->where('folio_grupo', $dataCurso->folio_grupo)->Update(
                [
                'id_instructor' => $dataInstructor->id,'modinstructor' => $tipo_honorario,
                'nombre' => $dataInstructor->instructor,'curp' => $dataInstructor->curp,'rfc' => $dataInstructor->rfc,
                'instructor_escolaridad' => $dataInstructor->escolaridad,'instructor_titulo' => $dataInstructor->titulo,'instructor_sexo' => $dataInstructor->sexo,
                'instructor_mespecialidad' => $dataInstructor->mespecialidad,'instructor_tipo_identificacion' => $dataInstructor->tipo_identificacion,
                'instructor_folio_identificacion' => $dataInstructor->folio_ine,'soportes_instructor'=>json_encode($soportes_instructor)
                ]);

            if (!$result_curso) {
                throw new \Exception('No se pudo actualizar los datos del instructor.');
            }

            // Guarda el id del instructor en alumnos_registro
            $result_alumnos = DB::table('alumnos_registro')->where('folio_grupo', $dataCurso->folio_grupo)->Update(
                [
                    'iduser_updated' => $this->id_user,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'id_instructor'=>$dataInstructor->id
                ]
            );

            if (!$result_alumnos) {
                throw new \Exception('No se pudo actualizar los datos del instructor.');
            }

            //Guardar id del instructor en agenda
            $result_agenda = DB::table('agenda')->where('id_curso', $dataCurso->folio_grupo)->Update(
                [
                    'id_instructor' => $dataInstructor->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'iduser_updated' => $this->id_user
                ]
            );

            if (!$result_agenda) {
                throw new \Exception('No se pudo actualizar agenda');
            }

            DB::commit(); // Todo salió bien, confirmar cambios
            return $result_agenda;

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack(); // Ocurrió un error, revertir todo
            $message = 'Error al actualizar datos del instructor => '.$th->getMessage();
            return redirect()->route('solicitudes.vb.grupos')->with('error', $message);
        }

    }

}
