<?php

namespace App\Http\Controllers\Catalogos\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\instructor;
use App\Models\especialidad;
use App\Models\estado_civil;
use App\Models\Banco;
use App\Models\pais;

class InstructorController extends Controller
{
    ##Inicio
    public function index(Request $request)
    {
        $busquedaInstructor = $request->get('busquedaPorInstructor');
        $tipoInstructor = $request->get('tipo_busqueda_instructor');
        $tipoStatus = $request->get('tipo_status');
        $tipoEspecialidad = $request->get('tipo_especialidad');
        $unidadUser = Auth::user()->unidad;
        $message = null;
        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();

        $data = instructor::searchinstructor($tipoInstructor, $busquedaInstructor, $tipoStatus, $tipoEspecialidad)->WHERE('instructores.id', '!=', '0')
            ->LEFTJOIN('especialidad_instructores',function($join){
                $join->on('instructores.id','=','especialidad_instructores.id_instructor');
                $join->where('especialidad_instructores.status','=','VALIDADO');
                $join->groupby('especialidad_instructores.id_instructor');
            });

            if(!Auth::user()->can('instructores.all')){   //RESTRICCION PARA UNIDADES
                $data = $data->whereIn('instructores.estado', [true])
                ->WHEREIN('instructores.status', ['EN CAPTURA','VALIDADO','BAJA','PREVALIDACION','REACTIVACION EN CAPTURA']);
            }else{
                //$data = $data->WHEREIN('estado', [true,false])
                $data = $data->WHEREIN('instructores.status', ['EN CAPTURA','VALIDADO','BAJA','PREVALIDACION','REACTIVACION EN CAPTURA','INHABILITADO']);
            }
            if($tipoInstructor=='nombre_curso'){
                $buscando = explode(' - ', $busquedaInstructor);
                if (isset($buscando[0]) && is_numeric($buscando[0])){
                    $data = $data->join('especialidad_instructor_curso','id_especialidad_instructor','especialidad_instructores.id')
                    ->where('especialidad_instructor_curso.activo','true')
                    ->where('curso_id', $buscando[0]);
                }else $message = "SELECCIONE UNA OPCIÓN DE LA LISTA DE CURSOS";

            }

            $data = $data->PAGINATE(25, ['nombre', 'curp', 'telefono', 'instructores.status', 'apellidoPaterno', 'apellidoMaterno',
                'numero_control', 'instructores.id', 'archivo_alta','curso_extra','estado','activo_curso', DB::raw('min(fecha_validacion) as fecha_validacion'),
                DB::raw("(min(fecha_validacion) + CAST('11 month' AS INTERVAL)) as por_vencer"),
                DB::raw("(min(fecha_validacion) + CAST('1 year' AS INTERVAL) - CAST('15 day' AS INTERVAL) ) as vigencia"),
                DB::raw('(SELECT hvalidacion FROM especialidad_instructores
                  WHERE especialidad_instructores.id_instructor = instructores.id
                  AND especialidad_instructores.status = \'VALIDADO\'
                  ORDER BY especialidad_instructores.updated_at DESC LIMIT 1) as hvalidacion')
            ]);
            $data->appends($request->only(['unidadbusquedaPorInstructor', 'tipo_busqueda_instructor']));

        $especialidades = especialidad::SELECT('id','nombre')->WHERE('activo','true')->ORDERBY('nombre','ASC')->GET();
        $old = $request->all(); //dd($old['tipo_busqueda_instructor']);
        if(!$old)  $old['tipo_busqueda_instructor'] = null;
        $tipo_busqueda = ['nombre_curso'=>'CURSO','clave_instructor'=>'CLAVE','nombre_instructor'=>'NOMBRE','curp'=>'CURP','telefono_instructor'=>'TELÉFONO','estatus_instructor'=>'ESTATUS','especialidad'=>'ESPECIALIDAD'];
        $busquedaPorInstructor = $request->busquedaPorInstructor;

        return view('catalogos.instructor.busqueda_instructores', compact('data', 'especialidades','message','old','tipo_busqueda','busquedaPorInstructor'));
    }

    public function vista_crear_instructor() {
        $lista_civil = estado_civil::WHERE('id', '!=', '0')->ORDERBY('nombre', 'ASC')->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->ORDERBY('nombre','ASC')->GET();
        $bancos = Banco::all();
        $lista_regimen = DB::Table('cat_conceptos')->Where('tipo', 'REGIMEN')->Where('activo', TRUE)->GET();
        $paises = pais::all();
        return view('catalogos.instructor.frm_general', compact('lista_civil','estados','bancos','lista_regimen','paises'));
    }
}
