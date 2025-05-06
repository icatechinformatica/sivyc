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

class vbgruposController extends Controller
{
    function __construct() {
        $this->ejercicio = date("Y");
    }

    public function index(Request $request){
        list($data, $status, $message) = $this->data($request);
        $estatus = ['PENDIENTES' => 'PENDIENTES', 'AUTORIZADOS' => 'AUTORIZADOS'];
        return view('solicitudes.vbgrupos.index', compact('message','data','estatus','status'));
    }

    private function data(Request $request){
        $data = $message = NULL;
        $clave = $request->clave;
        if($request->estatus) $status = $request->estatus;
        else $status = "PENDIENTES";

        $data = DB::table('tbl_cursos')->where('clave','0')->whereYear('inicio',$this->ejercicio);
        if($status == "PENDIENTES") $data = $data->where('vb_dg', false);
        elseif($status == "AUTORIZADOS") $data = $data->where('vb_dg', true);

        if($clave) $data = $data->where(DB::raw("CONCAT(nombre,curso,unidad)"),'like','%'.$clave.'%');
        //$data = $data->first();
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
                $result =  DB::table('tbl_cursos')->where('id',$id)->update(['vb_dg' => $estado]);
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

    public function autodata(Request $request){
        list($data, $status, $message, $clave) = $this->data($request);
        if($data){
            $filas = $checked = "";
            foreach ($data as $item){
                if($item->vb_dg==true) $checked = 'checked';
                else $checked = '';

                $modal_curso = 'ver_modal("CURSO", "'.$item->folio_grupo.'" )';
                $modal_instructor = 'ver_modal("INSTRUCTOR", "'.$item->folio_grupo.'" )';

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
                            <a onclick='".$modal_instructor."' style='color:rgb(1, 95, 84);'>
                                <b>".$item->nombre."</b>
                            </a>
                        </td>
                        <td>".$item->inicio."</td>
                        <td>".$item->termino."</td>
                        <td>".$item->unidad."</td>
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
                if (strlen($result->curso) > 25) $head = substr($result->curso, 0, 25) . " ...";
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

    public function modal_instructores(Request $request) {
        $folio_grupo = $request->folio_grupo;
        $grupo = DB::table('tbl_cursos')->select('inicio', 'id_especialidad', 'termino')->where('folio_grupo', $folio_grupo)->first();
        $instructores = $this->data_instructores($grupo);

        return response()->json($instructores);

    }

    public function data_instructores($data){
        $internos = DB::table('instructores as i')->select('i.id')->join('tbl_cursos as c','c.id_instructor','i.id')
        ->where('i.tipo_instructor', 'INTERNO')->where('curso_extra',false)
        ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($data->inicio)))
        ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($data->inicio)))
        ->havingRaw('count(*) >= 2')
        ->groupby('i.id');

        $instructores = DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
        ->select(DB::raw('CONCAT("apellidoPaterno", '."' '".' ,"apellidoMaterno",'."' '".',instructores.nombre) as instructor'),'instructores.id', DB::raw('count(id_curso) as total'))
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
        return $instructores;
    }

    public function guardar_instructor(Request $request) {
        $folio_grupo = $request->val_folio_grupo;
        $id_instructor = $request->sel_instructor;
        $message = '';

        // $datos_instruc = DB::table('instructores')
        // ->select('id','rfc','curp', 'tipo_honorario', DB::raw('CONCAT("apellidoPaterno", \' \', "apellidoMaterno", \' \', "nombre") as nombre_completo'))
        // ->where('id', $id_instructor)->first();

        // $curso = DB::table('tbl_cursos')->where('folio_grupo', $folio_grupo)->value('curso');

        // try {
        //     $filas_update = DB::table('tbl_cursos')
        //     ->where('folio_grupo', $folio_grupo)
        //     ->update([
        //         'nombre' => $datos_instruc->nombre_completo,
        //         'curp' => $datos_instruc->curp,
        //         'rfc' => $datos_instruc->rfc,
        //         'id_instructor' => $datos_instruc->id,
        //     ]);
        // } catch (\Throwable $th) {
        //     return redirect()->route('solicitudes.vb.grupos')->with('success', $th->getMessage());
        // }

        // if ($filas_update > 0) {
        //     $message = 'Instructor actualizado => '.$datos_instruc->nombre_completo. ' | Curso => '.$curso;
        //     return redirect()->route('solicitudes.vb.grupos')->with('success', $message);
        // }else{
        //     $message = 'Error al actualizar el instructor => '.$datos_instruc->nombre_completo.' |  Curso => '.$curso;
        //     return redirect()->route('solicitudes.vb.grupos')->with('success', $message);
        // }


        $message = 'Funcionalidad pendiente';
            return redirect()->route('solicitudes.vb.grupos')->with('success', $message);


    }
}
