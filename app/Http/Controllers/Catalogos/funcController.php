<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Funcionarios;
use App\Models\ModelPat\Organismos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class funcController extends Controller
{
    public function index(Request $request) {
        $busqueda = '';
        $action = $request->input('accion');
        // dd($action);
        if ($action == 'limpiar') {
            return redirect()->route('catalogos.funcionarios.inicio');
        }

        if(!empty($request->input('busqueda'))){
            $busqueda = $request->input('busqueda');
        }
        $data_func = Funcionarios::Busqueda($busqueda)
        ->select('tbl_funcionarios.*')
        ->orderBy('id', 'desc')
        ->paginate(10, ['tbl_funcionarios.*']);

        //Generar matricula
        // $matricula = $this->genera_matricula('MOMS680121MPLSLF06', '07EIC0002B');
        // dd($matricula);

        //Obtener los organismos
        $list_org = Organismos::select('id', 'nombre')->orderBy('id', 'asc')->pluck('nombre', 'id')->toArray();
        $list_cargos = DB::table('tbl_cargos')->select('id', 'cargo')->orderBy('id', 'asc')->pluck('cargo', 'id')->toArray();

        return view('catalogos.frm_funcionarios', compact('data_func','busqueda','list_org','list_cargos'));
    }

    public function genera_matricula($curp, $cct){
        $matricula_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('curp',$curp)->value('no_control');
        $matricula = NULL;
        if(!$matricula_sice){
            $matricula_pre = DB::table('alumnos_pre')->where('curp',$curp)->value('matricula');
            if(!$matricula_pre){
                $anio = date('y');
                $clave = $anio.substr($cct,0,2).substr($cct,5,9);
                $max_sice = DB::table('registro_alumnos_sice')->where('eliminado',false)->where('no_control','like',$clave.'%')->max(DB::raw('no_control'));
                $max_pre = DB::table('alumnos_pre')->where('matricula','like',$clave.'%')->max('matricula');

                if($max_sice > $max_pre) $maX = $max_sice;
                elseif($max_sice < $max_pre) $max = $max_pre;
                else $max = '0';

                $max =  str_pad(intval(substr($max,9,13))+1, 4, "0", STR_PAD_LEFT);
                $matricula = $clave.$max;
            }else $matricula = $matricula_pre;
        }else{
            $matricula = $matricula_sice;
            DB::table('registro_alumnos_sice')->where('curp',$curp)->update(['eliminado'=>true]);
        }
        return $matricula;
    }

    public function guardar(Request $request) {
        // dd($request->all());
        try {
            $id_registro = $request->input('id_registro');
            if (!empty($id_registro)) {
                $result = DB::table('tbl_funcionarios')
                    ->where('id', $id_registro)
                    ->update([
                        'id_org' => $request->input('org'),
                        'titular' => $request->input('titular') == 'titular_si' ? true : false,
                        'nombre' => $request->input('nombre'),
                        'cargo' => $request->input('cargo'),
                        'adscripcion' => $request->input('adscripcion'),
                        'direccion' => $request->input('direc'),
                        'telefono' => $request->input('telefono'),
                        'correo' => $request->input('email'),
                        'correo_institucional' => $request->input('email2'),
                        'curp' => $request->input('curp'),
                        'titulo' => $request->input('titulo'),
                        'activo' => $request->input('status') == 'activo' ? 'true' : 'false',
                        'id_cargo' => $request->input('sel_cargo'),
                        'iduser_created' => Auth::user()->id,
                        'created_at' => date('Y-m-d')
                    ]);
            } else {
                // Si el id_registro no existe, realiza un insert
                $result = DB::table('tbl_funcionarios')->insert([
                    'id_org' => $request->input('org'),
                    'titular' => $request->input('titular') == 'titular_si' ? true : false,
                    'nombre' => $request->input('nombre'),
                    'cargo' => $request->input('cargo'),
                    'adscripcion' => $request->input('adscripcion'),
                    'direccion' => $request->input('direc'),
                    'telefono' => $request->input('telefono'),
                    'correo' => $request->input('email'),
                    'correo_institucional' => $request->input('email2'),
                    'curp' => $request->input('curp'),
                    'titulo' => $request->input('titulo'),
                    'activo' => $request->input('status') == 'activo' ? 'true' : 'false',
                    'id_cargo' => $request->input('sel_cargo'),
                    'iduser_created' => Auth::user()->id,
                    'created_at' => date('Y-m-d')
                ]);
            }
        } catch (\Throwable $th) {
            return redirect()->route('catalogos.funcionarios.inicio')->with('message', 'Error: ' . $th->getMessage());
        }

        if ($result == 1 && !empty($id_registro)) $message = 'Datos actualizados con exito!!';
        else if ($result == 1 && empty($id_registro)) $message = 'Datos registrados con exito!!';
        else $message = 'Error en el guardado.';

        return redirect()->route('catalogos.funcionarios.inicio')->with('message', $message);

    }

    public function obtener_datos(Request $request){
        $id_registro = $request->input('id_registro');

        if(!empty($id_registro)){
            ##Realizar la busqueda y enviarla por json a la vista
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'El id de registro es:  '.$id_registro
        ]);
    }

    //Nuevo modulo
    function obtener_datos_modal(Request $request) {
        $tipo_valor = $request->input('tipo_valor');
        $valor = $request->input('valor');
        $ejercicio = $request->input('ejercicio');
        if (!empty($tipo_valor) && !empty($valor)){
            if ($tipo_valor == 'info_curso') {
                try {
                    $consulta = DB::table('tbl_cursos')->select('efisico', 'hini', 'hfin', 'muni')->where('folio_grupo', '=', $valor)->first();
                    return response()->json([
                        'status' => 200,
                        'mensaje' => 'Operación exitosa!',
                        'consulta' => $consulta,
                        'tipoValor' => $tipo_valor
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 500,
                        'mensaje' => 'Ocurrió un error: '. $th->getMessage()
                    ]);
                }

            }else if($tipo_valor == 'info_instructor'){

                try {
                    //Obtener datos generales para instructor y curso
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
                    ->where('tbl_cursos.folio_grupo', '=', $valor)
                    ->first();

                    $consulta_pago_prueba = DB::table('tbl_cursos')
        ->join('criterio_pago', 'tbl_cursos.cp', '=', 'criterio_pago.id')
        ->select(
            'tbl_cursos.inicio',
            'tbl_cursos.dura',
            'tbl_cursos.cp',
            'tbl_cursos.id_instructor',
            'tbl_cursos.modinstructor',
            DB::raw("
                CASE
                    WHEN tbl_cursos.ze = 'II' THEN
                        (
                            SELECT (vigencia->>'monto')::numeric
                            FROM jsonb_array_elements(criterio_pago.ze2->'vigencias') AS vigencia
                            WHERE (vigencia->>'fecha')::date <= tbl_cursos.inicio
                            ORDER BY (vigencia->>'fecha')::date DESC
                            LIMIT 1
                        ) * tbl_cursos.dura
                    WHEN tbl_cursos.ze = 'III' THEN
                        (
                            SELECT (vigencia->>'monto')::numeric
                            FROM jsonb_array_elements(criterio_pago.ze3->'vigencias') AS vigencia
                            WHERE (vigencia->>'fecha')::date <= tbl_cursos.inicio
                            ORDER BY (vigencia->>'fecha')::date DESC
                            LIMIT 1
                        ) * tbl_cursos.dura
                    ELSE NULL
                END as monto_pago
            ")
        )
        ->where('tbl_cursos.folio_grupo', '=', $valor)
        ->first();



                    $consulta = DB::table('instructores as ins')
                    ->select([
                        DB::raw('CONCAT(ins.nombre, \' \', ins."apellidoPaterno", \' \', ins."apellidoMaterno") AS nombre_completo'),
                        'ins.telefono',
                        'insper.grado_profesional as nivelAcad',
                        'insper.carrera',
                        DB::raw("(SELECT STRING_AGG(espe.nombre, '|' ORDER BY espe.nombre)
                        FROM especialidad_instructores esin_sub
                        JOIN especialidades espe ON espe.id = esin_sub.especialidad_id
                        WHERE esin_sub.id_instructor = ins.id AND esin_sub.status = 'VALIDADO') as especialidades"),
                        DB::raw("(esin.hvalidacion::json->0->>'fecha_val')::date as fecha_ingreso"),
                    ])
                    ->join('especialidad_instructores as esin', function($join) {
                        $join->on('esin.id_instructor', '=', 'ins.id')
                            ->where('esin.status', '=', 'VALIDADO');
                    })
                    ->join('especialidades as espe', 'espe.id', '=', 'esin.especialidad_id')
                    ->join('instructor_perfil as insper', 'insper.id', '=', 'esin.perfilprof_id')
                    ->where('ins.id', $consulta_pago->id_instructor)
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

                    $tcursosIns = DB::table('tbl_cursos as tc')
                    ->join('instructores as ins', 'ins.id', '=', 'tc.id_instructor')
                    ->where('ins.id', $consulta_pago->id_instructor)
                    ->whereYear('tc.created_at', $ejercicio)
                    ->count('tc.id');

                    $unidadSolicita = DB::table('especialidad_instructores')->where('id_instructor', $consulta_pago->id_instructor)->latest('fecha_validacion')->value('unidad_solicita');

                    //Obtener criterio de pago
                    $json_ze = json_decode($consulta_pago->ze_valor, true);
                    $monto_pago = 0;
                    if (!empty($json_ze['vigencias'])) {
                        foreach ($json_ze['vigencias'] as $key => $value) {
                            if (Carbon::parse($consulta_pago->inicio)->gte(Carbon::parse($value['fecha']))) {
                                $monto_pago = intval($value['monto']);
                            }
                        }
                    }
                    if($monto_pago != 0){
                        $monto_pago = $monto_pago * $consulta_pago->dura;
                    }

                    return response()->json([
                        'status' => 200,
                        'mensaje' => 'Operación exitosa!',
                        'consulta' => $consulta,
                        'tcursosIns' => $tcursosIns,
                        'tipoValor' => $tipo_valor,
                        'monto_pago' => $monto_pago,
                        'unidad_solicita' => $unidadSolicita,
                        'prueba' => $consulta_pago_prueba
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 500,
                        'mensaje' => 'Ocurrió un error: '. $th->getMessage()
                    ]);
                }

            }
        }
    }

    function showContrato() {
        // $plantilla = DB::table('eplantillas')->where('id', 1)->value('cuerpo');
        //Informacion que vendra de la base de datos.
        $head = "<!DOCTYPE html>
                <html>
                        <head>
                            <style>
                                .header { color: blue; }
                            </style>
                        </head>";


        $body = "<body>
                        <p>Nombre: @nombre </p>
                        <p>Apellido: @apellido </p>
                        <p>Edad: @edad</p>
                        <p>email: josluis@gmail.com</p>
                        <div class=\"page-number\"><small class=\"link\">@sello_digital</small></div>
                    </body>";


        $footer = "<footer>
                        <b>Tuxtla, 21/04/2025</b>
                    </footer>
                </html>";

        $hmtlFinal = $head. $body. $footer;


        //Prueba de inyeccion html
        $uuid = 'prueba uuid'; $cadena_sello = 'prueba de cadena de sellado'; $fecha_sello = '22/04/2025';

        $selloDigital = "Sello Digital: | GUID: $uuid | Sello: $cadena_sello | Fecha: $fecha_sello<br>
                Este documento ha sido Firmado Electrónicamente, teniendo el mismo valor que la firma autógrafa
                de acuerdo a los Artículos 1, 3, 8 y 11 de la Ley de Firma Electrónica Avanzada del Estado de Chiapas";






        //Datos para enviar al hmtl
        $datos = [
            'nombre' => 'José Luis',
            'apellido' => 'Moreno',
            'edad' => '28',
            'sello_digital' => $selloDigital
        ];


        // Reemplazar los placeholders en el HTML
        foreach ($datos as $key => $value) {
            $hmtlFinal = str_replace('@' . $key, $value, $hmtlFinal);
        }

        // Puedes retornar el HTML directamente o pasarlo a una vista
        // return response($hmtlFinal)->header('Content-Type', 'text/html');

        // dd($hmtlFinal);

        $pdf = PDF::loadView('catalogos.efirmacontrato', compact('hmtlFinal'));
        $pdf->setPaper('LETTER', 'Portrait');
        return $pdf->stream("Contrato.pdf");


    }


}
