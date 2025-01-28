<?php

namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\pre_instructor;
use App\Models\cursoVALIDADO;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\InstructorPerfil;
use App\Models\tbl_unidades;
use App\Models\especialidad;
use App\Models\estado_civil;
use App\Models\status;
use App\Models\especialidad_instructor;
use App\Models\criterio_pago;
use App\Models\instructor_history;
use App\Models\Inscripcion;
use App\Models\localidad;
use App\Models\Calificacion;
use App\Models\tbl_curso;
use App\Models\Banco;
use App\Models\pago;
use App\Models\pais;
use App\Models\contratos;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport;
use ZipArchive;
use PDF;
use App\User;

class InstructorController extends Controller
{
    public function prueba()
    {
        $data = User::Select('id')->Get();
        foreach($data as $item)
        {
            $user = User::find($item->id);
            if(str_contains($user->password,'BAJA') || str_contains($user->email,'BAJA')) {
                $user->activo = FALSE;
            } else {
                $user->activo = TRUE;
            }

            $user->save();
        }
        dd('complete');

    }

    private function honorarios($total)
    {

        return $total;
    }

    public function index(Request $request)
    {
        $busquedaInstructor = $request->get('busquedaPorInstructor');
        $tipoInstructor = $request->get('tipo_busqueda_instructor');
        $tipoStatus = $request->get('tipo_status');
        $tipoEspecialidad = $request->get('tipo_especialidad');
        $unidadUser = Auth::user()->unidad;

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
            })
            //->JOIN('especialidad_instructores','instructores.id','especialidad_instructores.id_instructor')
            //->WHERE('especialidad_instructores','especialidad_instructores.status','VALIDADO')
            ->WHEREIN('estado', [true,false])
            ->WHEREIN('instructores.status', ['EN CAPTURA','VALIDADO','BAJA','PREVALIDACION','REACTIVACION EN CAPTURA'])
            ->PAGINATE(25, ['nombre', 'curp', 'telefono', 'instructores.status', 'apellidoPaterno', 'apellidoMaterno',
                'numero_control', 'instructores.id', 'archivo_alta','curso_extra','estado', DB::raw('min(fecha_validacion) as fecha_validacion'),
                DB::raw("(min(fecha_validacion) + CAST('11 month' AS INTERVAL)) as por_vencer"),
                DB::raw("(min(fecha_validacion) + CAST('1 year' AS INTERVAL) - CAST('15 day' AS INTERVAL) ) as vigencia"),
                DB::raw('(SELECT hvalidacion FROM especialidad_instructores
                  WHERE especialidad_instructores.id_instructor = instructores.id
                  AND especialidad_instructores.status = \'VALIDADO\'
                  ORDER BY especialidad_instructores.updated_at DESC LIMIT 1) as hvalidacion')
            ]);

        $especialidades = especialidad::SELECT('id','nombre')->WHERE('activo','true')->ORDERBY('nombre','ASC')->GET();
        return view('layouts.pages.initinstructor', compact('data', 'especialidades'));
    }

    public function prevalidar_index(Request $request)
    {
        // dd($request);
        //CONFIGURACION INICIAL
        $daesp = $valor = $message = $data = $id_list = $seluni = $arch_sol = $especialidades = $perfiles = $databuzon = $buzonhistory = $regimen_actual = NULL;
        $chk_mod_espec = FALSE;
        $userid = Auth::user()->id;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $rol = DB::TABLE('role_user')->WHEREIN('role_id', ['3','1', '39'])->WHERE('user_id', '=', $userid)->FIRST();
        $unidades = DB::TABLE('tbl_unidades')->WHERE('cct', 'LIKE', '%07EI%')
                    ->SELECT('unidad')
                    ->ORDERBY('unidad', 'ASC')
                    ->GET();
        $especialidadeslist = DB::TABLE('especialidades')->SELECT('id','nombre')->GET();
        $critpag = DB::TABLE('criterio_pago')->GET();
        $nrevisiones = DB::TABLE('instructor')->SELECT('nrevision')
                    // ->JOIN('instructores', 'instructores.id', '=', 'especialidad_instructores.id_instructor')
                    // ->WHERE('unidad_solicita', '=', $request->seluni)
                    // ->WHERE('especialidad_instructores.status', '=', 'PREVALIDACION')
                    ->WHERE('nrevision', '!=', NULL)
                    ->WHERE('registro_activo', TRUE)
                    ->GROUPBY('nrevision')
                    ->ORDERBY('nrevision', 'ASC');
                    // ->GET();

        if(isset($request->valor)) //ANALIZA SI FILTRARON
        {
            if(isset($rol)) //ANALIZA SI ROL ESTA ASIGNADO
            {
                if($rol->role_id == '1') // OPCION PARA ADMIN
                {
                    $status = ['EN CAPTURA','RETORNO','PREVALIDACION','EN FIRMA','REVALIDACION EN CAPTURA','REVALIDACION EN PREVALIDACION','REVALIDACION RETORNADA','REVALIDACION EN FIRMA'];
                    $turnado = ['DTA','UNIDAD'];
                }
                else // OPCION PARA DTA
                {
                    $status = ['PREVALIDACION','EN FIRMA','REVALIDACION EN PREVALIDACION','REVALIDACION EN FIRMA','BAJA EN FIRMA','REACTIVACION EN REVALIDACION','REACTIVACION EN FIRMA','BAJA EN PREVALIDACION'];
                    $turnado = ['DTA'];

                }
                $uni = $request->seluni;
            }
            else // OPCION PARA UNIDAD CUANDO ROL NO ESTA ASIGNADO
            {
                $status = ['EN CAPTURA','REACTIVACION EN CAPTURA','RETORNO','EN FIRMA','REVALIDACION EN CAPTURA', 'REVALIDACION EN FIRMA','BAJA EN PREVALIDACION', 'BAJA EN FIRMA','REACTIVACION EN FIRMA'];
                $turnado = ['UNIDAD'];
                $uni = $userunidad->ubicacion;
            }

            $nrevisiones = $nrevisiones->WhereJsonContains('data_especialidad', [['unidad_solicita' => $uni]])
                            ->WHEREIN('status', $status)
                            ->WHEREIN('turnado', $turnado)
                            ->GET();

            $data = pre_instructor::WHERE('nrevision', '=', $request->valor)->FIRST();
            $regimen_actual = instructor::Where('id', $data->id)->Value('tipo_honorario');


            if(isset($data))
            {
                $perfiles = $this->make_collection($data->data_perfil);
                $especialidades = $this->make_collection($data->data_especialidad);
                foreach($especialidades as $moist)
                {
                    if(($moist->status != 'VALIDADO' && $moist->status != 'BAJA') || $regimen_actual != $data->tipo_honorario)
                    {
                        $chk_mod_espec = TRUE;
                    }
                }
            }

            if ($chk_mod_espec == FALSE || $data->statusins == 'BAJA EN FIRMA' || $data->statusins == 'REACTIVACION EN FIRMA') // si data no trae informacion es porque perfilprof o datos basicos se ha modificado
            {

                if($chk_mod_espec == FALSE)
                {
                    array_push($status, 'BAJA EN PREVALIDACION', 'REACTIVACION EN PREVALIDACION');
                    $unidad_solicita = DB::TABLE('tbl_unidades')
                            ->SELECT('ubicacion')
                            ->JOIN('users', 'users.unidad', '=', 'tbl_unidades.id')
                            ->WHERE('users.id', '=', $data->lastUserId)
                            ->FIRST();
                    $data->unidad_solicita = $unidad_solicita->ubicacion;
                    $data->onlyins = TRUE;
                }

            }

            if(isset($data->id))
            {
                foreach($especialidades as $boromir)
                {
                    if(isset($boromir->hvalidacion) && ($boromir->status != 'VALIDADO' && $boromir->status != 'INACTIVO' && $boromir->status != 'BAJA') || ($regimen_actual != $data->tipo_honorario))
                    {
                        if(($data->status == 'BAJA EN FIRMA' || $data->status == 'EN FIRMA') && $data->turnado == 'DTA'  && $boromir->status != 'INACTIVO' && $boromir->status != 'BAJA'){

                            $arch_sol = end($boromir->hvalidacion)['arch_sol'];printf($boromir->status . ' /// ');
                        }
                    }
                }
            }
            else
            {
                if($data[0]->statusins == 'BAJA EN FIRMA' || $data[0]->statusins == 'REACTIVACION EN FIRMA')
                {
                    $hvalidacion = especialidad_instructor::SELECT('hvalidacion','memorandum_solicitud')
                                                            ->WHERE('id_instructor', '=', $data[0]->idins)
                                                            ->FIRST();

                    foreach($hvalidacion->hvalidacion as $wort)
                    {
                        if($wort['memo_sol'] == $hvalidacion->memorandum_solicitud)
                        {
                            $arch_sol = $wort['arch_sol'];
                        }
                    }
                }
                else
                {
                    $hvalidacion = NULL;
                }
            }
            $daesp = DB::TABLE('tbl_unidades')
                ->WHERE('ubicacion','LIKE',$request->valor[0]. $request->valor[1] .'%')
                ->GROUPBY('ubicacion')
                ->VALUE('ubicacion');
            $id_list = pre_instructor::WHERE('nrevision', '=', $request->valor)->PLUCK('id');
        }
        else //OPCION CUANDO NO HAY FILTRADO
        {
            if(!isset($rol)) // ANALIZA SI ROL NO ESTA ASIGNADO
            {
                $unirev = $userunidad->ubicacion['0'] . $userunidad->ubicacion['1'];
                $nrevisiones = $nrevisiones->WHERE('nrevision', 'LIKE', '%' . $unirev . '%')//'data_especialidad', [['unidad_solicita' => $userunidad->ubicacion]])
                                ->WHEREIN('status', ['EN CAPTURA','REACTIVACION EN CAPTURA','EN FIRMA','BAJA EN PREVALIDACION','BAJA EN FIRMA','REACTIVACION EN FIRMA','RETORNO'])
                                ->WHERE('turnado', '=', 'UNIDAD')
                                ->GET();


                $databuzon = pre_instructor::SELECT('id','nombre', 'apellidoPaterno', 'apellidoMaterno', 'nrevision', 'updated_at','lastUserId','status','turnado')
                                                ->WHERE('turnado','UNIDAD')
                                                ->WHERE('nrevision', 'LIKE', $unirev . '%')
                                                ->WHEREIN('status', ['EN CAPTURA','REACTIVACION EN CAPTURA','EN FIRMA','BAJA EN PREVALIDACION','BAJA EN FIRMA','REACTIVACION EN FIRMA','RETORNO'])
                                                ->GET();
                $buzonhistory = pre_instructor::SELECT('id','nombre', 'apellidoPaterno', 'apellidoMaterno', 'nrevision', 'updated_at','lastUserId','status','turnado')
                                                ->WHERE('turnado','DTA')
                                                ->WHERE('nrevision', 'LIKE', $unirev . '%')
                                                ->WHERENOTIN('status', ['EN CAPTURA','RETORNO','VALIDADO','BAJA'])
                                                ->GET();
                // dd($databuzon);

            }
            else //OPCION CUANDO ROL ESTA ASIGNADO
            {
                $nrevisiones = NULL;
                $databuzon = pre_instructor::SELECT('id','nombre', 'apellidoPaterno', 'apellidoMaterno', 'nrevision', 'updated_at','lastUserId','status','turnado')
                                                ->WHERE('turnado','DTA')
                                                ->WHERENOTIN('status', ['EN CAPTURA','RETORNO','VALIDADO'])
                                                ->GET();
                $buzonhistory = pre_instructor::SELECT('id','nombre', 'apellidoPaterno', 'apellidoMaterno', 'nrevision', 'updated_at','lastUserId','status','turnado')
                                                ->WHERE('turnado','UNIDAD')
                                                ->WHEREIN('status', ['EN CAPTURA','EN FIRMA','BAJA EN PREVALIDACION','BAJA EN FIRMA','REACTIVACION EN FIRMA','RETORNO'])
                                                ->GET();
            }

            foreach($databuzon as $contador => $ari)
        {
            $databuzon[$contador]->unidad_solicita = DB::TABLE('tbl_unidades')//->SELECT('ubicacion')
                        ->WHERE('ubicacion','LIKE',$ari->nrevision[0]. $ari->nrevision[1] .'%')
                        ->GROUPBY('ubicacion')
                        ->VALUE('ubicacion');
        }
        }
        $valor = $request->valor;
        $seluni = $request->seluni;


        return view('layouts.pages.initprevalidarinstructor', compact('data','valor','message','id_list','unidades','seluni','nrevisiones','rol','arch_sol','especialidadeslist','critpag','especialidades','perfiles','databuzon','userunidad','buzonhistory','daesp','chk_mod_espec','regimen_actual'));
    }

    public function crear_instructor()
    {
        $lista_civil = estado_civil::WHERE('id', '!=', '0')->ORDERBY('nombre', 'ASC')->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->ORDERBY('nombre','ASC')->GET();
        $bancos = Banco::all();
        $lista_regimen = DB::Table('cat_conceptos')->Where('tipo', 'REGIMEN')->Where('activo', TRUE)->GET();
        $paises = pais::all();

        return view('layouts.pages.frminstructor', compact('lista_civil','estados','bancos','lista_regimen','paises'));
    }

    #----- instructor/guardar -----#
    public function guardar_instructor(Request $request)
    {
        // dd($request);
        $verify = instructor::WHERE('curp','=', $request->curp)->FIRST();
        if(is_null($verify) == TRUE)
        {
            $saveInstructor = new instructor();
            $save_preinstructor = new pre_instructor();
            $uid = instructor::select('id')->WHERE('id', '!=', '0')->orderby('id','desc')->first();
            if ($uid['id'] === null) {
                # si es nulo entra una vez y se le asigna un valor
                $id = 1;
            } else {
                # entra pero no se le asigna valor
                $id = $uid->id + 1;
            }
            $instructor = $this->guardado_ins($saveInstructor, $request, $id);
            unset($instructor->data_especialidad);
            unset($instructor->data_perfil);

            $pre_instructor  = $this->guardado_ins($save_preinstructor, $request, $id);
            $pre_instructor->id_oficial = $instructor->id;
            $pre_instructor->registro_activo = TRUE;

            // dd($instructor);
            $pre_instructor->save();
            $instructor->save();

            $newa = (array) $instructor;
            $this->new_history($newa, $instructor, 'creacion de instructor por parte de la unidad');

            return redirect()->route('instructor-crear-p2',['id' => $instructor->id])
                ->with('success','Información basica agregada');

        }
        else
        {
            $clave_instructor = instructor::WHERE('curp', '=', $request->curp)->VALUE('numero_control');
            $mensaje = "Lo sentimos, la curp ".$request->curp." asociada a este registro ya se encuentra en la base de datos al instructor con clave ".$clave_instructor.".";
            return redirect('/instructor/crear')->withErrors($mensaje);
        }
    }

    public function crear_instructor_p2($id)
    {
        $newarr = array();
        $municipios_nacimiento = $localidades_nacimiento = NULL;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $lista_civil = estado_civil::WHERE('id', '!=', '0')->ORDERBY('nombre', 'ASC')->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->ORDERBY('nombre','ASC')->GET();
        $instructor_perfil = new InstructorPerfil();
        $datainstructor = pre_instructor::WHERE('id', '=', $id)->FIRST();
        $perfil = $this->make_collection($datainstructor->data_perfil);
        $validado = $this->make_collection($datainstructor->data_especialidad);
        $idest = DB::TABLE('estados')->WHERE('nombre','=',$datainstructor->entidad)->FIRST();
        $idestnac = DB::TABLE('estados')->WHERE('nombre','=',$datainstructor->entidad_nacimiento)->FIRST();
        $municipios = DB::TABLE('tbl_municipios')->SELECT('id','muni')->WHERE('id_estado', '=', $idest->id)
                        ->ORDERBY('muni','ASC')->GET();
        $bancos = Banco::all();
        $lista_regimen = DB::Table('cat_conceptos')->Where('tipo', 'REGIMEN')->Where('activo', TRUE)->GET();
        $paises = pais::all();

        if(isset($idestnac->id))
        {
            $municipios_nacimiento = DB::TABLE('tbl_municipios')->SELECT('id','muni')->WHERE('id_estado', '=', $idestnac->id)
                            ->ORDERBY('muni','ASC')->GET();
        }


        $munix = DB::TABLE('tbl_municipios')->SELECT('clave', 'id_estado')
                ->WHERE('muni', '=', $datainstructor->municipio)
                ->WHERE('estados.nombre',$datainstructor->entidad)
                ->JOIN('estados',DB::raw('CAST(estados.id AS varchar)'),'tbl_municipios.id_estado')
                ->FIRST();
        $munixnac = DB::TABLE('tbl_municipios')->SELECT('clave', 'id_estado')
                ->WHERE('muni', '=', $datainstructor->municipio_nacimiento)
                ->WHERE('estados.nombre',$datainstructor->entidad_nacimiento)
                ->JOIN('estados',DB::raw('CAST(estados.id AS varchar)'),'tbl_municipios.id_estado')
                ->FIRST();

        if($munix != NULL)
        {
            $localidades = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.clave','localidad')
                            ->WHERE('tbl_localidades.clave_municipio', '=', $munix->clave)
                            ->WHERE('tbl_localidades.id_estado', '=', $munix->id_estado)
                            ->ORDERBY('tbl_localidades.localidad', 'ASC')
                            ->GET();
        }
        if($munixnac != NULL)
        {
            $localidades_nacimiento = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.clave','localidad')
                            ->WHERE('tbl_localidades.clave_municipio', '=', $munixnac->clave)
                            ->WHERE('tbl_localidades.id_estado', '=', $munixnac->id_estado)
                            ->ORDERBY('tbl_localidades.localidad', 'ASC')
                            ->GET();
        }
        // dd($pre_especialidad);
        if($validado != FALSE)
        {
            foreach($validado as $key => $ges)
            {
                $nomespec = DB::TABLE('especialidades')->SELECT('nombre')->WHERE('id',$ges->especialidad_id)->FIRST();
                $validado[$key]->nombre = $nomespec->nombre;
                $validado[$key]->espinid = $ges->id;
            }
        }

        $nrevisiones = DB::TABLE('especialidad_instructores')->SELECT('instructores.nrevision')
                    ->JOIN('instructores','instructores.id', '=', 'especialidad_instructores.id_instructor')
                    ->WHERE('unidad_solicita', '=', $userunidad->ubicacion)
                    ->WHEREIN('especialidad_instructores.status', ['EN CAPTURA','RETORNO'])
                    ->WHERE('nrevision', '!=', NULL)
                    ->GROUPBY('nrevision')
                    ->ORDERBY('nrevision','ASC')
                    ->GET();

        $nrevisionlast = DB::TABLE('especialidad_instructores')->SELECT('instructores.nrevision')
                        ->JOIN('instructores','instructores.id', '=', 'especialidad_instructores.id_instructor')
                        ->WHERE('unidad_solicita', '=', $userunidad->ubicacion)
                        ->WHEREIN('especialidad_instructores.status', ['EN CAPTURA','RETORNO'])
                        ->WHERE('nrevision', '!=', NULL)
                        ->GROUPBY('nrevision')
                        ->ORDERBY('nrevision','DESC')
                        ->FIRST();

        if(!isset($nrevisionlast))
        {
            $nrevisionlast = 0;
        }

        // verificacion de datos alfa. si es nulo o se construye array nulo para evitar errores
        if($datainstructor->instructor_alfa != true) {
            $datainstructor->datos_alfa = $this->instructor_alfa_nulo();
        }
        // dd($nrevisionlast);
        return view('layouts.pages.frminstructorp2', compact('perfil','userunidad','validado','id', 'datainstructor','lista_civil','estados','municipios','localidades','municipios_nacimiento','localidades_nacimiento','nrevisiones','nrevisionlast','bancos','lista_regimen','paises'));
    }

    public function send_to_dta(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        $chk_mod_perfil = $chk_mod_esp = false;
        $movimiento = NULL;
        $newb = $newc = $arrtemp = array();
        $stat_arr = array('EN CAPTURA','REACTIVACION EN CAPTURA','REVALIDACION EN CAPTURA','BAJA EN CAPTURA','RETORNO','REVALIDACION RETORNADA','BAJA RETORNADA');

            $bajachk = FALSE;
            $movimiento = 'Envio a DTA para su prevalidacion ';
            $modInstructor = pre_instructor::find($request->idinstructores);
            $regimen_actual = instructor::Where('id',$request->idinstructores)->Value('tipo_honorario');
            $perfiles = $this->make_collection($modInstructor->data_perfil);
            $especialidades = $this->make_collection($modInstructor->data_especialidad);
            // se llaman las funciones de string a arrays
            $newa = (array) $modInstructor;
            // if(isset($newa["\x00*\x00attributes"]["entrevista"]))
            // {
            //     $newa["\x00*\x00attributes"]["entrevista"] = $this->basic_array($newa["\x00*\x00attributes"]["entrevista"]);
            // }
            // if(isset($modInstructor->exp_laboral))
            // {
            //     $newa["\x00*\x00attributes"]["exp_laboral"] = $this->complex_array($newa["\x00*\x00attributes"]["exp_laboral"]);
            // }
            // if(isset($modInstructor->exp_docente))
            // {
            //     $newa["\x00*\x00attributes"]["exp_docente"] = $this->complex_array($newa["\x00*\x00attributes"]["exp_docente"]);
            // }

            foreach($perfiles as $moist)
            {
                if($moist->status != 'VALIDADO')
                {
                    $chk_mod_perfil = TRUE;
                }

            }
            foreach($especialidades as $joyo)
            {
                if($joyo->status != 'VALIDADO')
                {
                    $chk_mod_esp = TRUE;
                }
            }

            $modInstructor->turnado = 'DTA';
            $modInstructor->status = 'PREVALIDACION';

            if($chk_mod_perfil == TRUE)
            {
                $movimiento = $movimiento . 'con perfil profesional: ';

                foreach($perfiles as $key => $item)
                {
                    if(in_array($item->status, $stat_arr))
                    {
                        $arrper = (array) $item;
                        array_push($newb, $arrper);
                        switch($item->status)
                        {
                            case 'EN CAPTURA':
                                $perfiles[$key]->status = 'PREVALIDACION';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                            break;
                            case 'REVALIDACION EN CAPTURA':
                                $perfiles[$key]->status = 'REVALIDACION EN PREVALIDACION';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                            break;
                            case 'REACTIVACION EN CAPTURA':
                                $perfiles[$key]->status = 'REACTIVACION EN PREVALIDACION';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REACTIVACION), ';
                            break;
                            case 'BAJA EN CAPTURA':
                                $perfiles[$key]->status = 'BAJA EN PREVALIDACION';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA), ';
                            break;
                            case 'RETORNO':
                                if($perfiles[$key]->new == FALSE)
                                {
                                    $perfiles[$key]->status = 'REVALIDACION EN PREVALIDACION';
                                    $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                                }
                                else
                                {
                                    $perfiles[$key]->status = 'PREVALIDACION';
                                    $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                                }
                            break;
                            case 'REVALIDACION RETORNADA':
                                $perfiles[$key]->status = 'REVALIDACION EN PREVALIDACION';
                            break;
                            case 'BAJA RETORNADA':
                                $perfiles[$key]->status = 'BAJA EN PREVALIDACION';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA), ';
                            break;
                        }
                    }
                }
                $modInstructor->data_perfil = $perfiles;
            }

            if($chk_mod_esp == TRUE)
            {
                $movimiento = $movimiento . 'y especialidades a impartir: ';

                foreach($especialidades as $llave => $cadwell)
                {
                    if(in_array($cadwell->status, $stat_arr))
                    {
                        $arresp = (array) $cadwell;
                        array_push($newc, $arresp);
                        // $especialidades[$llave]->fecha_solicitud = NULL;
                        $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();
                        switch($especialidades[$llave]->status)
                        {
                            case 'EN CAPTURA':
                                $especialidades[$llave]->status = 'PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ',  ';
                            break;
                            case 'REVALIDACION EN CAPTURA':
                                $especialidades[$llave]->status = 'REVALIDACION EN PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ' (REVALIDACION),  ';
                            break;
                            case 'REACTIVACION EN CAPTURA':
                                $especialidades[$llave]->status = 'REACTIVACION EN PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ' (REACTIVACION),  ';
                            break;
                            case 'BAJA EN CAPTURA':
                                $especialidades[$llave]->status = 'BAJA EN PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ' (BAJA),  ';
                                $bajachk = TRUE;
                            break;
                            case 'RETORNO':
                                if($especialidades[$llave]->new == FALSE)
                                {
                                    $especialidades[$llave]->status = 'REVALIDACION EN PREVALIDACION';
                                    $movimiento = $movimiento . $especialidad->nombre . ' (REVALIDACION),  ';
                                }
                                else
                                {
                                    $especialidades[$llave]->status = 'PREVALIDACION';
                                    $movimiento = $movimiento . $especialidad->nombre . ',  ';
                                }
                            break;
                            case 'REVALIDACION RETORNADA':
                                $especialidades[$llave]->status = 'REVALIDACION EN PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ' (REVALIDACION),  ';
                            break;
                            case 'BAJA RETORNADA':
                                $especialidades[$llave]->status = 'BAJA EN PREVALIDACION';
                                $movimiento = $movimiento . $especialidad->nombre . ' (BAJA),  ';
                                $bajachk = TRUE;
                            break;
                        }
                        $especialidades[$llave]->memorandum_solicitud = NULL;
                    }
                }
                $modInstructor->data_especialidad = $especialidades;
            }

            if($bajachk == TRUE)
            {
                $movimiento = $movimiento . ' con motivo: ' . $modInstructor->motivo;
            }

            if($chk_mod_perfil == FALSE && $chk_mod_esp == FALSE)
            {
                if($regimen_actual !=  $modInstructor->tipo_honorario) {
                    $movimiento = $movimiento . 'modificacion en regimen fiscal';
                } else {
                    $movimiento = $movimiento . 'las modificaciones de informacion general del instructor';
                }
            }

            $historico = new instructor_history;
            $historico->id_instructor = $modInstructor->id;
            $historico->id_user = $userId;
            $historico->movimiento = $movimiento;
            $historico->status = $modInstructor->status;
            $historico->turnado = $modInstructor->turnado;
            // dd($newa["\x00*\x00attributes"]);
            $historico->data_instructor = 'temp'; //$newa["\x00*\x00attributes"];
            // $historico->data_perfil = $newb;
            // $historico->data_especialidad = $newc;
            $historico->nrevision = $modInstructor->nrevision;

            $insstat = instructor::find($request->idinstructores);
            $insstat->turnado = 'DTA';
            $insstat->status = 'PREVALIDACION';

            $modInstructor->save();
            $historico->save();
            $insstat->save();
            // dd($historico);

        return redirect('/prevalidacion/instructor')->with('success','REGISTROS ENVIADO A DTA CORRECTAMENTE');
    }

    public function prevalidar(request $request)
    {
        // dd($request);
        $chk_mod_perfil = $chk_mod_esp = false;
        $userId = Auth::user()->id;
        $idlist = explode(",", $request->idinstructoresprev);
        $newb = $newc = $arrtemp = array();
        $stat_arr = array('PREVALIDACION','REVALIDACION EN PREVALIDACION','BAJA EN PREVALIDACION','REACTIVACION EN PREVALIDACION');

        foreach($idlist as $bosmer)
        {
            $modInstructor = pre_instructor::find($bosmer);
            $regimen_actual = instructor::Where('id',$bosmer)->Value('tipo_honorario');
            $modInstructor->turnado = 'UNIDAD';

            if($modInstructor->status != 'VALIDADO')
            {
                switch ($modInstructor->status)
                {
                    case 'BAJA EN PREVALIDACION':
                        $modInstructor->status = 'BAJA EN FIRMA';
                        $movimiento = 'prevalidacion para la firma de la baja de instructor ';
                    break;
                    case 'REACTIVACION EN PREVALIDACION':
                        $modInstructor->status = 'REACTIVACION EN FIRMA';
                        $movimiento = 'prevalidacion para la firma de la reactivacion de instructor ';
                    break;
                    default:
                        $modInstructor->status = 'EN FIRMA';
                        $movimiento = 'prevalidacion para la firma de unidad ';
                    break;
                }
            }

            $modInstructor->lastUserId = $userId;

            $perfiles = $this->make_collection($modInstructor->data_perfil);
            $especialidades = $this->make_collection($modInstructor->data_especialidad);

            foreach($perfiles as $moist)
            {
                if($moist->status != 'VALIDADO' && $moist->status != 'BAJA')
                {
                    $chk_mod_perfil = TRUE;
                }

            }
            foreach($especialidades as $joyo)
            {
                if(($joyo->status != 'VALIDADO' && $joyo->status != 'BAJA') || $regimen_actual != $modInstructor->tipo_honorario)//A
                {
                    $chk_mod_esp = TRUE;
                }
            }

            if($chk_mod_perfil == TRUE)
            {
                $movimiento = $movimiento . 'con perfil profesional: ';
                foreach($perfiles as $key => $item)
                {
                    if(in_array($item->status, $stat_arr))
                    {
                        switch($item->status)
                        {
                            case 'PREVALIDACION';
                                $perfiles[$key]->status = 'EN FIRMA';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                            break;
                            case 'REVALIDACION EN PREVALIDACION';
                                if(!isset($especialidades[0]->id))
                                {
                                    $modInstructor->status = 'VALIDADO';
                                    $perfiles[$key]->status = 'VALIDADO';
                                    $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION DIRECTA), ';
                                }
                                else
                                {
                                    $perfiles[$key]->status = 'REVALIDACION EN FIRMA';
                                    $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                                }
                            break;
                            case 'BAJA EN PREVALIDACION';
                                $perfiles[$key]->status = 'BAJA EN FIRMA';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA)';

                            break;
                            case 'REACTIVACION EN PREVALIDACION';
                                $perfiles[$key]->status = 'REACTIVACION EN FIRMA';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera;
                            break;
                        }
                    }
                }
                $modInstructor->data_perfil = $perfiles;
            }

            if($chk_mod_esp == TRUE)
            {
                $movimiento = $movimiento . 'y especialidades a impartir: ';

                foreach($especialidades as $llave => $cadwell)
                {
                    if(in_array($cadwell->status, $stat_arr))
                    {
                        $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();

                        switch($cadwell->status)
                        {
                            case 'PREVALIDACION';
                                $especialidades[$llave]->status = 'EN FIRMA';
                                $movimiento = $movimiento . $especialidad->nombre . ',  ';
                                $especialidades[$llave]->fecha_solicitud = $request->fechadocs;
                                $especialidades[$llave]->fecha_validacion = $request->fechadocs;
                            break;
                            case 'REVALIDACION EN PREVALIDACION';
                                $especialidades[$llave]->status = 'REVALIDACION EN FIRMA';
                                $movimiento = $movimiento . $especialidad->nombre . ' (REVALIDACION),  ';
                                $especialidades[$llave]->fecha_solicitud = $request->fechadocs;
                                $especialidades[$llave]->fecha_validacion = $request->fechadocs;
                            break;
                            case 'BAJA EN PREVALIDACION';
                                $especialidades[$llave]->status = 'BAJA EN FIRMA';
                                $especialidades[$llave]->fecha_solicitud = $request->fechadocs;
                                $especialidades[$llave]->fecha_baja = $request->fechadocs;
                                $especialidades[$llave]->memorandum_baja = NULL;
                                $movimiento = $movimiento . $especialidad->nombre . ' (BAJA),  ';
                            break;
                            case 'REACTIVACION EN PREVALIDACION';
                                $especialidades[$llave]->status = 'REACTIVACION EN FIRMA';
                                $especialidades[$llave]->fecha_baja = $request->fechadocs;
                                $especialidades[$llave]->memorandum_baja = NULL;
                                $especialidades[$llave]->memorandum_solicitud = NULL;
                                $especialidades[$llave]->fecha_solicitud = $request->fechadocs;
                                $movimiento = $movimiento . $especialidad->nombre . ',  ';
                            break;
                        }
                    } else if ($regimen_actual != $modInstructor->tipo_honorario && $cadwell->status != 'BAJA') {
                        $especialidades[$llave]->fecha_solicitud = $request->fechadocs;
                        $especialidades[$llave]->fecha_validacion = $request->fechadocs;
                    }
                    // $especialidades[$llave]->fecha_validacion = NULL;
                    $especialidades[$llave]->memorandum_validacion = NULL;
                }
                $modInstructor->data_especialidad = $especialidades;
            }

            if($chk_mod_perfil == FALSE && $chk_mod_esp == FALSE && $regimen_actual == $modInstructor->tipo_honorario)
            {
                $modInstructor->status = 'VALIDADO';
                $modInstructor->registro_activo = FALSE;
                $movimiento = 'prevalidacion de las modificaciones de informacion basica del instructor';
                $this->guardado_oficial($modInstructor);//a
            }

            $modInstructor->save();

            $historico = new instructor_history;
            $historico->id_instructor = $modInstructor->id;
            $historico->id_user = $userId;
            $historico->movimiento = $movimiento;
            $historico->status = $modInstructor->status;
            $historico->turnado = $modInstructor->turnado;
            $historico->nrevision = $modInstructor->nrevision;
            $historico->save();

        }

        return redirect('/prevalidacion/instructor')->with('success','REGISTROS PREVALIDADOS CORRECTAMENTE');
    }

    public function solicitud_firmada_todta(Request $request)
    {
        // dd($request);
        $saveInstructor = pre_instructor::find($request->idinsdoctodta);
        $regimen_actual = instructor::Where('id',$request->idinsdoctodta)->Value('tipo_honorario');
        $userId = Auth::user()->id;
        $stat_arr = array('EN FIRMA','REVALIDACION EN FIRMA','BAJA EN FIRMA','REACTIVACION EN FIRMA');
        $perfiles = $this->make_collection($saveInstructor->data_perfil);
        $especialidades = $this->make_collection($saveInstructor->data_especialidad);
        $movimientoesp = $movimientoper = NULL;

        $solva = $request->file('memosolicitud'); # obtenemos el archivo
        $url = $this->pdf_upload($solva, $request->idinsdoctodta, 'solicitudespecialidad'); # invocamos el método

        foreach ($especialidades AS $posi => $cadwell)
        {
            if(in_array($cadwell->status, $stat_arr) || ($regimen_actual != $saveInstructor->tipo_honorario && $cadwell->status != 'BAJA'))
            {
                if(!isset($cadwell->hvalidacion))
                {
                    $especialidades[$posi]->hvalidacion = NULL;
                }
                if($especialidades[$posi]->hvalidacion == null)
                {
                    $record = array( "0" => array(
                        "memo_sol" => $cadwell->memorandum_solicitud,
                        "fecha_sol" => $cadwell->fecha_solicitud,
                        "arch_sol" => $url,
                        "memo_val" => null,
                        "fecha_val" => null,
                        "arch_val" => null));
                }
                else
                {
                    if($cadwell->status == 'BAJA EN FIRMA')
                    {
                        $newrecord = array(
                            "memo_sol" => $cadwell->memorandum_solicitud,
                            "fecha_sol" => $cadwell->fecha_solicitud,
                            "arch_sol" => $url,
                            "memo_baja" => null,
                            "fecha_baja" => null,
                            "arch_baja" => null);
                    }
                    else
                    {
                    $newrecord = array(
                        "memo_sol" => $cadwell->memorandum_solicitud,
                        "fecha_sol" => $cadwell->fecha_solicitud,
                        "arch_sol" => $url,
                        "memo_val" => null,
                        "fecha_val" => null,
                        "arch_val" => null);
                    }
                    if(!is_array($cadwell->hvalidacion)) {
                        $cadwell->hvalidacion = json_decode($cadwell->hvalidacion, true);
                    }
                    $count = count($cadwell->hvalidacion);
                    $record = $cadwell->hvalidacion;
                    $record[$count] = $newrecord;

                }

                $especialidades[$posi]->hvalidacion = $record;

                $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();
                switch($cadwell->status)
                {
                    case 'EN FIRMA';
                        $movimientoesp = $movimientoesp . $especialidad->nombre . ',  ';
                    break;
                    case 'REVALIDACION EN FIRMA';
                        $movimientoesp = $movimientoesp . $especialidad->nombre . ' (REVALIDACION),  ';
                    break;
                    case 'BAJA EN FIRMA';
                        $movimientoesp = $movimientoesp . $especialidad->nombre . '(BAJA),  ';
                    break;
                    case 'REACTIVACION EN FIRMA';
                        $movimientoesp = $movimientoesp . $especialidad->nombre . '(REACTIVACION),  ';
                    break;
                }
            }
        }

        $movimiento = 'Firma de unidad enviada a DTA con numero de memo: ' . $cadwell->memorandum_solicitud . ' ';
        switch($saveInstructor->status)
        {
            case 'BAJA EN FIRMA';
                $movimiento = $movimiento . 'para la baja de instructor ';
            break;
            case 'REACTIVACION EN FIRMA';
                $movimiento = $movimiento . 'para la reactivacion de instructor ';
            break;
        }

        if(isset($perfiles[0]->id))
        {
            $movimiento = $movimiento . 'con perfil profesional: ';
            foreach($perfiles AS $item)
            {
                if(in_array($item->status, $stat_arr))
                {
                    switch ($item->status)
                    {
                        case 'EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                        break;
                        case 'REVALIDACION EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                        break;
                        case 'BAJA EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA), ';
                        break;
                        case 'REACTIVACION EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REACTIVACION), ';
                        break;
                    }
                }
            }
        }

        $movimiento = $movimiento . 'y especialidades a impartir: ' . $movimientoesp;

        $saveInstructor->data_especialidad = $especialidades;
        $saveInstructor->turnado = 'DTA';
        $saveInstructor->save();

        $historico = new instructor_history;
        $historico->id_instructor = $saveInstructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $saveInstructor->status;
        $historico->turnado = $saveInstructor->turnado;
        $historico->nrevision = $saveInstructor->nrevision;
        $historico->save();

        return redirect('/prevalidacion/instructor')->with('success','REGISTROS ENVIADOS CORRECTAMENTE');
    }

    public function validar(Request $request)
    {
        $movimientoesp = $movimientoper = $mvalidacion = NULL;
        $saveInstructor = pre_instructor::find($request->idinsvalidar);
        $regimen_actual = instructor::Where('id',$saveInstructor->id)->Value('tipo_honorario');
        $arrtemp = array('EN FIRMA','REVALIDACION EN FIRMA','REACTIVACION EN FIRMA','BAJA EN FIRMA');
        $userId = Auth::user()->id;
        $userUnidad = DB::TABLE('tbl_unidades')->SELECT('cct')
                        ->WHERE('id','=',Auth::user()->unidad)
                        ->FIRST();
        $perfiles = $this->make_collection($saveInstructor->data_perfil);
        $especialidades  = $this->make_collection($saveInstructor->data_especialidad);
        $solva = $request->file('memovalidacion'); # obtenemos el archivo
        $url = $this->pdf_upload($solva, $request->idinsvalidar, 'validacionespecialidad'); # invocamos el método

        $movimiento = 'Validacion cargada con numero de memo: ' . $mvalidacion . ' ';
        if($saveInstructor->status == 'REACTIVACION EN FIRMA')
        {
            $movimiento = $movimiento . 'para la revalidacion del instructor ';
        }

        if(isset($perfiles[0]->id))
        {
            $movimiento = $movimiento . 'con perfil profesional: ';
            foreach($perfiles AS $llave => $item)
            {
                if(in_array($item->status, $arrtemp))
                {
                    switch($item->status)
                    {
                        case 'EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                        break;
                        case 'REVALIDACION EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                        break;
                        case 'REACTIVACION EN FIRMA';
                            $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REACTIVACION), ';
                        break;
                    }

                    if($item->status == 'EN FIRMA')
                    {
                        $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                    }
                    else
                    {
                        $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                    }

                    $perfiles[$llave]->status = 'VALIDADO';
                }

                if(!isset($item->new) || $item->new == FALSE)
                {
                    $perfil = instructorPerfil::find($item->id);
                }
                else
                {
                    $perfil = new instructorPerfil();
                }

                $perfil->grado_profesional = $perfiles[$llave]->grado_profesional;
                $perfil->area_carrera = $perfiles[$llave]->area_carrera;
                $perfil->estatus = $perfiles[$llave]->estatus;
                $perfil->pais_institucion = $perfiles[$llave]->pais_institucion;
                $perfil->entidad_institucion = $perfiles[$llave]->entidad_institucion;
                $perfil->ciudad_institucion = $perfiles[$llave]->ciudad_institucion;
                $perfil->nombre_institucion = $perfiles[$llave]->nombre_institucion;
                $perfil->fecha_expedicion_documento = $perfiles[$llave]->fecha_expedicion_documento;
                $perfil->periodo = $perfiles[$llave]->periodo;
                $perfil->folio_documento = $perfiles[$llave]->folio_documento;
                $perfil->cursos_recibidos = $perfiles[$llave]->cursos_recibidos;
                $perfil->capacitador_icatech = $perfiles[$llave]->capacitador_icatech;
                $perfil->recibidos_icatech = $perfiles[$llave]->recibidos_icatech;
                $perfil->cursos_impartidos = $perfiles[$llave]->cursos_impartidos;
                $perfil->numero_control = $perfiles[$llave]->numero_control;
                $perfil->lastUserId = $perfiles[$llave]->lastUserId;
                $perfil->status = $perfiles[$llave]->status;
                $perfil->periodo = $perfiles[$llave]->periodo;
                $perfil->carrera = $perfiles[$llave]->carrera;
                $perfil->save();

                foreach($especialidades as $moist)
                {
                    if($moist->perfilprof_id == $item->id)
                    {
                        $moist->perfilprof_id = $perfil->id;
                    }
                }
                $perfiles[$llave]->new = FALSE;
                $perfiles[$llave]->id = $perfil->id;
            }
            $saveInstructor->data_perfil = $perfiles;
        }

        if(isset($especialidades[0]->id))
        {
            foreach ($especialidades AS $key => $cadwell)
            {
                if(in_array($cadwell->status, $arrtemp) || ($regimen_actual != $saveInstructor->tipo_honorario && $cadwell->status != 'BAJA'))
                {
                    $hvalidacion = $cadwell->hvalidacion;
                    $end = count($hvalidacion) - 1;
                    if($cadwell->status == 'BAJA EN FIRMA')
                    {
                        $hvalidacion[$end]['memo_baja'] = $cadwell->memorandum_baja;
                        $hvalidacion[$end]['fecha_baja'] = $cadwell->fecha_validacion;
                        $hvalidacion[$end]['arch_baja'] = $url;
                        $especialidades[$key]->hvalidacion = $hvalidacion;
                    }
                    else
                    {
                        $hvalidacion[$end]['memo_val'] = $cadwell->memorandum_validacion;
                        $hvalidacion[$end]['fecha_val'] = $cadwell->fecha_validacion;
                        $hvalidacion[$end]['arch_val'] = $url;
                        $especialidades[$key]->hvalidacion = $hvalidacion;
                    }

                    $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();
                    switch($cadwell->status)
                    {
                        case 'EN FIRMA';
                            $movimientoesp = $movimientoesp . $especialidad->nombre . ',  ';
                        break;
                        case 'REVALIDACION EN FIRMA';
                            $movimientoesp = $movimientoesp . $especialidad->nombre . ' (REVALIDACION),  ';
                        break;
                        case 'REACTIVACION EN FIRMA';
                            $movimientoesp = $movimientoesp . $especialidad->nombre . ' (REACTIVACION),  ';
                        break;
                        case 'BAJA EN FIRMA';
                            $movimientoesp = $movimientoesp. $especialidad->nombre . ' (BAJA),  ';
                        break;
                    }

                    if($cadwell->status == 'BAJA EN FIRMA')
                    {
                        $especialidades[$key]->status = 'BAJA';
                        $especialidades[$key]->activo = FALSE;
                    }
                    else
                    {
                        $especialidades[$key]->status = 'VALIDADO';
                        $especialidades[$key]->activo = TRUE;
                    }

                    $mvalidacion = $cadwell->memorandum_validacion;

                    if($cadwell->new == false)
                    {
                        $espins = especialidad_instructor::find($cadwell->id);
                    }
                    else
                    {
                        $espins = new especialidad_instructor();
                    }

                    $espins->especialidad_id = $especialidades[$key]->especialidad_id;
                    $espins->perfilprof_id = $especialidades[$key]->perfilprof_id;
                    $espins->unidad_solicita = $especialidades[$key]->unidad_solicita;
                    if(isset($especialidades[$key]->memorandum_validacion))
                    {
                        $espins->memorandum_validacion = $especialidades[$key]->memorandum_validacion;
                        $espins->fecha_validacion = $especialidades[$key]->fecha_validacion;
                    }
                    $espins->memorandum_modificacion = $especialidades[$key]->memorandum_modificacion;
                    $espins->observacion = $especialidades[$key]->observacion;
                    $espins->criterio_pago_id = $especialidades[$key]->criterio_pago_id;
                    $espins->lastUserId = $especialidades[$key]->lastUserId;
                    $espins->activo = $especialidades[$key]->activo;
                    $espins->id_instructor = $especialidades[$key]->id_instructor;
                    $espins->cursos_impartir = $especialidades[$key]->cursos_impartir;
                    $espins->memorandum_solicitud = $especialidades[$key]->memorandum_solicitud;
                    $espins->fecha_solicitud = $especialidades[$key]->fecha_solicitud;
                    $espins->status = $especialidades[$key]->status;
                    $espins->observacion_validacion = $especialidades[$key]->observacion_validacion;
                    $espins->hvalidacion = $especialidades[$key]->hvalidacion;

                    if(isset($especialidades[$key]->memorandum_baja))
                    {
                        $espins->memorandum_baja = $especialidades[$key]->memorandum_baja;
                        $espins->fecha_baja = $especialidades[$key]->fecha_baja;
                    }

                    if($espins->status == 'BAJA' && $cadwell->new == TRUE)
                    {
                        $espins->memorandum_validacion = $espins->memorandum_baja = $especialidades[$key]->memorandum_baja;
                        $espins->fecha_validacion = $especialidades[$key]->fecha_baja;
                    }
                    $espins->save();
                    $especialidades[$key]->new = FALSE;
                    $especialidades[$key]->id = $espins->id;
                }
            }
            $saveInstructor->data_especialidad = $especialidades;
        }

        $movimiento = $movimiento . 'y especialidades a impartir: ' . $movimientoesp;

        if($saveInstructor->numero_control == 'Pendiente')
        {
            $uni = substr($userUnidad->cct, -3, 2) * 1 . substr($userUnidad->cct, -1);
            $now = Carbon::now();
            $year = substr($now->year, -2);
            $rfcpart = substr($saveInstructor->rfc, 0, 10);
            $numero_control = $uni.$year.$rfcpart;
            $saveInstructor->clave_unidad = $userUnidad->cct;
        }
        else
        {
            $D3 = substr($saveInstructor->numero_control, 0, 3);
            if( $D3 == '10K' || $D3 == '11J')
            {
                $part1 = substr($saveInstructor->numero_control, 0, 5);
            }
            else
            {
                $part1 = substr($saveInstructor->numero_control, 0, 4);
            }
            $rfcpart = substr($saveInstructor->rfc, 0, 10);
            $numero_control = $part1 . $rfcpart;
        }
        $saveInstructor->numero_control = trim($numero_control);
        $saveInstructor->turnado = 'UNIDAD';
        $saveInstructor->estado = TRUE;
        $saveInstructor->lastUserId = Auth::user()->id;
        $saveInstructor->status = 'VALIDADO';
        $saveInstructor->registro_activo = FALSE;
        $saveInstructor->save();

        $this->guardado_oficial($saveInstructor);

        $historico = new instructor_history;
        $historico->id_instructor = $saveInstructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $saveInstructor->status;
        $historico->turnado = $saveInstructor->turnado;
        $historico->save();

        return redirect('/prevalidacion/instructor')->with('success','REGISTROS VALIDADOS CORRECTAMENTE');
    }

    public function movimiento_retorno(Request $request)
    {
        if($request->movimiento != 'sin especificar') {
            $instructor = pre_instructor::find($request->id);
            $instructor_oficial = instructor::find($request->id);

            $copiaDataEspecialidad = $instructor->data_especialidad;
            foreach ($copiaDataEspecialidad as $key => $data) {
                $hvalidacion = $data['hvalidacion'];
                // Verificar si hay elementos en hvalidacion
                if (count($hvalidacion) > 0) {
                    // Obtener el último elemento
                    if((in_array($data['status'],['REVALIDACION EN FIRMA', 'BAJA EN FIRMA', 'EN FIRMA']) && $instructor['turnado'] == 'DTA') || $data['status'] == 'VALIDADO') {
                        $ultimoElemento = array_pop($hvalidacion);
                        $data['hvalidacion'] = $hvalidacion;
                    }
                    if($request->movimiento == 'retornar en firma') {
                        if($data['status'] == 'VALIDADO') {
                            $data['status'] = 'REVALIDACION EN FIRMA';
                        } else {
                            $data['status'] = 'BAJA EN FIRMA';
                        }
                    } else {
                        if($data['status'] != 'BAJA') {
                            $data['status'] = 'REVALIDACION EN CAPTURA';
                        } else {
                            $data['status'] = 'BAJA EN CAPTURA';
                        }
                    }

                    if($data['new'] == false && $data['status'] == 'VALIDADO') {
                        $especialidad_oficial = especialidad_instructor::WHERE('id', '=', $data['id'])->FIRST();
                        $hvalidacion_oficial = $especialidad_oficial->hvalidacion;
                        array_pop($hvalidacion_oficial);
                        $especialidad_oficial->hvalidacion = $hvalidacion_oficial;
                        $especialidad_oficial->save();
                    }
                    // Actualizar la data en la copia del array
                    $copiaDataEspecialidad[$key] = $data;
                }
            }

            $instructor->data_especialidad = $copiaDataEspecialidad;
            $instructor->registro_activo = true;
            $instructor->turnado = 'UNIDAD';

            if($request->movimiento == 'retornar en firma') {
                $instructor->status = 'EN FIRMA';
                $instructor_oficial->status = 'EN FIRMA';
            } else {
                $instructor->status = 'EN CAPTURA';
                $instructor_oficial->status = 'EN CAPTURA';
            }

            $instructor->save();
            $instructor_oficial->save();

            return back()->with('success', 'Se ha retornado al instructor de manera exitosa');
        } else {
            return back()->with('mensaje', 'Error: No se ha especificado el movimiento');
        }
    }

    public function rechazo_save(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        $idlist = explode(",", $request->idinstructoresreturn);
        $newb = $newc = $arrtemp = array();
        $retorno_firma = false;
        $stat_arr = array('PREVALIDACION','REVALIDACION EN PREVALIDACION','BAJA EN PREVALIDACION','BAJA EN FIRMA','EN FIRMA','REVALIDACION EN FIRMA');
        foreach($idlist as $bosmer)
        {
            $chk_mod_perfil = $chk_mod_esp = $retorno_firma = FALSE;
            $movimiento = 'Retorno a unidad para su modificacion ';
            $modInstructor = pre_instructor::find($bosmer);
            $modInstructor->turnado = 'UNIDAD';
            $modInstructor->rechazo = $request->observacion_retorno;

            $modInstructor->lastUserId = $userId;

            $perfiles = $this->make_collection($modInstructor->data_perfil);
            $especialidades = $this->make_collection($modInstructor->data_especialidad);

            foreach($perfiles as $moist)
            {
                if($moist->status != 'VALIDADO')
                {
                    $chk_mod_perfil = TRUE;
                }

            }
            foreach($especialidades as $joyo)
            {
                if($joyo->status != 'VALIDADO')
                {
                    $chk_mod_esp = TRUE;
                }
            }

            if($chk_mod_perfil == TRUE)
            {
                $movimiento = $movimiento . 'con perfil profesional: ';
                foreach($perfiles as $key => $item)
                {
                    if(in_array($item->status, $stat_arr))
                    {
                        $arrper = (array) $item;
                        array_push($newb, $arrper);
                        switch($item->status)
                        {
                            case 'PREVALIDACION':
                                $perfiles[$key]->status = 'RETORNO';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ', ';
                            break;
                            case 'REVALIDACION EN PREVALIDACION':
                                $perfiles[$key]->status = 'REVALIDACION RETORNADA';
                                $movimiento = $movimiento . $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION), ';
                            break;
                            case 'BAJA EN PREVALIDACION':
                                $perfiles[$key]->status = 'RETORNO';
                            break;
                            case 'REVALIDACION EN FIRMA':
                                if($request->tipo_envio == 'captura') {
                                    $movimiento = $movimiento. $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION EN FIRMA), ';
                                    $perfiles[$key]->status = 'EN CAPTURA';
                                } else {
                                    $movimiento = $movimiento. $item->grado_profesional . ' ' . $item->area_carrera . ' (REVALIDACION EN FIRMA), ';
                                    $retorno_firma = TRUE;
                                }

                            break;
                            case 'BAJA EN FIRMA':
                                $movimiento = $movimiento. $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA), ';
                                $retorno_firma = TRUE;
                            break;
                            case 'EN FIRMA':
                                $movimiento = $movimiento. $item->grado_profesional . ' ' . $item->area_carrera . ' (EN FIRMA), ';
                                $retorno_firma = TRUE;
                            break;
                            case 'BAJA EN FIRMA':
                                $movimiento = $movimiento. $item->grado_profesional . ' ' . $item->area_carrera . ' (BAJA EN FIRMA), ';
                                $retorno_firma = TRUE;
                            break;
                        }
                    }
                }
                $modInstructor->data_perfil = $perfiles;
            }

            if($chk_mod_esp == TRUE)
            {
                $movimiento = $movimiento . 'y especialidades a impartir: ';
                foreach($especialidades as $space => $cadwell)
                {
                    if(in_array($cadwell->status, $stat_arr))
                    {
                        $especialidades[$space]->fecha_solicitud = NULL;
                        $especialidades[$space]->memorandum_solicitud = NULL;
                        $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();
                        switch ($cadwell->status)
                        {
                            case 'PREVALIDACION':
                                $especialidades[$space]->status = 'RETORNO';
                                $movimiento = $movimiento . $especialidad->nombre . ',  ';
                            break;
                            case 'REVALIDACION EN PREVALIDACION':
                                $especialidades[$space]->status = 'REVALIDACION RETORNADA';
                                $movimiento = $movimiento . $especialidad->nombre . ' (REVALIDACION),  ';
                            case 'BAJA EN PREVALIDACION':
                                $especialidades[$space]->status = 'RETORNO';
                                $movimiento = $movimiento. $especialidad->nombre . ' (BAJA), ';
                            break;
                            case 'REVALIDACION EN FIRMA':
                                if($request->tipo_envio == 'captura') {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (REVALIDACION EN FIRMA), ';
                                    $especialidades[$space]->status = 'EN CAPTURA';
                                    $modInstructor->status = 'EN CAPTURA';
                                } else {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (REVALIDACION EN FIRMA), ';
                                    $retorno_firma = TRUE;
                                }
                                    unset($especialidades[$space]->hvalidacion[count($cadwell->hvalidacion) - 1]);
                            break;
                            case 'BAJA EN FIRMA':
                                if($request->tipo_envio == 'captura') {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (BAJA), ';
                                    $especialidades[$space]->status = 'EN CAPTURA';
                                    $modInstructor->status = 'EN CAPTURA';
                                } else {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (BAJA), ';
                                    $retorno_firma = TRUE;
                                }
                                unset($especialidades[$space]->hvalidacion[count($cadwell->hvalidacion) - 1]);
                            break;
                            case 'EN FIRMA':
                                if($request->tipo_envio == 'captura') {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (EN FIRMA), ';
                                    $especialidades[$space]->status = 'EN CAPTURA';
                                    $modInstructor->status = 'EN CAPTURA';
                                } else {
                                    $movimiento = $movimiento. $especialidad->nombre . ' (EN FIRMA), ';
                                    $retorno_firma = TRUE;
                                }

                                if(isset($especialidades[$space]->hvalidacion)) {
                                    unset($especialidades[$space]->hvalidacion[count($cadwell->hvalidacion) - 1]);
                                }
                            break;
                        }
                        if(isset($especialidades[$space]->hvalidacion) && $especialidades[$space]->hvalidacion == []) {
                            $especialidades[$space]->hvalidacion = null;
                        }
                    }
                }
                $modInstructor->data_especialidad = $especialidades;
            }

            if($chk_mod_perfil == FALSE && $chk_mod_esp == FALSE)
            {
                $movimiento = $movimiento . 'de la informacion general del instructor ';
            }

            if($modInstructor->status != 'VALIDADO' && $retorno_firma == FALSE)
            {
                $modInstructor->status = 'RETORNO';
            }

            if($request->tipo_envio == 'captura') {
                $modInstructor->status = 'RETORNO';
            }
            $movimiento = $movimiento . 'con la observacion: ' . $request->observacion_retorno;

            $historico = new instructor_history;
            $historico->id_instructor = $modInstructor->id;
            $historico->id_user = $userId;
            $historico->movimiento = $movimiento;
            $historico->status = $modInstructor->status;
            $historico->turnado = $modInstructor->turnado;
            $historico->nrevision = $modInstructor->nrevision;
            $historico->save();
            $modInstructor->save();
        }

        return redirect('/prevalidacion/instructor')->with('success','REGISTROS RETORNADOS A UNIDAD CORRECTAMENTE');
    }

    public function validado_save(Request $request)
    {
        $userId = Auth::user()->id;

        $instructor = instructor::find($request->idins);
        $instructor->status = "VALIDADO";
        $instructor->estado = TRUE;

        //Creacion de el numero de control
        $uni = substr($instructor->clave_unidad, -3, 2) * 1 . substr($instructor->clave_unidad, -1);
        $now = Carbon::now();
        $year = substr($now->year, -2);
        $rfcpart = substr($instructor->rfc, 0, 10);
        $numero_control = $uni.$year.$rfcpart;
        $instructor->numero_control = trim($numero_control);
        $instructor->save();

            return redirect()->route('instructor-inicio')
            ->with('success','Instructor VALIDADO');
    }

    public function solicitud_baja(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $instructor = pre_instructor::find($request->idbajains);
        if(!isset($instructor))
        {
            $instructorof = instructor::find($request->idbajains);
            // dd($instructor);
            $instructor = new pre_instructor();
            $instructor  = $this->guardado_ins_model($instructor, $instructorof, $request->idbajains);
            $instructor->id_oficial = $instructor->id;
            $instructor->registro_activo = TRUE;
            $instructor->save();
        }
        $nrev = $this->new_revision($request->idbajains);
        $perfiles = $this->make_collection($instructor->data_perfil);
        $especialidades = $this->make_collection($instructor->data_especialidad);
        $movimiento = 'Solicitud de baja de instructor con motivo: ' .  $request->motivo_baja;

        foreach($especialidades AS $key => $cadwell)
        {
            $especialidades[$key]->status = 'BAJA EN PREVALIDACION';
            $especialidades[$key]->fecha_solicitud = null;
            $especialidades[$key]->fecha_baja = null;
            $especialidades[$key]->activo = TRUE;
        }
        $instructor->data_especialidad = $especialidades;

        foreach($perfiles AS $rise => $moist)
        {
            $perfiles[$rise]->status = 'BAJA EN PREVALIDACION';
        }

        $instructor->data_perfil = $perfiles;
        $instructor->status = 'BAJA EN PREVALIDACION';
        $instructor->turnado = 'DTA';
        $instructor->motivo = $request->motivo_baja;
        $instructor->registro_activo = TRUE;
        $instructor->nrevision = $nrev;
        $instructor->save();

        $historico = new instructor_history;
        $historico->id_instructor = $instructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $instructor->status;
        $historico->turnado = $instructor->turnado;
        $historico->nrevision = $instructor->nrevision;
        $historico->save();
        // dd($historico);

        return redirect('/instructor/ver/'.$instructor->id)
            ->with('success','Solicitud de Baja Solicitada con Numero de Revision: ' . $instructor->nrevision);
    }

    public function baja_instructor(Request $request)
    {
        // dd($request);
        $movimientoesp = $movimientoper = NULL;
        $saveInstructor = pre_instructor::find($request->idinsvalidarbaja);
        $instructorOficial = instructor::find($request->idinsvalidarbaja);
        $userId = Auth::user()->id;
        $especialidades = $this->make_collection($saveInstructor->data_especialidad);
        $perfiles = $this->make_collection($saveInstructor->data_perfil);

        $solva = $request->file('memovalidacionbaja'); # obtenemos el archivo
        $url = $this->pdf_upload($solva, $request->idinsvalidarbaja, 'bajainstructor'); # invocamos el método

        foreach ($especialidades AS $key => $cadwell)
        {
            if($cadwell->status == 'BAJA EN FIRMA' && $cadwell->new == false)
            {
                $upd = especialidad_instructor::find($cadwell->id);
                $hvalidacion = $cadwell->hvalidacion;
                $end = count($hvalidacion) - 1;
                $hvalidacion[$end]['memo_baja'] = $cadwell->memorandum_baja;
                $hvalidacion[$end]['fecha_baja'] = $cadwell->fecha_baja;
                $hvalidacion[$end]['arch_baja'] = $url;
                $upd->hvalidacion = $especialidades[$key]->hvalidacion = $hvalidacion;
                $upd->activo = $especialidades[$key]->activo = FALSE;
                $upd->status = $especialidades[$key]->status = 'BAJA';
                $especialidad = especialidad::WHERE('id', '=', $cadwell->especialidad_id)->SELECT('nombre')->FIRST();
                $movimientoesp = $movimientoesp . $especialidad->nombre . ',  ';

                $upd->save();
            }
        }
        $saveInstructor->data_especialidad = $especialidades;

        foreach ($perfiles AS $rise => $moist)
        {
            if($moist->status == 'BAJA EN FIRMA')
            {
                $update = instructorPerfil::find($moist->id);
                $update->status = $perfiles[$rise]->status = 'BAJA';
                $update->save();
            }
        }
        $saveInstructor->data_perfil = $perfiles;

        $movimiento = 'Baja cargada con numero de memo: ' . $upd->memorandum_baja . ' ';

        $movimiento = $movimiento . 'y especialidades a impartir: ' . $movimientoesp;

        $saveInstructor->turnado = 'UNIDAD';
        $saveInstructor->lastUserId = Auth::user()->id;
        $saveInstructor->registro_activo = FALSE;
        if($saveInstructor->status == 'BAJA EN FIRMA')
        {
            $instructorOficial->status = $saveInstructor->status = 'BAJA';
        }
        else
        {
            $instructorOficial->status = $saveInstructor->status = 'VALIDADO';
        }

        $instructorOficial->estado = FALSE;
        $saveInstructor->estado = FALSE;

        $saveInstructor->save();
        $instructorOficial->save();

        $historico = new instructor_history;
        $historico->id_instructor = $saveInstructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $saveInstructor->status;
        $historico->turnado = $saveInstructor->turnado;
        $historico->nrevision = $saveInstructor->nrevision;
        $historico->save();

        return redirect('/prevalidacion/instructor')->with('success','INSTRUCTOR DADO DE BAJA CORRECTAMENTE');
    }

    public function solicitud_reactivacion(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $instructor = pre_instructor::find($request->idreacins);

        $extract_inf = instructor::find($request->idreacins);
        if(!isset($instructor))
        {
            $pre_instructor = new pre_instructor();
            $instructor  = $this->guardado_ins_model($pre_instructor, $extract_inf, $request->idreacins);
            $pre_instructor->id_oficial = $request->idreacins;
            $pre_instructor->archivo_ine = $extract_inf->archivo_ine;
            $pre_instructor->archivo_domicilio = $extract_inf->archivo_domicilio;
            $pre_instructor->archivo_curp = $extract_inf->archivo_curp;
            $pre_instructor->archivo_alta = $extract_inf->archivo_alta;
            $pre_instructor->archivo_bancario = $extract_inf->archivo_bancario;
            $pre_instructor->archivo_fotografia = $extract_inf->archivo_fotografia;
            $pre_instructor->archivo_estudios = $extract_inf->archivo_estudios;
            $pre_instructor->archivo_otraid = $extract_inf->archivo_otraid;
            $pre_instructor->archivo_rfc = $extract_inf->archivo_rfc;
            $pre_instructor->numero_control = $extract_inf->numero_control;
            $pre_instructor->registro_activo = TRUE;
            $instructor->save();
        }

        $especialidades = $this->make_collection($instructor->data_especialidad);
        $perfiles = $this->make_collection($instructor->data_perfil);
        $movimiento = 'Solicitud de reactivacion de instructor';
        $nrev = $this->new_revision($request->idreacins);

        foreach($perfiles AS $key => $cadwell)
        {
            $perfiles[$key]->status = 'REACTIVACION EN CAPTURA';
        }

        foreach($especialidades AS $rise => $moist)
        {
            $especialidades[$rise]->status = 'REACTIVACION EN CAPTURA';
        }

        $instructor->data_perfil = $perfiles;
        $instructor->data_especialidad = $especialidades;
        $instructor->status = 'EN CAPTURA';
        $instructor->turnado = 'UNIDAD';
        $instructor->registro_activo = TRUE;
        $instructor->motivo = NULL;
        $instructor->nrevision = $nrev;
        $instructor->save();

        $historico = new instructor_history;
        $historico->id_instructor = $instructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $instructor->status;
        $historico->turnado = $instructor->turnado;
        $historico->nrevision = $instructor->nrevision;
        $historico->save();
        // dd($historico);

        return redirect('/instructor/ver/'.$instructor->id)
            ->with('success','Solicitud de Reactivación Solicitada con Numero de Revision: ' . $instructor->nrevision);
    }

    public function solicitud_baja_especialidad(Request $request)
    {
        // dd($request);
        $userId = Auth::user()->id;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $especialidad = especialidad_instructor::find($request->idbajaespe);
        $nomesp = especialidad::WHERE('id', '=', $especialidad->especialidad_id)->SELECT('nombre')->FIRST();
        $instructor = pre_instructor::find($especialidad->id_instructor);
        $especialidades = $this->make_collection($instructor->data_especialidad);
        foreach($especialidades AS $key => $cadwell)
        {
            if($cadwell->id == $request->idbajaespe)
            {
                $especialidades[$key]->status = 'BAJA EN CAPTURA';
            }
        }
        $movimiento = 'Solicitud de baja en la especialidad ' . $nomesp->nombre . ' con motivo: ' .  $request->motivo_baja_especialidad;
        $nrev = $this->new_revision($instructor->id);
        if($nrev != $instructor->nrevision)
        {
            $instructor->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $instructor->nrevision;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructor->nrevision;
        }

        $instructor->data_especialidad = $especialidades;
        $instructor->status = 'EN CAPTURA';
        $instructor->motivo = $request->motivo_baja_especialidad;
        $instructor->registro_activo = TRUE;
        $instructor->save();

        $historico = new instructor_history;
        $historico->id_instructor = $instructor->id;
        $historico->id_user = $userId;
        $historico->movimiento = $movimiento;
        $historico->status = $instructor->status;
        $historico->turnado = $instructor->turnado;
        $historico->save();
        // dd($historico);

        return redirect('/instructor/ver/'.$instructor->id)
            ->with('success', $nrevisiontext);
    }

    public function editar($id)
    {
        $instructor = new instructor();
        $datains = instructor::WHERE('id', '=', $id)->FIRST();
        $ec = DB::TABLE('estado_civil')->SELECT('id','nombre')->GET();
        $idest = DB::TABLE('estados')->SELECT('id')->WHERE('nombre', '=', $datains->entidad)->FIRST();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->GET();
        $municipios = DB::TABLE('tbl_municipios')->SELECT('id','muni')
                        ->WHERE('id_estado', '=', $idest->id)->ORDERBY('muni', 'ASC')->GET();
        $localidades = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.localidad','tbl_localidades.clave')
                            ->JOIN('tbl_municipios','tbl_municipios.clave', '=','tbl_localidades.clave_municipio')
                            ->WHERE('tbl_municipios.muni', '=', $datains->municipio)
                            ->GET();


        return view('layouts.pages.editarinstructor', compact('datains','estados','municipios','localidades','ec'));
    }

    public function ver_instructor($id)
    {
        $datainstructor = pre_instructor::find($id);
        $municipios_nacimiento = $localidades_nacimiento = NULL;
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
        $roluser = DB::TABLE('role_user')->WHERE('user_id', '=', Auth::user()->id)->FIRST();
        $lista_civil = estado_civil::WHERE('id', '!=', '0')->ORDERBY('nombre', 'ASC')->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->ORDERBY('nombre','ASC')->GET();
        $instructor_perfil = new InstructorPerfil();
        $bancos = Banco::all();
        $lista_regimen = DB::Table('cat_conceptos')->Where('tipo', 'REGIMEN')->Where('activo', TRUE)->GET();
        $paises = pais::all();

        if(!isset($datainstructor) || $datainstructor->registro_activo == FALSE)
        {
            $datainstructor = $subproyectos = NULL;
            $datainstructor = instructor::WHERE('id', '=', $id)->FIRST();
            $perfil = $instructor_perfil->WHERE('numero_control', '=', $id)->GET();
            $validado = $instructor_perfil->SELECT('especialidades.nombre', 'especialidad_instructores.id as espinid',
                'especialidad_instructores.observacion', 'especialidad_instructores.id AS especialidadinsid',
                'especialidad_instructores.memorandum_validacion','especialidad_instructores.criterio_pago_id',
                'especialidad_instructores.fecha_validacion','especialidad_instructores.activo',
                'especialidad_instructores.hvalidacion','especialidad_instructores.status')
                ->WHERE('instructor_perfil.numero_control', '=', $id)
                ->RIGHTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id','=','instructor_perfil.id')
                ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                ->GET();
        }
        else if ($datainstructor->registro_activo == TRUE)
        {

            $perfil = $this->make_collection($datainstructor->data_perfil);
            $validado = $this->make_collection($datainstructor->data_especialidad);
            foreach($validado as $key => $ges)
            {
                $lista = null;
                if(isset($ges->hvalidacion))
                {
                    $ges->hvalidacion = json_encode($ges->hvalidacion);
                }
                $nomespec = DB::TABLE('especialidades')->SELECT('nombre')->WHERE('id',$ges->especialidad_id)->FIRST();
                $validado[$key]->nombre = $nomespec->nombre;
                $validado[$key]->espinid = $ges->id;

                if(!isset($ges->memorandum_validacion))
                {
                    $validado[$key]->memorandum_validacion = NULL;
                    $validado[$key]->fecha_validacion = NULL;
                }

                if(isset($ges->cursos_impartir))
                {
                    if(!is_array($ges->cursos_impartir)) {
                        $ges->cursos_impartir = json_decode($ges->cursos_impartir, true);
                    }
                    $cursos = curso::SELECT('nombre_curso')->WHEREIN('id', $ges->cursos_impartir)->GET();
                    foreach($cursos as $llavesita => $ari)
                    {
                        if($llavesita == 0)
                        {
                            $lista = '<li>' . $ari->nombre_curso . '</li>';
                        }
                        else
                        {
                            $lista = $lista . '<li>' . $ari->nombre_curso . '</li>';
                        }
                    }
                    $validado[$key]->cursos_impartir = $lista;
                }

                // dd($validado[$key]->cursos_impartir);
            }
            // dd($validado);
        }
        $idest = DB::TABLE('estados')->WHERE('nombre','=',$datainstructor->entidad)->FIRST();
        $idestnac = DB::TABLE('estados')->WHERE('nombre','=',$datainstructor->entidad_nacimiento)->FIRST();
        $municipios = DB::TABLE('tbl_municipios')->SELECT('id','muni')->WHERE('id_estado', '=', $idest->id)
                        ->ORDERBY('muni','ASC')->GET();

        if(isset($idestnac->id))
        {
            $municipios_nacimiento = DB::TABLE('tbl_municipios')->SELECT('id','muni')->WHERE('id_estado', '=', $idestnac->id)
                            ->ORDERBY('muni','ASC')->GET();
        }

        $munix = DB::TABLE('tbl_municipios')->SELECT('clave', 'id_estado')
                ->WHERE('muni', '=', $datainstructor->municipio)
                ->WHERE('estados.nombre',$datainstructor->entidad)
                ->JOIN('estados',DB::raw('CAST(estados.id AS varchar)'),'tbl_municipios.id_estado')
                ->FIRST();
        $munixnac = DB::TABLE('tbl_municipios')->SELECT('clave', 'id_estado')
                ->WHERE('muni', '=', $datainstructor->municipio_nacimiento)
                ->WHERE('estados.nombre',$datainstructor->entidad_nacimiento)
                ->JOIN('estados',DB::raw('CAST(estados.id AS varchar)'),'tbl_municipios.id_estado')
                ->FIRST();

        if($munix != NULL)
        {
            $localidades = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.clave','localidad')
                            ->WHERE('tbl_localidades.clave_municipio', '=', $munix->clave)
                            ->WHERE('tbl_localidades.id_estado', '=', $munix->id_estado)
                            ->ORDERBY('tbl_localidades.localidad', 'ASC')
                            ->GET();
        }
        if($munixnac != NULL)
        {
            $localidades_nacimiento = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.clave','localidad')
                            ->WHERE('tbl_localidades.clave_municipio', '=', $munixnac->clave)
                            ->WHERE('tbl_localidades.id_estado', '=', $munixnac->id_estado)
                            ->ORDERBY('tbl_localidades.localidad', 'ASC')
                            ->GET();
        }

        $nrevisiones = DB::TABLE('especialidad_instructores')->SELECT('instructores.nrevision')
                        ->JOIN('instructores','instructores.id', '=', 'especialidad_instructores.id_instructor')
                        ->WHERE('unidad_solicita', '=', $userunidad->ubicacion)
                        ->WHEREIN('especialidad_instructores.status', ['EN CAPTURA','RETORNO'])
                        ->WHERE('nrevision', '!=', NULL)
                        ->GROUPBY('nrevision')
                        ->ORDERBY('nrevision','ASC')
                        ->GET();

        $nrevisionlast = DB::TABLE('especialidad_instructores')->SELECT('instructores.nrevision')
                        ->JOIN('instructores','instructores.id', '=', 'especialidad_instructores.id_instructor')
                        ->WHERE('unidad_solicita', '=', $userunidad->ubicacion)
                        ->WHEREIN('especialidad_instructores.status', ['EN CAPTURA','RETORNO'])
                        ->WHERE('nrevision', '!=', NULL)
                        ->GROUPBY('nrevision')
                        ->ORDERBY('nrevision','DESC')
                        ->FIRST();

        if(!isset($nrevisionlast))
        {
            $nrevisionlast = 0;
        }

        // verificacion de datos alfa. si es nulo o se construye array nulo para evitar errores y otros arreglos de json a array
        if($datainstructor->instructor_alfa != true) {
            $datainstructor->datos_alfa = $this->instructor_alfa_nulo();
        }

        if(!is_array($datainstructor->exp_docente)) {
            $datainstructor->exp_docente = json_decode($datainstructor->exp_docente);
        }
        if(!is_array($datainstructor->exp_laboral)) {
            $datainstructor->exp_laboral = json_decode($datainstructor->exp_laboral);
        }
        //fin
        return view('layouts.pages.verinstructor', compact('perfil','validado','id', 'datainstructor','lista_civil','estados','municipios','localidades','municipios_nacimiento','localidades_nacimiento','nrevisionlast','userunidad','nrevisiones','roluser','bancos','lista_regimen','paises'));
    }

    public function save_ins(Request $request)
    {
        // dd($request);
        $arrperf = $arresp = [];
        $modInstructor = NULL;
        $userId = Auth::user()->id;
        $modInstructor = pre_instructor::find($request->id);
        $extract_inf = instructor::find($request->id);
        if(!isset($modInstructor))
        {
            $modInstructor = new pre_instructor();
            $modInstructor->id_oficial = $request->id;
            $modInstructor->archivo_ine = $extract_inf->archivo_ine;
            $modInstructor->archivo_domicilio = $extract_inf->archivo_domicilio;
            $modInstructor->archivo_curp = $extract_inf->archivo_curp;
            $modInstructor->archivo_alta = $extract_inf->archivo_alta;
            $modInstructor->archivo_bancario = $extract_inf->archivo_bancario;
            $modInstructor->archivo_fotografia = $extract_inf->archivo_fotografia;
            $modInstructor->archivo_estudios = $extract_inf->archivo_estudios;
            $modInstructor->archivo_otraid = $extract_inf->archivo_otraid;
            $modInstructor->archivo_rfc = $extract_inf->archivo_rfc;
            $modInstructor->numero_control = $extract_inf->numero_control;
            $modInstructor->registro_activo = TRUE;
        }
        $pre_instructor = $this->guardado_ins($modInstructor, $request, $request->id);
        $pre_instructor->registro_activo = TRUE;
        $new = $request->apellido_paterno . ' ' . $request->apellido_materno . ' ' . $request->nombre;
        $old = $pre_instructor->apellidoPaterno . ' ' . $pre_instructor->apellidoMaterno . ' ' . $pre_instructor->nombre;

        $extract_inf->save();


        if($pre_instructor->status == 'RETORNO' || $pre_instructor->status == 'VALIDADO')
        {
            $pre_instructor->status = 'EN CAPTURA';
        }
        //PROCESO DE PREVENCION EN CAMBIO DE NUMERO DE REVISIONES VACIOS
        $pre_instructor->save();
        if($pre_instructor->turnado == 'UNIDAD')
        {
            $nrev = $this->new_revision($pre_instructor->id);
            if($nrev != $pre_instructor->nrevision)
            {
                $pre_instructor->nrevision = $nrev;
                $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $pre_instructor->nrevision;
            }
            else
            {
                $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $pre_instructor->nrevision;
            }
        }
        $pre_instructor->save();

        Inscripcion::where('instructor', '=', $old)->update(['instructor' => $new]);
        tbl_curso::where('nombre', '=', $old)->update(['nombre' => $new]);
        tbl_curso::where('id_instructor', '=', $request->id)->update(['curp' => $request->curp]);


        return redirect()->route('instructor-inicio')
                ->with('success', $nrevisiontext);
    }

    public function expdoc_save(Request $request) //expdoc_save(Request $request)
    {
        $ww = 'wok';
        $instructorupd = pre_instructor::find($request->idins);
        if(!isset($instructorupd))
        {
            $instructor = instructor::find($request->idins);
            // dd($instructor);
            $pre_instructor = new pre_instructor();
            $instructorupd  = $this->guardado_ins_model($pre_instructor, $instructor, $request->idins);
            $instructorupd->id_oficial = $instructor->id;
        }

        if(isset($instructorupd->exp_docente))
        {
            $new = count($instructorupd->exp_docente);
            $expdoc = $instructorupd->exp_docente;
            $expdoc[$new] = ['asignatura' => $request->asignatura,
                             'institucion' => $request->institucion,
                             'funcion' => $request->funcion,
                             'periodo' => $request->periodo];
            $new++;
        }
        else
        {
            $expdoc = [0 => ['asignatura' => $request->asignatura,
                             'institucion' => $request->institucion,
                             'funcion' => $request->funcion,
                             'periodo' => $request->periodo]];
            $new = 1;
        }

        $instructorupd->exp_docente = $expdoc;
        if($instructorupd->status == 'REACTIVACION EN CAPTURA')
        {
            $instructorupd->status = 'REACTIVACION EN CAPTURA';
        }
        else
        {
            $instructorupd->status = 'EN CAPTURA';
        }
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        $nrev = $this->new_revision($instructorupd->id);
        if($nrev != $instructorupd->nrevision)
        {
            $instructorupd->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $instructorupd->nrevision;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructorupd->nrevision;
        }

        $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delexpdocModal"
            data-id=' . "'" . '["'. $new . '","' . $request->idins . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';

        $respuesta = [
            'asignatura' => $request->asignatura,
            'institucion' => $request->institucion,
            'funcion' => $request->funcion,
            'periodo' => $request->periodo,
            'button' => $paw,
            'pos' => $new,
            'nrevisiontext' => $nrevisiontext
        ];

        $json=json_encode($respuesta);
        return $json;
    }

    public function expdoc_delete(Request $request)
    {
        $instructorupd = pre_instructor::find($request->idins);
        $key = 0;
        $new = [];

        foreach($instructorupd->exp_docente as $cadwell)
        {
            if($request->asignatura != $cadwell['asignatura'] && $request->institucion != $cadwell['institucion'] &&
               $request->funcion != $cadwell['funcion'] && $request->periodo != $cadwell['periodo'])
            {
                $new[$key] = ['asignatura' => $cadwell['asignatura'],
                              'institucion' => $cadwell['institucion'],
                              'funcion' => $cadwell['funcion'],
                              'periodo' => $cadwell['periodo']];
                $key++;
            }
        }

        $key = 1;
        $instructorupd->exp_docente = $new;
        $instructorupd->save();

        foreach($new as $pointer => $lex)
        {
            $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delexpdocModal"
            data-id=' . "'" . '["'. $key . '","' . $request->idins . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';

            $new[$pointer] = ['asignatura' => $lex['asignatura'],
                              'institucion' => $lex['institucion'],
                              'funcion' => $lex['funcion'],
                              'periodo' => $lex['periodo'],
                              'button' => $paw];

            $key++;
        }

        $json=json_encode($new);
        return $json;
    }

    public function explab_save(Request $request)
    {
        $instructorupd = pre_instructor::find($request->idins);
        if(!isset($instructorupd))
        {
            $instructor = instructor::find($request->idins);
            // dd($instructor);
            $pre_instructor = new pre_instructor();
            $instructorupd  = $this->guardado_ins_model($pre_instructor, $instructor, $request->idins);
            $instructorupd->id_oficial = $instructor->id;
        }

        if(isset($instructorupd->exp_laboral))
        {
            $new = count($instructorupd->exp_laboral);
            $explab = $instructorupd->exp_laboral;
            $explab[$new] = ['puesto' => $request->puesto,
                             'periodo' => $request->periodo,
                             'institucion' => $request->institucion];
            $new++;
        }
        else
        {
            $explab = [0 => ['puesto' => $request->puesto,
                             'periodo' => $request->periodo,
                             'institucion' => $request->institucion]];
            $new = 1;
        }



        $instructorupd->exp_laboral = $explab;
        if($instructorupd->status == 'REACTIVACION EN CAPTURA')
        {
            $instructorupd->status = 'REACTIVACION EN CAPTURA';
        }
        else
        {
            $instructorupd->status = 'EN CAPTURA';
        }
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        $nrev = $this->new_revision($request->idins);
        if($nrev != $instructorupd->nrevision)
        {
            $instructorupd->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $instructorupd->nrevision;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructorupd->nrevision;
        }

        $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delexplabModal"
            data-id=' . "'" . '["'. $new . '","' . $request->idins . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';

        $respuesta = [
            'puesto' => $request->puesto,
            'periodo' => $request->periodo,
            'institucion' => $request->institucion,
            'button' => $paw,
            'pos' => $new,
            'nrevisiontext' => $nrevisiontext
        ];

        $json=json_encode($respuesta);
        return $json;
    }

    public function explab_delete(Request $request)
    {
        $instructorupd = pre_instructor::find($request->idins);
        $key = 0;
        $new = [];

        foreach($instructorupd->exp_laboral as $cadwell)
        {
            if($request->puesto != $cadwell['puesto'] && $request->periodo != $cadwell['periodo'] && $request->institucion != $cadwell['institucion'])
            {
                $new[$key] = ['puesto' => $cadwell['puesto'],
                              'periodo' => $cadwell['periodo'],
                              'institucion' => $cadwell['institucion']];
                $key++;
            }
        }

        $key = 1;
        $instructorupd->exp_laboral = $new;
        $instructorupd->save();

        foreach($new as $pointer => $lex)
        {
            $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delexplabModal"
            data-id=' . "'" . '["'. $key . '","' . $request->idins . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';

            $new[$pointer] = ['puesto' => $lex['puesto'],
                              'periodo' => $lex['periodo'],
                              'institucion' => $lex['institucion'],
                              'button' => $paw];

            $key++;
        }

        $json=json_encode($new);
        return $json;
    }

    public function save_entrevista(Request $request)
    {
        // dd($request);
        $instructorupd = pre_instructor::find($request->idInstructorentrevista);
        if(!isset($instructorupd))
        {
            $instructor = instructor::find($request->idInstructorentrevista);
            // dd($instructor);
            $pre_instructor = new pre_instructor();
            $instructorupd  = $this->guardado_ins_model($pre_instructor, $instructor, $request->idInstructorentrevista);
            $instructorupd->id_oficial = $instructor->id;
        }

        $instructorupd->registro_activo = TRUE;
        $entrevista = ['1' => $request->Q1, '2' => $request->Q2, '3' => $request->Q3,
                       '4' => $request->Q4, '5' => $request->Q5, '6' => $request->Q6,
                       '7' => $request->Q7, '8' => $request->Q8, '9' => $request->Q9,
                       '10' => $request->Q10, '11' => $request->Q11, '12' => $request->Q12,
                       '13' => $request->Q13, '14' => $request->Q14, 'link' => null];
        $instructorupd->entrevista = $entrevista;
        if($instructorupd->status == 'REACTIVACION EN CAPTURA')
        {
            $instructorupd->status = 'REACTIVACION EN CAPTURA';
        }
        else
        {
            $instructorupd->status = 'EN CAPTURA';
        }
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        if($instructorupd->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idInstructorentrevista])
                            ->with('success','Entrevista Guardada Exitosamente');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idInstructorentrevista])
                        ->with('success', 'Entrevista Guardada Exitosamente');
        }
    }

    public function save_mod_entrevista(Request $request)
    {
        // dd($request);
        $instructorupd = pre_instructor::find($request->idInstructorentrevistamod);
        if(!is_array($instructorupd->entrevista)) {
            $instructorupd->entrevista = json_decode($instructorupd->entrevista, true);
        }
        $entrevista = ['1' => $request->MQ1, '2' => $request->MQ2, '3' => $request->MQ3,
                       '4' => $request->MQ4, '5' => $request->MQ5, '6' => $request->MQ6,
                       '7' => $request->MQ7, '8' => $request->MQ8, '9' => $request->MQ9,
                       '10' => $request->MQ10, '11' => $request->MQ11, '12' => $request->MQ12,
                       '13' => $request->MQ13, '14' => $request->MQ14, 'link' => $instructorupd->entrevista['link']];
        $instructorupd->entrevista = $entrevista;
        $instructorupd->status = 'EN CAPTURA';
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        if($instructorupd->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idInstructorentrevistamod])
                            ->with('success','Entrevista Modificada Exitosamente');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idInstructorentrevistamod])
                        ->with('success', 'Entrevista Modificada Exitosamente');
        }
    }

    public function entrevista_upload(Request $request)
    {
        // dd($request);
        $instructorupd = pre_instructor::find($request->idInstructorentrevistaupd);
        if(!isset($instructorupd))
        {
            $instructor = instructor::find($request->idInstructorentrevistaupd);
            // dd($instructor);
            $pre_instructor = new pre_instructor();
            $instructorupd  = $this->guardado_ins_model($pre_instructor, $instructor, $request->idInstructorentrevistaupd);
            $instructorupd->id_oficial = $instructor->id;
        }
        $entrevista = $instructorupd->entrevista;
        $archivo = $request->file('doc_entrevista'); # obtenemos el archivo
        $urlentrevista = $this->pdf_upload($archivo, $request->idInstructorentrevistaupd, 'entrevista'); # invocamos el método
        if(!is_array($entrevista)) {
            $entrevista = json_decode($entrevista, true);
        }
        $entrevista['link'] = $urlentrevista; # guardamos el path
        $instructorupd->entrevista = $entrevista;
        $instructorupd->status = 'EN CAPTURA';
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        if($instructorupd->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idInstructorentrevistaupd])
                            ->with('success','Entrevista Subida Exitosamente');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idInstructorentrevistaupd])
                        ->with('success', 'Entrevista Subida Exitosamente');
        }
    }

    public function curriculum_upload(Request $request)
    {
        // dd($request);
        $instructorupd = pre_instructor::find($request->idInstructorcurriculumupd);
        $archivo = $request->file('doc_curriculum'); # obtenemos el archivo
        $urlcurriculum = $this->pdf_upload($archivo, $request->idInstructorcurriculumupd, 'Curriculum'); # invocamos el método
        $curriculum = $urlcurriculum; # guardamos el path
        $instructorupd->curriculum = $curriculum;
        $instructorupd->status = 'EN CAPTURA';
        $instructorupd->registro_activo = TRUE;
        $instructorupd->save();

        if($instructorupd->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idInstructorcurriculumupd])
                            ->with('success','Curriculum, Subido Exitosamente');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idInstructorcurriculumupd])
                        ->with('success', 'Curriculum, Subido Exitosamente');
        }
    }

    public function perfilinstructor_save(Request $request)
    {
        $userId = Auth::user()->id;
        $arrinsert = $arrtemp = array();
        $check = DB::TABLE('instructor_perfil')->SELECT('instructor_perfil.*')
                                    ->WHERE('instructor_perfil.numero_control', '=', $request->idInstructor)
                                    ->ORWHERE('instructor.data_perfil', '!=', NULL)
                                    ->JOIN('instructor', 'instructor.id', '=', 'instructor_perfil.numero_control')
                                    ->FIRST();
        $instructor = pre_instructor::WHERE('id', '=', $request->idInstructor)->FIRST();
        if ($check == NULL)
        {
            $exist = 'FALSO';
        }
        else
        {
            $exist = 'TRUE';
        }
        // dd($status);

        #proceso de guardado en pre instructor
        $arrtemp = [
            'grado_profesional' => trim($request->grado_prof),
            'area_carrera' => trim($request->area_carrera), //
            'carrera' => $request->carrera,
            'estatus' => trim($request->estatus), //
            'pais_institucion' => trim($request->institucion_pais), //
            'entidad_institucion' => trim($request->institucion_entidad), //
            'ciudad_institucion' => trim($request->institucion_ciudad),
            'nombre_institucion' => trim($request->institucion_nombre),
            'fecha_expedicion_documento' => trim($request->fecha_documento), //
            'periodo' => $request->periodo,
            'folio_documento' => trim($request->folio_documento), //
            'periodo' => trim($request->periodo),
            'cursos_recibidos' => trim($request->cursos_recibidos),
            'capacitador_icatech' => trim($request->capacitador_icatech),
            'recibidos_icatech' => trim($request->recibidos_icatech),
            'cursos_impartidos' => trim($request->cursos_impartidos),
            'numero_control' => trim($request->idInstructor),
            'lastUserId' => $userId,
            'status' => 'EN CAPTURA',
            'new' => TRUE
        ];
        if (!isset($instructor->data_perfil))
        {
            $arrtemp['id'] = '0';
            $pid = 0;
            $arrinsert[0] = $arrtemp;
        }
        else
        {
            $pid = count($instructor->data_perfil);
            $arrtemp['id'] = $pid;
            $arrinsert = $instructor->data_perfil;
            array_push($arrinsert, $arrtemp);
        }

        $instructor->data_perfil = $arrinsert;

        $nrev = $this->new_revision($request->idInstructor);
        if($nrev != $instructor->nrevision)
        {
            $instructor->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $nrev;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructor->nrevision;
        }

        $instructor->lastUserId = Auth::user()->id;
        $instructor->status = 'EN CAPTURA';
        $instructor->registro_activo = TRUE;
        $instructor->save();

        $cue = '<button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="MODIFICAR REGISTRO"
        data-toggle="modal"
        data-placement="top"
        data-target="#modperprofModal"
        data-id=' . "'" . '["' . $request->grado_prof . '","' . $request->area_carrera . '","' . $request->carrera . '","'. $request->estatus . '",
                "' . $request->pais_institucion . '",
                "' . $request->id . '","' . $request->idInstructor . '","' . $request->row . '"]' . "'" . '> <i class="fas fa-pencil-alt" aria-hidden="true"></i> </button>';
        $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delperprofModal"
            data-id=' . "'" . '["'. $pid . '","' . $request->row . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';
        // $perfilInstructor->button = $cue;
        $respuesta = [
            'id' => $pid,
            'grado_profesional' => $request->grado_prof,
            'area_carrera' => $request->area_carrera,
            'estatus' => $request->estatus,
            'status' => $request->status,
            'button' => $cue,
            'button2' => $paw,
            'exist' => $exist,
            'nrevisiontext' => $nrevisiontext
        ];

        $json=json_encode($respuesta);
        return $json;

    }

    public function modperfilinstructor_save(Request $request)
    {
        $userId = Auth::user()->id;
        $arrmod = array();
        $instructor = pre_instructor::WHERE('id', '=', $request->idInstructor)->FIRST();
        $arrtemp = $instructor->data_perfil;
        foreach($arrtemp as $key => $cadwell)
        {
            if($cadwell['id'] == $request->idperfprof)
            {
                $arrmod = $cadwell;
                #proceso de guardado
                $arrmod['grado_profesional'] = trim($request->grado_prof); //
                $arrmod['area_carrera'] = trim($request->area_carrera); //
                $arrmod['carrera'] = $request->carrera;
                $arrmod['estatus'] = trim($request->estatus); //
                $arrmod['pais_institucion'] = trim($request->institucion_pais); //
                $arrmod['entidad_institucion'] = trim($request->institucion_entidad); //
                $arrmod['ciudad_institucion'] = trim($request->institucion_ciudad);
                $arrmod['nombre_institucion'] = trim($request->institucion_nombre);
                $arrmod['fecha_expedicion_documento'] = trim($request->fecha_documento); //
                $arrmod['periodo'] = $request->periodo;
                $arrmod['folio_documento'] = trim($request->folio_documento); //
                $arrmod['cursos_recibidos'] = trim($request->cursos_recibidos);
                $arrmod['capacitador_icatech'] = trim($request->capacitador_icatech);
                $arrmod['recibidos_icatech'] = trim($request->recibidos_icatech);
                $arrmod['cursos_impartidos'] = trim($request->cursos_impartidos);
                $arrmod['numero_control'] = trim($request->idInstructor);
                $arrmod['lastUserId'] = $userId;

                if($cadwell['status'] == 'EN CAPTURA')
                {
                    $arrmod['status'] = 'EN CAPTURA';
                }
                else if($cadwell['status'] == 'REACTIVACION EN CAPTURA')
                {
                    $arrtemp[$key]['status'] = 'REACTIVACION EN CAPTURA';
                }
                else
                {
                    if ($cadwell['new'] == false) {
                        $arrmod['status'] = 'REVALIDACION EN CAPTURA';
                    } else {
                        $arrmod['status'] = 'EN CAPTURA';
                    }
                }
                if ($arrmod != '[]')
                {
                    $arrtemp[$key] = $arrmod;
                    $instructor->data_perfil = $arrtemp;
                }
                break;
            }
        }

        $nrev = $this->new_revision($request->idInstructor);
        if($nrev != $instructor->nrevision)
        {
            $instructor->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $nrev;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructor->nrevision;
        }

        $instructor->lastUserId = Auth::user()->id;
        $instructor->status = 'EN CAPTURA';
        $instructor->registro_activo = TRUE;
        $instructor->save();


        $cue = '<button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="MODIFICAR REGISTRO"
        data-toggle="modal"
        data-placement="top"
        data-target="#modperprofModal"
        data-id=' . "'" . '["' . $request->grado_prof . '","' . $request->area_carrera . '","' . $request->estatus . '",
                "' . $request->institucion_pais . '","' . $request->institucion_entidad . '","' . $request->institucion_ciudad . '",
                "' . $request->institucion_nombre . '","' . $request->fecha_documento . '","' . $request->folio_documento . '",
                "' . $request->cursos_recibidos . '","' . $request->capacitador_icatech . '","' . $request->recibidos_icatech . '",
                "' . $request->cursos_impartidos . '","' . $request->experiencia_laboral . '","' . $request->experiencia_docente . '",
                "' . $request->idperfprof . '","' . $request->numero_control . '","' . $request->pos . '"]' . "'" . '> <i class="fas fa-pencil-alt" aria-hidden="true"></i> </button>';

        $paw = '<button type="button" class="btn btn-warning mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="ELIMINAR REGISTRO"
            data-toggle="modal"
            data-placement="top"
            data-target="#delperprofModal"
            data-id=' . "'" . '["'. $request->idperfprof . '","' . $request->pos . '"]' . "'" . '>
            <i class="fa fa-eraser" aria-hidden="true"></i>
            </button>';

        $bview = '<button type="button" class="btn mr-sm-4 mt-3 btn-circle m-1 btn-circle-sm" style="color: white;" title="VER REGISTRO"
        data-toggle="modal"
        data-placement="top"
        data-target="#verperfprofModal"
        data-id=' . "'" . $request->idperfprof . "'" . '>
            <i class="fa fa-eye" aria-hidden="true"></i>
        </button>';

        $respuesta = [
            'id' => $request->idperfprof,
            'grado_profesional' => $request->grado_prof,
            'area_carrera' => $request->area_carrera,
            'estatus' => $request->estatus,
            'status' => $request->status,
            'button' => $cue,
            'button2' => $paw,
            'button3' => $bview,
            'nrevision' => $instructor->nrevision,
            'nrevisiontext' => $nrevisiontext
        ];

        $json=json_encode($respuesta);
        return $json;

    }

    public function perfilinstructor_delete(Request $request)
    {
        $pre_instructor = pre_instructor::find($request->idins);
        $arrtemp = $pre_instructor->data_perfil;
        $arrnew = array();
        if ($request->new == TRUE)
        {
            $perf = 'gola';
            foreach($arrtemp as $key => $cadwell)
            {
                if($cadwell['id'] != $request->id)
                {
                    array_push($arrnew, $cadwell);
                }
            }
            $pre_instructor->data_perfil = $arrnew;
            $pre_instructor->save();
            $json=json_encode($perf);
        }
        else
        {
            $error = ['error' => 'error'];
            $json = $error;
        }
        return $json;
    }

    public function cursoimpartir_form(/*$id,*/ $idins)
    {
        $unidadUser = Auth::user()->unidad;
        $arrtemp = $arrnew = array();
        $pre_instructor = pre_instructor::WHERE('id',$idins)->FIRST();
        $data_especialidad = especialidad::WHERE('id', '!=', '0')->ORDERBY('nombre','asc')->GET();
        $memosol = DB::TABLE('especialidad_instructores')->SELECT('memorandum_solicitud','fecha_solicitud')
                                ->WHERE('id_instructor', '=', $idins)
                                ->WHERE('status', array('EN CAPTURA','RETORNO'))
                                ->FIRST();
        if( isset($pre_instructor->registro_activo) && $pre_instructor->registro_activo == TRUE)
        {
            foreach($pre_instructor->data_perfil as $cadwell)
            {
                $arrnew = (object) $cadwell;
                array_push($arrtemp, $arrnew);
            }
            $perfil = collect($arrtemp);
            // dd($perfil);
        }
        else
        {
            $perfil = InstructorPerfil::WHERE('numero_control', '=', $idins)->GET(['id','grado_profesional','area_carrera']);
        }
        $pago = criterio_pago::SELECT('id','perfil_profesional')->WHERE('id', '!=', '0')->WHERE('activo',TRUE)->GET();
        $data = tbl_unidades::SELECT('id','unidad','cct')->WHERE('id','!=','0')->GET();
        // dd($unidadUser);
        return view('layouts.pages.frmaddespecialidad', compact('idins','perfil','pago','data','data_especialidad','memosol','unidadUser'));
        /*}
        else
        {
            $esp = especialidad::SELECT('nombre')->WHERE('id', '=', $id)->FIRST();
            $mensaje = "Lo sentimos, la especialidad ".$esp->nombre." ya esta asociada a este instructor.";
            return redirect('instructor/add/curso-impartir/' . $idins)->withErrors($mensaje);
        }*/
    }

    public function espec_val_save(Request $request)
    {
        // dd($request);
        $arrtemp = array();
        $cursos_impartir = [];
        set_time_limit(0);
        $userId = Auth::user()->id;
        $instructor = pre_instructor::WHERE('id', '=', $request->idInstructor)->FIRST();
        $arrtemp = [
            'especialidad_id' => $request->idespec,
            'perfilprof_id' => $request->valido_perfil,
            'unidad_solicita' => $request->unidad_validacion,
            'memorandum_solicitud' => $request->memorandum,
            'fecha_solicitud' => $request->fecha_solicitud,
            'memorandum_modificacion' => $request->memorandum_modificacion,
            'observacion' => $request->observaciones,
            'criterio_pago_id' => $request->criterio_pago_instructor,
            'lastUserId' => $userId,
            'solicito' => $userId,
            'activo' => TRUE,
            'id_instructor' => $request->idInstructor,
            'status' => 'EN CAPTURA',
            'new' => TRUE
        ];

        foreach ($request->itemAdd as $keys=>$roshan)
        {
            if(!isset($cursos_impartir[0]))
            {
                $cursos_impartir[0] = $roshan['check_cursos'];
            }
            else
            {
                array_push($cursos_impartir,$roshan['check_cursos']);
            }
        }
        $arrtemp['cursos_impartir'] = $cursos_impartir;
        if (!isset($instructor->data_especialidad))
        {
            $arrtemp['id'] = '0';
            $pid = 0;
            $arrinsert[0] = $arrtemp;
        }
        else
        {
            $pid = count($instructor->data_especialidad);
            $arrtemp['id'] = $pid;
            $arrinsert = $instructor->data_especialidad;
            array_push($arrinsert, $arrtemp);
        }
        $instructor->data_especialidad = $arrinsert;

        $nrev = $this->new_revision($request->idInstructor);
        if($nrev != $instructor->nrevision)
        {
            $instructor->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $nrev;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructor->nrevision;
        }

        $instructor->status = 'EN CAPTURA';
        $instructor->registro_activo = TRUE;
        $instructor->nrevision = $nrev;
        $instructor->save();
        // dd($espec_save);

        if($instructor->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idInstructor])
                            ->with('success','Especialidad Para Impartir Agregada');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Especialidad Para Impartir Agregada');
        }

    }

    public function edit_especval($id,$idins)
    {
        $pre_instructor = pre_instructor::WHERE('id',$idins)->FIRST();
        if(isset($pre_instructor->registro_activo) && $pre_instructor->registro_activo == TRUE)
        {
            // dd($pre_instructor->data_especialidad);
            foreach($pre_instructor->data_especialidad as $cadwell)
            {
                if($cadwell['id'] == $id)
                {
                    $especvalid = (object) $cadwell;
                }

            }
            if(!isset($especvalid->cursos_impartir))
            {
                $arrt = array();
                $listacursos = DB::TABLE('especialidad_instructor_curso')->WHERE('id_especialidad_instructor',$id)->GET();

                foreach($listacursos as $cursos_id)
                {

                    if($cursos_id->activo == TRUE)
                    {
                        array_push($arrt, $cursos_id->curso_id);
                    }
                }
                $especvalid->cursos_impartir = $arrt;
            }
            $data_espec = $this->make_collection($pre_instructor->data_perfil);
            // dd($data_espec);
        }
        else
        {
            $especvalid = especialidad_instructor::WHERE('id', '=', $id)->FIRST();
            if(!isset($especvalid->cursos_impartir))
            {
                $arrt = array();
                $listacursos = DB::TABLE('especialidad_instructor_curso')->WHERE('id_especialidad_instructor',$id)->GET();
                foreach($listacursos as $cursos_id)
                {

                    if($cursos_id->activo == TRUE)
                    {
                        array_push($arrt, $cursos_id->curso_id);
                    }
                }
                $especvalid->cursos_impartir = $arrt;
            }
            $data_espec = InstructorPerfil::WHERE('numero_control', '=', $idins)->GET();
        }
        // dd($especvalid->solicito);
        $data_pago = criterio_pago::Where('id','!=','0')->Where('activo', TRUE)->GET();
        $data_unidad = tbl_unidades::WHERE('id', '!=', 0)->orderBy('unidad', 'ASC')->GET();
        $nomesp = especialidad::ALL();
        $catcursos = curso::SELECT('id', 'nombre_curso', 'modalidad', 'objetivo', 'costo', 'duracion', 'objetivo',
                'tipo_curso', 'id_especialidad', 'rango_criterio_pago_minimo', 'rango_criterio_pago_maximo')
                ->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                ->WHERE('estado', '=', TRUE)
                ->orderby('nombre_curso','asc')
                ->GET();
        $count = curso::SELECT(DB::raw("SUM(CASE WHEN id != 0 THEN 1 ELSE 0 END) as count"))
                ->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                ->FIRST();

        //  dd($listacursos);
        $listacursos = DB::table('cursos')->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                ->orderby('nombre_curso', 'asc')
                ->GET();

                $cursoupd = DB::table('cursos')->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                        ->orderby('nombre_curso', 'asc')
                        ->GET();
                $lstarr = count($listacursos) -1;

        //chequeo de json a array
        if(!is_array($especvalid->cursos_impartir)) {
            $especvalid->cursos_impartir = json_decode($especvalid->cursos_impartir, true);
        }
        // dd($especvalid);
        //fin
        // dd($listacursos);
        return view('layouts.pages.frmmodespecialidad', compact('especvalid','data_espec','data_pago','data_unidad', 'id','idins','nomesp', 'catcursos'));
    }

    public function especval_mod_save(Request $request)
    {
        // dd($request);
        set_time_limit(0);
        $cursos_impartir[] = NULL;
        $arrtemp = array();
        $userId = Auth::user()->id;
        $instructor = pre_instructor::WHERE('id', '=', $request->idins)->FIRST();
        if(!isset($instructor))
        {
            $newinstructor = instructor::find($request->idins);
            // dd($instructor);
            $pre_instructor = new pre_instructor();
            $instructor  = $this->guardado_ins_model($pre_instructor, $newinstructor, $request->idins);
            $instructor->id_oficial = $newinstructor->id;
        }

        $arrtemp = $instructor->data_especialidad;
        foreach($arrtemp as $key => $cadwell)
        {
            if($cadwell['id'] == $request->idespec)
            {
                $arrtemp[$key]['perfilprof_id'] = $request->valido_perfil;
                $arrtemp[$key]['observacion'] = $request->observaciones;
                $arrtemp[$key]['criterio_pago_id'] = $request->criterio_pago_mod;
                $arrtemp[$key]['lastUserId'] = $userId;
                $arrtemp[$key]['id_instructor'] = $request->idins;

                if($cadwell['status'] == 'EN CAPTURA')
                {
                    $arrtemp[$key]['status'] = 'EN CAPTURA';
                }
                else if($cadwell['status'] == 'REACTIVACION EN CAPTURA')
                {
                    $arrtemp[$key]['status'] = 'REACTIVACION EN CAPTURA';
                }
                else
                {
                    if($cadwell['new'] == false) {
                        $arrtemp[$key]['status'] = 'REVALIDACION EN CAPTURA';
                    } else {
                        $arrtemp[$key]['status'] = 'EN CAPTURA';
                    }
                }

                if(isset($request->itemEdit))
                {
                    foreach ($request->itemEdit as $keys=>$roshan)
                    {
                        if($cursos_impartir[0] == 0)
                        {
                            $cursos_impartir[0] = $roshan['check_cursos_edit'];
                        }
                        else
                        {
                            array_push($cursos_impartir,$roshan['check_cursos_edit']);
                        }
                    }
                }

                $arrtemp[$key]['cursos_impartir'] = $cursos_impartir;
                break;
            }
        }

        $instructor->status = 'EN CAPTURA';

        $instructor->lastUserId = Auth::user()->id;
        $instructor->data_especialidad = $arrtemp;

        $instructor->save();

        $nrev = $this->new_revision($request->idins);
        if($nrev != $instructor->nrevision)
        {
            $instructor->nrevision = $nrev;
            $nrevisiontext = 'Se ha generado un nuevo numero de revisión: ' . $nrev;
        }
        else
        {
            $nrevisiontext = 'Modificaciones agregadas al numero de revisión: ' . $instructor->nrevision;
        }

        $instructor->registro_activo = TRUE;
        $instructor->save();

        if($instructor->numero_control == 'Pendiente')
        {
            return redirect()->route('instructor-crear-p2',['id' => $request->idins])
                            ->with('success','Perfil Profesional Agregado');
        }
        else
        {
            return redirect()->route('instructor-ver', ['id' => $request->idins])
                        ->with('success', $nrevisiontext);
        }
    }

    public function especialidadimpartir_delete(Request $request)
    {
        $arrtemp = $arrnew = array();
        $perf = 'gola';
        $espec_imp = pre_instructor::WHERE('id', $request->idins)->FIRST();
        $arrnew = $espec_imp->data_especialidad;
        foreach($arrnew as $cadwell)
        {
            if($cadwell['id'] != $request->id && $cadwell['new'] != TRUE)
            {
                array_push($arrtemp, $cadwell);
            }
        }
        $espec_imp->data_especialidad = $arrtemp;
        $row = $espec_imp->save();

        $json=json_encode($arrtemp);
        return $json;
    }

    protected function new_revision($id)
    {
            $instructor = pre_instructor::find($id);
            $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion','cct')->WHERE('id', '=', Auth::user()->unidad)->FIRST();
            $nrevisionlast = pre_instructor::SELECT('nrevision', 'registro_activo')
                        // ->WHERE('clave_unidad', '=', $userunidad->cct) // quitar cct y poner las primeras letras de ubicacon y likear con nrevision
                        ->WHERE('nrevision', '!=', NULL)
                        ->WHERE('nrevision', 'LIKE', $userunidad->ubicacion[0].$userunidad->ubicacion[1].'%')
                        ->GROUPBY('nrevision','registro_activo')
                        ->ORDERBY('nrevision','DESC')
                        ->FIRST();
            $año = explode('-',$nrevisionlast->nrevision);
            $año_nrevision = explode('-', $instructor->nrevision);

            if(($instructor->status != 'EN CAPTURA' && $instructor->status != 'RETORNO' && $instructor->nrevision != $nrevisionlast->nrevision) || $instructor->registro_activo == FALSE  || $instructor->nrevision == NULL || $año_nrevision[1] != carbon::now()->year)
            {
                if(!isset($nrevisionlast) || $año[1] != carbon::now()->year)
                {
                    $nrev = $userunidad->ubicacion['0'] .  $userunidad->ubicacion['1'] . '-' .carbon::now()->year . '-0001';
                }
                else
                {
                    $aa = explode('-', $nrevisionlast->nrevision);
                    $pr = $aa[2] + 1;

                    switch(strlen($pr))
                    {
                        case 1:
                            $aa[2] = '000' . $pr;
                        break;
                        case 2:
                            $aa[2] = '00' . $pr;
                        break;
                        case 3:
                            $aa[2] = '0' . $pr;
                        break;
                    }

                    $nrev = implode('-',$aa);
                }
                $instructor->nrevision = $nrev;
                $instructor->save();
            }
            else
            {
                $nrev =  $instructor->nrevision;
            }
            return $nrev;
    }

    // public function add_cursoimpartir($id)
    // {
    //     $idins = $id;
    //     $data_especialidad = especialidad::where('id', '!=', '0')->orderBy('nombre','asc')->paginate(20);
    //     return view('layouts.pages.frmcursoimpartir', compact('data_especialidad','idins'));
    // }


    public function alta_baja($id)
    {
        $av = instructor::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        if($av == NULL)
        {
            $reform = instructor::find($id);
            $unidades = ['TUXTLA', 'TAPACHULA', 'COMITAN', 'REFORMA', 'TONALA', 'VILLAFLORES', 'JIQUIPILAS', 'CATAZAJA',
            'YAJALON', 'SAN CRISTOBAL', 'CHIAPA DE CORZO', 'MOTOZINTLA', 'BERRIOZABAL', 'PIJIJIAPAN', 'JITOTOL',
            'LA CONCORDIA', 'VENUSTIANO CARRANZA', 'TILA', 'TEOPISCA', 'OCOSINGO', 'CINTALAPA', 'COPAINALA',
            'SOYALO', 'ANGEL ALBINO CORZO', 'ARRIAGA', 'PICHUCALCO', 'JUAREZ', 'SIMOJOVEL', 'MAPASTEPEC',
            'VILLA CORZO', 'CACAHOATAN', 'ONCE DE ABRIL', 'TUXTLA CHICO', 'OXCHUC', 'CHAMULA', 'OSTUACAN',
            'PALENQUE'];

            $reform->unidades_disponible = $unidades;
            $reform->save();

            $av = instructor::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        }
        $available = $av->unidades_disponible;
        // dd($av);
        return view('layouts.pages.vstaltabajains', compact('id','available'));
    }

    public function alta_baja_save(Request $request)
    {
        $unidades = [];
        if($this->checkComparator($request->chk_tuxtla) == TRUE)
        {
            array_push($unidades, 'TUXTLA');
        }
        if($this->checkComparator($request->chk_tapachula) == TRUE)
        {
            array_push($unidades, 'TAPACHULA');
        }
        if($this->checkComparator($request->chk_comitan) == TRUE)
        {
            array_push($unidades, 'COMITAN');
        }
        if($this->checkComparator($request->chk_reforma) == TRUE)
        {
            array_push($unidades, 'REFORMA');
        }
        if($this->checkComparator($request->chk_tonala) == TRUE)
        {
            array_push($unidades, 'TONALA');
        }
        if($this->checkComparator($request->chk_villaflores) == TRUE)
        {
            array_push($unidades, 'VILLAFLORES');
        }
        if($this->checkComparator($request->chk_jiquipilas) == TRUE)
        {
            array_push($unidades, 'JIQUIPILAS');
        }
        if($this->checkComparator($request->chk_catazaja) == TRUE)
        {
            array_push($unidades, 'CATAZAJA');
        }
        if($this->checkComparator($request->chk_yajalon) == TRUE)
        {
            array_push($unidades, 'YAJALON');
        }
        if($this->checkComparator($request->chk_san_cristobal) == TRUE)
        {
            array_push($unidades, 'SAN CRISTOBAL');
        }
        if($this->checkComparator($request->chk_chiapa_de_corzo) == TRUE)
        {
            array_push($unidades, 'CHIAPA DE CORZO');
        }
        if($this->checkComparator($request->chk_motozintla) == TRUE)
        {
            array_push($unidades, 'MOTOZINTLA');
        }
        if($this->checkComparator($request->chk_berriozabal) == TRUE)
        {
            array_push($unidades, 'BERRIOZABAL');
        }
        if($this->checkComparator($request->chk_pijijiapan) == TRUE)
        {
            array_push($unidades, 'PIJIJIAPAN');
        }
        if($this->checkComparator($request->chk_jitotol) == TRUE)
        {
            array_push($unidades, 'JITOTOL');
        }
        if($this->checkComparator($request->chk_la_concordia) == TRUE)
        {
            array_push($unidades, 'LA CONCORDIA');
        }
        if($this->checkComparator($request->chk_venustiano_carranza) == TRUE)
        {
            array_push($unidades, 'VENUSTIANO CARRANZA');
        }
        if($this->checkComparator($request->chk_tila) == TRUE)
        {
            array_push($unidades, 'TILA');
        }
        if($this->checkComparator($request->chk_teopisca) == TRUE)
        {
            array_push($unidades, 'TEOPISCA');
        }
        if($this->checkComparator($request->chk_ocosingo) == TRUE)
        {
            array_push($unidades, 'OCOSINGO');
        }
        if($this->checkComparator($request->chk_cintalapa) == TRUE)
        {
            array_push($unidades, 'CINTALAPA');
        }
        if($this->checkComparator($request->chk_copainala) == TRUE)
        {
            array_push($unidades, 'COPAINALA');
        }
        if($this->checkComparator($request->chk_soyalo) == TRUE)
        {
            array_push($unidades, 'SOYALO');
        }
        if($this->checkComparator($request->chk_angel_albino_corzo) == TRUE)
        {
            array_push($unidades, 'ANGEL ALBINO CORZO');
        }
        if($this->checkComparator($request->chk_arriaga) == TRUE)
        {
            array_push($unidades, 'ARRIAGA');
        }
        if($this->checkComparator($request->chk_pichucalco) == TRUE)
        {
            array_push($unidades, 'PICHUCALCO');
        }
        if($this->checkComparator($request->chk_juarez) == TRUE)
        {
            array_push($unidades, 'JUAREZ');
        }
        if($this->checkComparator($request->chk_simojovel) == TRUE)
        {
            array_push($unidades, 'SIMOJOVEL');
        }
        if($this->checkComparator($request->chk_mapastepec) == TRUE)
        {
            array_push($unidades, 'MAPASTEPEC');
        }
        if($this->checkComparator($request->chk_villa_corzo) == TRUE)
        {
            array_push($unidades, 'VILLA CORZO');
        }
        if($this->checkComparator($request->chk_cacahoatan) == TRUE)
        {
            array_push($unidades, 'CACAHOATAN');
        }
        if($this->checkComparator($request->chk_once_de_abril) == TRUE)
        {
            array_push($unidades, 'ONCE DE ABRIL');
        }
        if($this->checkComparator($request->chk_tuxtla_chico) == TRUE)
        {
            array_push($unidades, 'TUXTLA CHICO');
        }
        if($this->checkComparator($request->chk_oxchuc) == TRUE)
        {
            array_push($unidades, 'OXCHUC');
        }
        if($this->checkComparator($request->chk_chamula) == TRUE)
        {
            array_push($unidades, 'CHAMULA');
        }
        if($this->checkComparator($request->chk_ostuacan) == TRUE)
        {
            array_push($unidades, 'OSTUACAN');
        }
        if($this->checkComparator($request->chk_palenque) == TRUE)
        {
            array_push($unidades, 'PALENQUE');
        }

        $reform = instructor::find($request->id_available);
        $reform->unidades_disponible = $unidades;
        $reform->save();

        return redirect()->route('instructor-inicio')
                ->with('success','Instructor Modificado');
    }

    protected function checkComparator($check)
    {
        if(isset($check))
        {
            $stat = TRUE;
        }
        else
        {
            $stat = FALSE;
        }
        return $stat;
    }

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function jpg_upload($jpg, $id, $nom)
    {
        # nuevo nombre del archivo
        $jpgFile = trim($nom."_".date('YmdHis')."_".$id.".jpg");
        $jpg->storeAs('/uploadFiles/instructor/'.$id, $jpgFile); // guardamos el archivo en la carpeta storage
        $jpgUrl = Storage::url('/uploadFiles/instructor/'.$id."/".$jpgFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $jpgUrl;
    }

    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }

    public function exportar_instructores()
    {
        $data = instructor::SELECT('instructores.id','tbl_unidades.unidad','instructores.apellidoPaterno',
                'instructores.apellidoMaterno','instructores.nombre','instructores.curp','instructores.rfc',
                'instructores.numero_control',
                DB::raw("array(select especialidades.nombre from especialidad_instructores
                LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as espe"),
                DB::raw("array(select fecha_validacion from especialidad_instructores
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as fechaval"),
                DB::raw("array(select especialidades.clave from especialidad_instructores
                LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as clave"),
                DB::raw("array(select criterio_pago_id from especialidad_instructores
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as criteriopago"),
                DB::raw("array(select grado_profesional from instructor_perfil
                where instructores.id = instructor_perfil.numero_control )as grado"),
                DB::raw("array(select estatus from instructor_perfil
                where instructores.id = instructor_perfil.numero_control )as estatus"),
                DB::raw("array(select area_carrera from instructor_perfil
                where instructores.id = instructor_perfil.numero_control )as area"),
                DB::raw("array(select nombre_institucion from instructor_perfil
                where instructores.id = instructor_perfil.numero_control )as institucion"),
                'instructores.sexo','instructores.estado_civil',
                'instructores.asentamiento','instructores.domicilio','instructores.telefono','instructores.correo',
                DB::raw("array(select memorandum_validacion from especialidad_instructores
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as memo"),
                DB::raw("array(select observacion from especialidad_instructores
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as obs"),
                DB::raw("(CASE WHEN EXISTS (
                    SELECT 1
                    FROM especialidad_instructores
                    LEFT JOIN instructor_perfil ON instructor_perfil.numero_control = instructores.id
                    WHERE especialidad_instructores.perfilprof_id = instructor_perfil.id
                    AND especialidad_instructores.fecha_validacion BETWEEN (CURRENT_DATE - INTERVAL '1 year') AND CURRENT_DATE
                    )
                    THEN 'activo'
                    ELSE 'inactivo'
                    END) AS activo"))
                ->WHERE('instructores.estado', '=', TRUE)
                ->whereRaw("array(select especialidades.nombre from especialidad_instructores
                LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
                LEFT JOIN instructor_perfil ip on ip.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = ip.id) != '{}'")
                ->LEFTJOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
                ->ORDERBY('apellidoPaterno', 'ASC')
                ->GET();

        $cabecera = ['ID','UNIDAD DE CAPACITACION/ACCION MOVIL','APELLIDO PATERNO','APELLIDO MATERNO','NOMBRE','CURP','RFC','NUMERO COTROL','ESPECIALIDAD','FECHA DE VALIDACION','CLAVE','CRITERIO PAGO',
                    'GRADO PROFESIONAL QUE CUBRE PARA LA ESPECIALIDAD','PERFIL PROFESIONAL CON EL QUE SE VALIDO',
                    'FORMACION PROFESIONAL CON EL QUE SE VALIDO','INSTITUCION','SEXO','ESTADO_CIVIL',
                    'ASENTAMIENTO','DOMICILIO','TELEFONO','CORREO','MEMORANDUM DE VALIDACION',
                    'OBSERVACION','ACTIVO/INACTIVO'];

        $nombreLayout = "Catalogo de instructores.xlsx";
        $titulo = "Catalogo de instructores";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    public function exportar_instructores_activos()
    {
        $data = instructor::SELECT('instructores.id', 'tbl_unidades.unidad', 'instructores.apellidoPaterno', 'instructores.apellidoMaterno', 'instructores.nombre', 'instructores.curp', 'instructores.rfc', 'instructores.numero_control',
        DB::raw("array(select especialidades.nombre from especialidad_instructores
        LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as espe"),
        DB::raw("array(select fecha_validacion from especialidad_instructores
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as fechaval"),
        DB::raw("array(select especialidades.clave from especialidad_instructores
        LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as clave"),
        DB::raw("array(select criterio_pago_id from especialidad_instructores
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as criteriopago"),
        DB::raw("array(select grado_profesional from instructor_perfil
        where instructores.id = instructor_perfil.numero_control) as grado"),
        DB::raw("array(select estatus from instructor_perfil
        where instructores.id = instructor_perfil.numero_control) as estatus"),
        DB::raw("array(select area_carrera from instructor_perfil
        where instructores.id = instructor_perfil.numero_control) as area"),
        DB::raw("array(select nombre_institucion from instructor_perfil
        where instructores.id = instructor_perfil.numero_control) as institucion"),
        'instructores.sexo', 'instructores.estado_civil', 'instructores.asentamiento', 'instructores.domicilio', 'instructores.telefono', 'instructores.correo',
        DB::raw("array(select memorandum_validacion from especialidad_instructores
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as memo"),
        DB::raw("array(select observacion from especialidad_instructores
        LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
        where especialidad_instructores.perfilprof_id = instructor_perfil.id) as obs"))
    ->WHERE('instructores.estado', '=', TRUE)
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
            ->from('especialidad_instructores')
            ->leftJoin('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
            ->leftJoin('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->whereRaw('especialidad_instructores.perfilprof_id = instructor_perfil.id')
            ->whereRaw('especialidad_instructores.fecha_validacion BETWEEN (CURRENT_DATE - INTERVAL \'1 year\') AND CURRENT_DATE');
    })
    ->leftJoin('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
    ->orderBy('apellidoPaterno', 'ASC')
    ->get();


        $cabecera = ['ID','UNIDAD DE CAPACITACION/ACCION MOVIL','APELLIDO PATERNO','APELLIDO MATERNO','NOMBRE','CURP','RFC','NUMERO COTROL','ESPECIALIDAD','FECHA DE VALIDACION','CLAVE','CRITERIO PAGO',
                    'GRADO PROFESIONAL QUE CUBRE PARA LA ESPECIALIDAD','PERFIL PROFESIONAL CON EL QUE SE VALIDO',
                    'FORMACION PROFESIONAL CON EL QUE SE VALIDO','INSTITUCION','SEXO','ESTADO_CIVIL',
                    'ASENTAMIENTO','DOMICILIO','TELEFONO','CORREO','MEMORANDUM DE VALIDACION',
                    'OBSERVACION'];

        $nombreLayout = "Catalogo de instructores.xlsx";
        $titulo = "Catalogo de instructores";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    public function exportar_instructoresByEspecialidad()
    {
        $data = Especialidad::SELECT('especialidades.id','especialidades.nombre','especialidades.clave',
                DB::raw('Array( SELECT CONCAT(instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno", '."' '".' ,
                instructores.nombre) from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS ins'),
                DB::raw('Array( SELECT instructores.numero_control from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS numero_control'),
                DB::raw("array(select especialidad_instructores.criterio_pago_id from especialidad_instructores
                where especialidad_instructores.especialidad_id = especialidades.id) as criteriopago"),
                DB::raw("array(select instructor_perfil.grado_profesional from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                where especialidad_instructores.especialidad_id = especialidades.id) as gradoprof"),
                DB::raw("array(select instructor_perfil.estatus from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                where especialidad_instructores.especialidad_id = especialidades.id) as estatus"),
                DB::raw("array(select instructor_perfil.area_carrera from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                where especialidad_instructores.especialidad_id = especialidades.id) as area"),
                DB::raw("array(select instructor_perfil.nombre_institucion from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                where especialidad_instructores.especialidad_id = especialidades.id) as institucion"),
                DB::raw('Array( SELECT instructores.rfc from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS rfc'),
                DB::raw('Array( SELECT instructores.curp from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS curp'),
                DB::raw('Array( SELECT instructores.sexo from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS sexo'),
                DB::raw('Array( SELECT instructores.estado_civil from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS estado_civil'),
                DB::raw('Array( SELECT instructores.asentamiento from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS asentamiento'),
                DB::raw('Array( SELECT instructores.domicilio from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS domicilio'),
                DB::raw('Array( SELECT instructores.telefono from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS telefono'),
                DB::raw('Array( SELECT instructores.correo from especialidad_instructores
                inner join instructor_perfil on instructor_perfil.id = especialidad_instructores.perfilprof_id
                inner join instructores on instructores.id = instructor_perfil.numero_control
                where especialidad_instructores.especialidad_id = especialidades.id) AS correo'),
                DB::raw("array(select especialidad_instructores.memorandum_validacion from especialidad_instructores
                where especialidad_instructores.especialidad_id = especialidades.id) as memo"),
                DB::raw("array(select especialidad_instructores.fecha_validacion from especialidad_instructores
                where especialidad_instructores.especialidad_id = especialidades.id) as fechaval"),
                DB::raw("array(select especialidad_instructores.observacion from especialidad_instructores
                where especialidad_instructores.especialidad_id = especialidades.id) as observacion"))
                /*
                ->WHERE('instructores.estado', '=', TRUE)
                ->whereRaw("array(select especialidades.nombre from especialidad_instructores
                LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
                LEFT JOIN instructor_perfil ip on ip.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = ip.id) != '{}'")*/
                //->ORDERBY('apellidoPaterno', 'ASC')
                ->GET();

        $cabecera = ['ID','ESPECIALIDAD','CLAVE','NOMBRE','NUMERO COTROL','CRITERIO PAGO',
                    'GRADO PROFESIONAL QUE CUBRE PARA LA ESPECIALIDAD','PERFIL PROFESIONAL CON EL QUE SE VALIDO',
                    'FORMACION PROFESIONAL CON EL QUE SE VALIDO','INSTITUCION','RFC','CURP','SEXO','ESTADO_CIVIL',
                    'ASENTAMIENTO','DOMICILIO','TELEFONO','CORREO','UNIDAD DE CAPACITACION','MEMORANDUM DE VALIDACION',
                    'FECHA DE VALIDACION','OBSERVACION'];

        $nombreLayout = "Catalogo de instructores.xlsx";
        $titulo = "Catalogo de instructores";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    public function entrevista_pdf($idins)
    {
        // dd($idins);
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();
        $data = pre_instructor::WHERE('id', '=', $idins)->WHERE('registro_activo', TRUE)->FIRST();
        if(!is_array($data->entrevista)) {
            $data->entrevista = json_decode($data->entrevista, true);
        }
        if(!isset($data))
        {
            $data = instructor::WHERE('id', '=', $idins)->FIRST();
        }
        $fecha_solicitud = carbon::now()->toDateString();
        $date = strtotime($fecha_solicitud);
        $userunidad = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', Auth::user()->unidad)->FIRST();

        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);

        $funcionarios = $this->funcionarios('TUXTLA');
        $direccion = explode("*",$funcionarios['dacademico']['direccion']);

        $pdf = PDF::loadView('layouts.pdfpages.entrevistainstructor',compact('data','leyenda','D','M','Y','userunidad','funcionarios','direccion'));
        $pdf->setPaper('letter');
        return  $pdf->stream('entrevista_instructor.pdf');
    }

    public function curriculumicatech_pdf($idins)
    {
        // dd($idins);
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();
        $data = pre_instructor::WHERE('id', '=', $idins)->WHERE('registro_activo', TRUE)->FIRST();
        if(!isset($data))
        {
            $data = instructor::WHERE('id', '=', $idins)->FIRST();
            $perfiles = InstructorPerfil::WHERE('numero_control', '=', $data->id)->GET();
        }
        else
        {
            $perfiles = $this->make_collection($data->data_perfil);
        }
        $date = strtotime(carbon::now()->toDateString());
        $funcionarios  = $this->funcionarios('TUXTLA');

        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        $direccion = explode("*",$funcionarios['dacademico']['direccion']);

        //revision de json a array
        if(!is_array($data->exp_docente)) {
            $data->exp_docente = json_decode($data->exp_docente, true);
        }
        if(!is_array($data->exp_laboral)) {
            $data->exp_laboral = json_decode($data->exp_laboral, true);
        }
        // fin

        $pdf = PDF::loadView('layouts.pdfpages.curriculumicatechinstructor',compact('leyenda','data', 'perfiles','D','M','Y','funcionarios','direccion'));
        $pdf->setPaper('letter');
        return  $pdf->stream('curriculum_icatech_instructor.pdf');
    }

    public function solicitud_instructor_pdf(Request $request)
    {
        // dd($request);
        $instructor = pre_instructor::WHERE('id', $request->idins)->FIRST();
        $honorario_actual = instructor::Where('id', $request->idins)->First()->Value('tipo_honorario');
        $porcentaje = $cursosnoav = NULL;
        $tipo_doc = 'VALIDACION';
        $rplc = array('[',']','"');
        $arrtemp = array();
        $especialidad_cambios = FALSE;
        if(!isset($request->borrador))
        {
            $arrstat = array('EN FIRMA','REVALIDACION EN FIRMA','REACTIVACION EN FIRMA','BAJA EN FIRMA');
            // $especialidad_cambios = TRUE;
        }
        else
        {
            $arrstat = array('REACTIVACION EN CAPTURA','BAJA EN CAPTURA','REVALIDACION EN CAPTURA','EN CAPTURA','PREVALIDACION','REVALIDACION EN PREVALIDACION','REACTIVACION EN PREVALIDACION','BAJA EN PREVALIDACION');
            // $especialidad_cambios = TRUE;
        }
        set_time_limit(0);

        $daesp = DB::TABLE('tbl_unidades')
                ->WHERE('ubicacion','LIKE',$instructor->nrevision[0]. $instructor->nrevision[1] .'%')
                ->GROUPBY('ubicacion')
                ->VALUE('ubicacion');
        $especialidades = $this->make_collection($instructor->data_especialidad);
        foreach($especialidades as $moist)
        {
            if(in_array($moist->status, $arrstat))
            {
                $onesp = DB::TABLE('especialidades')->SELECT('nombre')->WHERE('id',$moist->especialidad_id)->FIRST();
                $moist->especialidad = $onesp->nombre;
                array_push($arrtemp, $moist);
                $especialidad_cambios = TRUE;
            }
        }
        if($instructor->tipo_honorario != $honorario_actual && $especialidad_cambios == FALSE)
        {
            $arrstat = array('VALIDADO');
            foreach($especialidades as $moist)
            {
                if(in_array($moist->status, $arrstat))
                {
                    $onesp = DB::TABLE('especialidades')->SELECT('nombre')->WHERE('id',$moist->especialidad_id)->FIRST();
                    $moist->especialidad = $onesp->nombre;
                    array_push($arrtemp, $moist);
                }
            }
        }
        $data = $this->make_collection($arrtemp);
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();


        foreach($data as $count => $item)
        {
            // $item->cursos_impartir = explode(',',str_replace($rplc,'',$item->cursos_impartir));
            if($honorario_actual != $instructor->tipo_honorario || ($item->status != 'BAJA EN FIRMA' && $item->status != 'BAJA EN PREVALIDACION'))
            {
                $cursos[$count] = DB::TABLE('cursos')->SELECT('cursos.nombre_curso')
                                ->WHEREIN('id',$item->cursos_impartir)
                                ->GET();
                $totalcursos = DB::TABLE('cursos')->SELECT(DB::RAW("COUNT(id) AS total"))
                                ->WHERE('id_especialidad','=',$item->especialidad_id)
                                ->FIRST();
                $cursosnoav[$count] = DB::TABLE('cursos')->SELECT('nombre_curso')
                                ->WHERE('id_especialidad','=',$item->especialidad_id)
                                ->WHERENOTIN('id',$item->cursos_impartir)->GET();
                if($totalcursos->total != 0) {
                    $porcentaje[$count] = (100*count($cursos[$count]))/$totalcursos->total;
                } else {
                    $porcentaje[$count] = 0;
                }
            }
            else
            {
                $cursos[$count] = array();
            }

            //GUARDADO DE FECHA Y MEMO DE SOLICITUD
            $thalmor = especialidad_instructor::WHERE('id','=',$item->id)->FIRST();
            if($item->fecha_solicitud == NULL || isset($request->borrador))
            {
                $item->fecha_solicitud = carbon::now()->toDateString();
                $date = strtotime($item->fecha_solicitud);
            }
            else
            {
                $date = strtotime($data[0]->fecha_solicitud);
            }
            if($item->memorandum_solicitud != $request->nomemo)
            {
                $item->memorandum_solicitud = $request->nomemo;
            }

            foreach($especialidades as $pos => $cadwell)
            {
                if($cadwell->id == $item->id)
                {
                    $especialidades[$pos] = $item;
                }
            }
            if($tipo_doc != 'REACTIVACION')
            {
                switch($item->status)
                {
                    case 'REVALIDACION EN CAPTURA';
                        $tipo_doc = 'REVALIDACION';
                    break;
                    case 'REVALIDACION EN FIRMA';
                        $tipo_doc = 'REVALIDACION';

                    break;
                    case 'REACTIVACION EN FIRMA';
                        $tipo_doc = 'REACTIVACION';
                    break;
                    case 'REVALIDACION EN PREVALIDACION';
                        $tipo_doc = 'REVALIDACION';
                    break;
                    case 'REACTIVACION EN PREVALIDACION';
                        $tipo_doc = 'REACTIVACION';
                    break;
                }
            } if($instructor->tipo_honorario != $honorario_actual && $especialidad_cambios == FALSE) {
                $tipo_doc = 'REVALIDACION';
            }
        }


        $instructor->data_especialidad = $especialidades;
        // if(!isset($request->borrador))
        // {
            $instructor->save();
        // }

        $data_unidad = DB::TABLE('tbl_unidades')->WHERE('unidad', '=', $daesp)->FIRST();
        $solicito = DB::TABLE('users')->WHERE('id', '=', Auth::user()->id)->FIRST();
        $funcionarios = $this->funcionarios($daesp);

        if($honorario_actual != $instructor->tipo_honorario && $especialidad_cambios == FALSE && isset($request->borrador)) {
            $date = strtotime(carbon::now());
        }
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        $nomemosol = $request->nomemo;
        $fecha_letra = $this->obtenerFechaEnLetra($D);
        $direccion = explode("*",$funcionarios['dunidad']['direccion']);
        $pdf = PDF::loadView('layouts.pdfpages.solicitudinstructor',compact('leyenda','data','cursos','porcentaje','instructor','data_unidad','solicito','D','M','Y','cursosnoav','nomemosol','tipo_doc','fecha_letra','daesp','funcionarios','direccion'));
        $pdf->setPaper('letter');
        return  $pdf->stream('solicitud_instructor.pdf');
    }

    function obtenerFechaEnLetra($fecha){
        $mes_array = ['01'=> 'UN','02' => 'DOS','03' => 'TRÉS','04' => 'CUATRO','05' => 'CINCO',
                    '06' => 'SEIS','07' => 'SIETE','08' => 'OCHO','09' => 'NUEVE','10' => 'DIEZ',
                    '11' => 'ONCE','12' => 'DOCE','13' => 'TRECE','14' => 'CATORCE','15' => 'QUINCE',
                    '16' => 'DIECISEIS','17' => 'DIECISIETE','18' => 'DIECIOCHO','19' => 'DIECINUEVE','20' => 'VEINTE',
                    '21' => 'VEINTIUNO','22' => 'VEINTIDOS','23' => 'VEINTIRÉS','24' => 'VEINTICUATRO','25' => 'VEINTICINCO',
                    '26' => 'VEINTISEIS','27' => 'VEINTISIETE','28' => 'VEINTIOCHO','29' => 'VEINTINUEVE','30' => 'TREINTA',
                    '31' => 'TREINTA Y UN'];
        $fecha_letra = $mes_array[$fecha];
        return $fecha_letra;
    }

    public function validacion_instructor_pdf(Request $request)
    {
        // dd($request);
        $rplc = array('[',']','"');
        $arrstat = array('EN FIRMA','REVALIDACION EN FIRMA','REACTIVACION EN FIRMA','BAJA EN FIRMA');
        $especialidades = $arrtemp = array();
        set_time_limit(0);
        $user = auth::user()->id;

        $instructor = pre_instructor::find($request->idinsgendocval);
        $regimen_actual = instructor::Where('id',$instructor->id)->Value('tipo_honorario');
        $special = $this->make_collection($instructor->data_especialidad);

        foreach($special as $key => $cadwell)
        {
            $arrtemp = null;
            if(in_array($cadwell->status, $arrstat) || ($regimen_actual != $instructor->tipo_honorario && $cadwell->status != 'BAJA'))
            {
                if($cadwell->status != 'BAJA EN FIRMA' && $cadwell->fecha_validacion == NULL)
                {
                    $cadwell->fecha_validacion = $special[$key]->fecha_validacion = carbon::now()->toDateString();
                }
                elseif($cadwell->status == 'BAJA EN FIRMA' && $cadwell->fecha_baja == NULL)
                {
                    $cadwell->fecha_baja = $special[$key]->fecha_baja = carbon::now()->toDateString();
                }

                if($cadwell->status != 'BAJA EN FIRMA' && $cadwell->memorandum_validacion != $request->memovali)
                {
                    $cadwell->memorandum_validacion = $special[$key]->memorandum_validacion = $request->memovali;
                }
                elseif($cadwell->status == 'BAJA EN FIRMA' && $cadwell->memorandum_baja != $request->memovali)
                {
                    $cadwell->memorandum_baja = $special[$key]->memorandum_baja = $request->memovali;
                }
                $cadwell->observacion_validacion = $special[$key]->observacion_validacion = $request->observacion_validacion;

                $cp = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')->WHERE('id',$cadwell->criterio_pago_id)->FIRST();
                $sp = DB::TABLE('especialidades')->SELECT('nombre', 'clave')->WHERE('id',$cadwell->especialidad_id)->FIRST();
                $arrtemp = $cadwell;
                $arrtemp->nombre = $sp->nombre;
                $arrtemp->clave = $sp->clave;
                $arrtemp->perfil_profesional = $cp->perfil_profesional;
                array_push($especialidades,$arrtemp);
            }
        }

        $instructor->data_especialidad = $special;

        // $elaboro = DB::TABLE('users')->WHERE('id','=', $user)->FIRST();
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();
        $especialidades = $this->make_collection($especialidades);
        $ubicacion = DB::TABLE('tbl_unidades')
                        ->WHERE('ubicacion', 'LIKE', $instructor->nrevision[0].$instructor->nrevision[1].'%')
                        ->value('ubicacion');
        $unidad = DB::TABLE('tbl_unidades')
                        ->WHERE('unidad', '=', $ubicacion)
                        ->FIRST();
        $funcionarios = $this->funcionarios($ubicacion);
        if($instructor->numero_control == 'Pendiente')
        {
            $uni = substr($unidad->cct, -3, 2) * 1 . substr($unidad->cct, -1);
            $now = Carbon::now();
            $year = substr($now->year, -2);
            $rfcpart = substr($instructor->rfc, 0, 10);
            $numero_control = $uni.$year.$rfcpart;
            $instructor->clave_unidad = $unidad->cct;
        }
        else
        {
            $D3 = substr($instructor->numero_control, 0, 3);
            if( $D3 == '10K' || $D3 == '11J')
            {
                $part1 = substr($instructor->numero_control, 0, 5);
            }
            else
            {
                $part1 = substr($instructor->numero_control, 0, 4);
            }
            $rfcpart = substr($instructor->rfc, 0, 10);
            $numero_control = $part1 . $rfcpart;
        }
        $instructor->numero_control = $numero_control;
        $instructor->save();

        if($especialidades[0]->status != 'BAJA EN FIRMA')
        {
            $date = strtotime($especialidades[0]->fecha_validacion);
        }
        else
        {
            $date = strtotime($especialidades[0]->fecha_baja);
        }
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        $direccion = explode("*",$funcionarios['dacademico']['direccion']);
        $pdf = PDF::loadView('layouts.pdfpages.validacioninstructor',compact('leyenda','instructor','especialidades','unidad','D','M','Y','funcionarios','direccion'));
        $pdf->setPaper('letter', 'Landscape');
        return  $pdf->stream('validacion_instructor.pdf');
    }

    public function solicitud_baja_instructor_pdf(Request $request)
    {
        // dd($request);
        $nomesp = $arrtemp = $especialidades = array();
        $instructor = pre_instructor::find($request->idins);
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();
        $idesps = especialidad_instructor::SELECT('id')->WHERE('id_instructor', '=', $request->idins)
                                            ->WHERE('status','=','BAJA EN FIRMA')
                                            ->GET();
        $special = $this->make_collection($instructor->data_especialidad);
        foreach($special as $key => $moist)
        {
            if($moist->status == 'BAJA EN FIRMA' || $moist->status == 'BAJA EN PREVALIDACION')
            {
                if($moist->fecha_solicitud == NULL)
                {
                    $moist->fecha_solicitud = $special[$key]->fecha_solicitud = carbon::now()->toDateString();
                }
                if($moist->memorandum_solicitud != $request->nomemo && $moist->status == 'BAJA EN FIRMA')
                {
                    $moist->memorandum_solicitud = $special[$key]->memorandum_solicitud = $request->nomemo;
                }
                else
                {

                    $moist->memorandum_solicitud = 'BORRADOR';
                }
                $moist->especialidad = DB::TABLE('especialidades')->WHERE('id',$moist->especialidad_id)->value('nombre');
                array_push($especialidades, $moist);
            }
        }
        $instructor->data_especialidad = $special;
        if($instructor->status == 'BAJA EN FIRMA')
        {
            $instructor->save();
        }
        $especialidades = $this->make_collection($especialidades);

        $data_unidad = DB::TABLE('tbl_unidades')->WHERE('ubicacion', 'LIKE', $instructor->nrevision[0].$instructor->nrevision[1].'%')
        ->WHERE('unidad', '!=', 'VILLA CORZO')
        ->WHERE('unidad', '!=', 'TUXTLA CHICO')->FIRST();
        $funcionarios = $this->funcionarios($data_unidad->ubicacion);
        $date = strtotime($especialidades[0]->fecha_solicitud);
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        // dd($especialidades);
        $direccion = explode("*",$funcionarios['dunidad']['direccion']);

        $pdf = PDF::loadView('layouts.pdfpages.solicitudbajainstructor',compact('leyenda','instructor','data_unidad','D','M','Y','especialidades','funcionarios','direccion'));
        $pdf->setPaper('letter');
        return  $pdf->stream('baja_instructor.pdf');
    }

    public function validacion_baja_instructor_pdf(Request $request)
    {
        // dd($request);
        $nomesp = $arrtemp = $especialidades = array();
        $instructor = pre_instructor::find($request->idinsbajadocval);
        $leyenda = DB::TABLE('tbl_instituto')->PLUCK('distintivo')->FIRST();
        $special = $this->make_collection($instructor->data_especialidad);
        $idesps = especialidad_instructor::SELECT('id','memorandum_validacion')->WHERE('id_instructor', '=', $request->idinsbajadocval)
                                            ->WHERE('status','=','BAJA EN FIRMA')
                                            ->GET();
        foreach($special as $key => $cadwell)
        {
            if($cadwell->status == 'BAJA EN FIRMA')
            {
                if($cadwell->fecha_baja == NULL)
                {
                    $cadwell->fecha_baja = $special[$key]->fecha_baja = carbon::now()->toDateString();
                }
                if($cadwell->memorandum_baja != $request->memobaja)
                {
                    $cadwell->memorandum_baja = $special[$key]->memorandum_baja = $request->memobaja;
                }

                array_push($especialidades, $cadwell);
            }
        }

        $instructor->data_especialidad = $special;
        $instructor->save();

        $data_unidad = DB::TABLE('tbl_unidades')->WHERE('ubicacion', 'LIKE', $instructor->nrevision[0].$instructor->nrevision[1].'%')
        ->WHERE('unidad', '!=', 'VILLA CORZO')
        ->WHERE('unidad', '!=', 'TUXTLA CHICO')->FIRST();
        $funcionarios = $this->funcionarios($data_unidad->ubicacion);

        $date = strtotime($especialidades[0]->fecha_baja);
        $datesol = strtotime($especialidades[0]->fecha_solicitud);
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        $DS = date('d', $datesol);
        $MOS = date('m',$datesol);
        $MS = $this->monthToString(date('m',$datesol));//A
        $YS = date("Y",$datesol);
        // dd($data_unidad);
        $direccion = explode("*",$funcionarios['dacademico']['direccion']);

        $pdf = PDF::loadView('layouts.pdfpages.validacionbajainstructor',compact('leyenda','instructor','data_unidad','D','M','Y','especialidades','DS','MS','YS','funcionarios','direccion'));
        $pdf->setPaper('letter');
        return  $pdf->stream('baja_instructor_validacion.pdf');
    }

    public function deshacer_movimiento($id) {
        $instructor_ofc = instructor::find($id);
        $temp = pre_instructor::find($id);
        $perfiles = InstructorPerfil::WHERE('numero_control',$id)->GET();
        $especialidades = especialidad_instructor::WHERE('id_instructor', '=', $id)->GET();

        if(!is_null($perfiles) && !is_null($especialidades)) {
            if($instructor_ofc->estado) {
                $instructor_ofc->status = 'VALIDADO';
            } else {
                $instructor_ofc->status = 'BAJA';
            }

            foreach ($instructor_ofc->getAttributes() as $key => $data) {
                $temp->$key = $data;
            }

            $instructor_ofc->turnado = $temp->turnado = 'UNIDAD';
            $temp->registro_activo = FALSE;
            unset($temp->curso_extra);

            //data_especialidad
            $data_especialidad = $temp->data_especialidad;
            foreach ($especialidades as $pointer => $item) {
                foreach ($data_especialidad as $count => $seguridad) {
                    if($seguridad['id'] == $item->id && !$seguridad['new'] && !in_array($seguridad['status'],['VALIDADO','BAJA'])) { //Seguridad para evitar que nuevos o ya validados hagan cambios que no existen
                        foreach ($item->getAttributes() as $element => $esp_ofc) {
                            if(!in_array($element,['created_at','updated_at'])) { // evita que se inserten en data_especialidad
                                $data_especialidad[$count][$element] = $esp_ofc;
                            }
                        }
                        //termina proceso de llenado de un elemento de data_especialidad
                        $data_especialidad[$count]['fecha_baja'] = $data_especialidad[$count]['memorandum_baja'] = $data_especialidad[$count]['memorandum_solicitud'] = $data_especialidad[$count]['memorandum_validacion'] = $data_especialidad[$count]['memorandum_modificacion'] = NULL; // campos que deben ir nulos en data_especialidad
                    }
                }
            }
            $temp->data_especialidad = $data_especialidad;

            //data_perfil
            $data_perfil = $temp->data_perfil;
            foreach ($perfiles as $pointer => $item) {
                foreach ($data_perfil as $count => $seguridad) {
                    if($seguridad['id'] == $item->id && !$seguridad['new'] && !in_array($seguridad['status'],['VALIDADO','BAJA'])) { //Seguridad para evitar que nuevos o ya validados hagan cambios que no existen
                        foreach ($item->getAttributes() as $element => $perf_ofc) {
                            if(!in_array($element,['created_at','updated_at'])) { // evita que se inserten en data_especialidad
                                $data_perfil[$count][$element] = $perf_ofc;
                            }
                        }
                    }
                }
            }
            $temp->data_perfil = $data_perfil;
        } else {
            $instructor_ofc->status = $temp->turnado = 'ELIMINADO';
            $instructor_ofc->turnado = $temp->turnado = 'UNIDAD';
            $instructor_ofc->estado = $temp->estado = FALSE;
        }

        $temp->save();
        $instructor_ofc->save();

        return redirect()->route('instructor-inicio')
                ->with('success', 'El Movimiento ha sido Anulado Exitosamente');
    }

    private function guardado_ins($saveInstructor,$request,$id)
    {
        // dd($request);
        $arresp = $arrper = $arrtemp = array();
        $perfiles = InstructorPerfil::WHERE('numero_control',$id)->GET();
        $especialidades = especialidad_instructor::WHERE('id_instructor', '=', $id)->GET();
        $userId = Auth::user()->id;
        $useruni = Auth::user()->unidad;
        $unidades = ['TUXTLA', 'TAPACHULA', 'COMITAN', 'REFORMA', 'TONALA', 'VILLAFLORES', 'JIQUIPILAS', 'CATAZAJA',
                'YAJALON', 'SAN CRISTOBAL', 'CHIAPA DE CORZO', 'MOTOZINTLA', 'BERRIOZABAL', 'PIJIJIAPAN', 'JITOTOL',
                'LA CONCORDIA', 'VENUSTIANO CARRANZA', 'TILA', 'TEOPISCA', 'OCOSINGO', 'CINTALAPA', 'COPAINALA',
                'SOYALO', 'ANGEL ALBINO CORZO', 'ARRIAGA', 'PICHUCALCO', 'JUAREZ', 'SIMOJOVEL', 'MAPASTEPEC',
                'VILLA CORZO', 'CACAHOATAN', 'ONCE DE ABRIL', 'TUXTLA CHICO', 'OXCHUC', 'CHAMULA', 'OSTUACAN',
                'PALENQUE'];
        $estado = DB::TABLE('estados')->SELECT('nombre')->WHERE('id', '=', $request->entidad)->FIRST();
        $munic = DB::TABLE('tbl_municipios')->SELECT('muni')->WHERE('id', '=', $request->municipio)->FIRST();
        $ubicacion = DB::TABLE('tbl_unidades')->SELECT('ubicacion')->WHERE('id', '=', $useruni)->FIRST();
        $unidadregistra = DB::TABLE('tbl_unidades')->SELECT('cct')->WHERE('unidad', '=', $ubicacion->ubicacion)->FIRST();
        $locali = DB::TABLE('tbl_localidades')->SELECT('localidad')->WHERE('clave','=', $request->localidad)->FIRST();
        $estado_nac = DB::TABLE('estados')->SELECT('nombre')->WHERE('id', '=', $request->entidad_nacimiento)->FIRST();
        $munic_nac = DB::TABLE('tbl_municipios')->SELECT('muni')->WHERE('id', '=', $request->municipio_nacimiento)->FIRST();
        $locali_nac = DB::TABLE('tbl_localidades')->SELECT('localidad')->WHERE('clave','=', $request->localidad_nacimiento)->FIRST();
        # Proceso de Guardado
        #----- Personal -----
        $saveInstructor->id = $id;
        $saveInstructor->nombre = trim($request->nombre);
        $saveInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $saveInstructor->apellidoMaterno = trim($request->apellido_materno);
        $saveInstructor->curp = trim($request->curp);
        $saveInstructor->rfc = $request->rfc;
        $saveInstructor->tipo_identificacion = trim($request->tipo_identificacion);
        $saveInstructor->expiracion_identificacion = trim($request->expiracion_identificacion);
        $saveInstructor->folio_ine = $request->folio_ine;
        $saveInstructor->sexo = $request->sexo;
        $saveInstructor->estado_civil = $request->estado_civil;
        $saveInstructor->fecha_nacimiento = $request->fecha_nacimientoins;
        $saveInstructor->entidad = $estado->nombre;
        $saveInstructor->municipio = $munic->muni;
        $saveInstructor->clave_loc = $request->localidad;
        $saveInstructor->localidad = $locali->localidad;
        $saveInstructor->entidad_nacimiento = $estado_nac->nombre;
        $saveInstructor->municipio_nacimiento = $munic_nac->muni;
        $saveInstructor->clave_loc_nacimiento = $request->localidad_nacimiento;
        $saveInstructor->localidad_nacimiento = $locali_nac->localidad;
        $saveInstructor->domicilio = $request->domicilio;
        $saveInstructor->telefono = $request->telefono;
        $saveInstructor->correo = $request->correo;
        $saveInstructor->banco = $request->banco;
        $saveInstructor->interbancaria = $request->clabe;
        $saveInstructor->no_cuenta = $request->numero_cuenta;
        $saveInstructor->tipo_instructor = $request->tipo_instructor;
        $saveInstructor->nacionalidad = $request->pais_nacimiento;
        if(isset($request->numero_control))
        {
            $saveInstructor->numero_control = $request->numero_control;
        }
        if(!isset($saveInstructor->numero_control))
        {
            $saveInstructor->numero_control = "Pendiente";
        }
        if($saveInstructor->status == 'RETORNO')
        {
            $perfil = $saveInstructor->data_perfil;
            $especialidad = $saveInstructor->data_especialidad;
            $status_array = ['RETORNO','REVALIDACION RETORNADA','BAJA RETORNADA'];
            foreach($perfil as $llave => $luthier)
            {
                switch($luthier['status'])
                {
                    case 'RETORNO':
                        $perfil[$llave]['status'] = 'EN CAPTURA';
                    break;
                    case 'REVALIDACION RETORNADA':
                        $perfil[$llave]['status'] = 'REVALIDACION EN CAPTURA';
                    break;
                    case 'BAJA RETORNADA':
                        $perfil[$llave]['status'] = 'BAJA EN PREVALIDACION';
                    break;
                }
            }
            foreach($especialidad as $rl => $tem)
            {
                switch($luthier['status'])
                {
                    case 'RETORNO':
                        $especialidad[$rl]['status'] = 'EN CAPTURA';
                    break;
                    case 'REVALIDACION RETORNADA':
                        $especialidad[$rl]['status'] = 'REVALIDACION EN CAPTURA';
                    break;
                    case 'BAJA RETORNADA':
                        $especialidad[$rl]['status'] = 'BAJA EN PREVALIDACION';
                    break;
                }
            }

            $saveInstructor->data_perfil = $perfil;
            $saveInstructor->data_especialidad = $especialidad;
        }
        $saveInstructor->status = "EN CAPTURA";
        $saveInstructor->unidades_disponible = $unidades;
        $saveInstructor->tipo_honorario = $request->honorario;
        $saveInstructor->clave_unidad = $unidadregistra->cct;
        $saveInstructor->lastUserId = $userId;
        $saveInstructor->extracurricular = trim($request->extracurricular);
        $saveInstructor->stps = trim($request->stps);
        $saveInstructor->conocer = trim($request->conocer);
        $saveInstructor->turnado = 'UNIDAD';
        $saveInstructor->estado = TRUE;
        $saveInstructor->codigo_postal = $request->codigo_postal;
        $saveInstructor->telefono_casa = $request->telefono_casa;

        if ($request->file('arch_ine') != null)
        {
            $ine = $request->file('arch_ine'); # obtenemos el archivo
            $urline = $this->pdf_upload($ine, $id, 'ine'); # invocamos el método
            $saveInstructor->archivo_ine = $urline; # guardamos el path
        }

        if ($request->file('arch_domicilio') != null)
        {
            $dom = $request->file('arch_domicilio'); # obtenemos el archivo
            $urldom = $this->pdf_upload($dom, $id, 'dom'); # invocamos el método
            $saveInstructor->archivo_domicilio = $urldom; # guardamos el path
        }

        if ($request->file('arch_curp') != null)
        {
            $curp = $request->file('arch_curp'); # obtenemos el archivo
            $urlcurp = $this->pdf_upload($curp, $id, 'curp'); # invocamos el método
            $saveInstructor->archivo_curp = $urlcurp; # guardamos el path
        }

        if ($request->file('arch_alta') != null)
        {
            $alta = $request->file('arch_alta'); # obtenemos el archivo
            $urlalta = $this->pdf_upload($alta, $id, 'alta'); # invocamos el método
            $saveInstructor->archivo_alta = $urlalta; # guardamos el path
        }

        if ($request->file('arch_banco') != null)
        {
            $banco = $request->file('arch_banco'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $id, 'banco'); # invocamos el método
            $saveInstructor->archivo_bancario = $urlbanco; # guardamos el path
        }

        if ($request->file('arch_rfc') != null)
        {
            $rfc = $request->file('arch_rfc'); # obtenemos el archivo
            $urlrfc = $this->pdf_upload($rfc, $id, 'rfc'); # invocamos el método
            $saveInstructor->archivo_rfc = $urlrfc; # guardamos el path
        }

        if ($request->file('arch_foto') != null)
        {
            $foto = $request->file('arch_foto'); # obtenemos el archivo
            $urlfoto = $this->jpg_upload($foto, $id, 'foto'); # invocamos el método
            $saveInstructor->archivo_fotografia = $urlfoto; # guardamos el path
        }

        if ($request->file('arch_estudio') != null)
        {
            $estudio = $request->file('arch_estudio'); # obtenemos el archivo
            $urlestudio = $this->pdf_upload($estudio, $id, 'estudios'); # invocamos el método
            $saveInstructor->archivo_estudios = $urlestudio; # guardamos el path
        }

        if ($request->file('arch_id') != null)
        {
            $otraid = $request->file('arch_id'); # obtenemos el archivo
            $urlotraid = $this->pdf_upload($otraid, $id, 'acta'); # invocamos el método
            $saveInstructor->archivo_otraid = $urlotraid; # guardamos el path
        }
        if ($request->file('arch_curriculum_personal') != null)
        {
            $otraid = $request->file('arch_curriculum_personal'); # obtenemos el archivo
            $urlotraid = $this->pdf_upload($otraid, $id, 'oid'); # invocamos el método
            $saveInstructor->archivo_curriculum_personal = $urlotraid; # guardamos el path
        }
        if($saveInstructor->data_perfil == NULL)
        {
            foreach($perfiles as $cadwell)
            {
                $arrtemp = [
                    'id' => $cadwell->id,
                    'area_carrera' => $cadwell->area_carrera,
                    'estatus' => $cadwell->estatus,
                    'pais_institucion' => $cadwell->pais_institucion,
                    'entidad_institucion' => $cadwell->entidad_institucion,
                    'fecha_expedicion_documento' => $cadwell->fecha_expedicion_documento,
                    'folio_documento' => $cadwell->folio_documento,
                    'numero_control' => $cadwell->numero_control,
                    'ciudad_institucion' => $cadwell->ciudad_institucion,
                    'nombre_institucion' => $cadwell->nombre_institucion,
                    'grado_profesional' => $cadwell->grado_profesional,
                    'experiencia_laboral' => $cadwell->experiencia_laboral,
                    'experiencia_docente' => $cadwell->experiencia_docente,
                    'cursos_recibidos' => $cadwell->cursos_recibidos,
                    'capacitador_icatech' => $cadwell->capacitador_icatech,
                    'recibidos_icatech' => $cadwell->recibidos_icatech,
                    'cursos_impartidos' => $cadwell->cursos_impartidos,
                    'lastUserId' => $cadwell->lastUserId,
                    'carrera' => $cadwell->carrera,
                    'status' => 'VALIDADO',
                    'periodo' => $cadwell->periodo,
                    'new' => FALSE
                ];
                array_push($arrper,$arrtemp);
                $arrtemp = array();
            }
            $saveInstructor->data_perfil = $arrper;

            foreach($especialidades as $moist)
            {
                // dd($moist);
                $arrtemp = [
                    'id' => $moist->id,
                    'especialidad_id' => $moist->especialidad_id,
                    'perfilprof_id' => $moist->perfilprof_id,
                    'unidad_solicita' => $moist->unidad_solicita,
                    'memorandum_validacion' => $moist->memorandum_validacion,
                    'fecha_validacion' => $moist->fecha_validacion,
                    'memorandum_modificacion' => $moist->memorandum_modificacion,
                    'observacion' => $moist->observacion,
                    'criterio_pago_id' => $moist->criterio_pago_id,
                    'lastUserId' => $userId,
                    'activo' => TRUE,
                    'id_instructor' => $moist->idInstructor,
                    'cursos_impartir' => $moist->cursos_impartir,
                    'fecha_solicitud' => $moist->fecha_solicitud,
                    'status' => 'VALIDADO',
                    'memorandum_solicitud' => $moist->memorandum_solicitud,
                    'solicito' => $moist->solicito,
                    'observacion_validacion' => $moist->observacion_validacion,
                    'fecha_baja' => $moist->fecha_baja,
                    'memorandum_baja' => $moist->memorandum_baja,
                    'hvalidacion' => $moist->hvalidacion,
                    'new' => FALSE
                ];
                array_push($arresp, $arrtemp);
                $arrtemp = array();
            }
            $saveInstructor->data_especialidad = $arresp;
        }

        if(isset($request->hispanohablante)) {
            $subproyectos = [];
            $saveInstructor->instructor_alfa = true;
            foreach($request->subproyecto as $subproyecto) {
                switch($subproyecto) {
                    case 'oportunidades':
                        $subproyectos[$subproyecto] = $request->oportunidades_puesto;
                    break;
                    case 'conafe':
                        $subproyectos[$subproyecto] = $request->conafe_puesto;
                    break;
                    case 'sedena':
                        $subproyectos[$subproyecto] = $request->sedena_puesto;
                    break;
                    case 'profesores':
                        $subproyectos[$subproyecto] = $request->profesores_puesto;
                    break;
                    case 'becarios':
                        $subproyectos[$subproyecto] = $request->becarios_puesto;
                    break;
                    case 'osc':
                        $subproyectos[$subproyecto] = $request->osc_puesto;
                    break;
                    case 'ipf':
                        $subproyectos[$subproyecto] = $request->ipf_puesto;
                    break;
                    case 'programas federales':
                        $subproyectos[$subproyecto] = $request->input('programas federales_puesto');
                    break;
                    case 'conevyt':
                        $subproyectos[$subproyecto] = $request->conevyt_puesto;
                    break;
                    case 'oportunidades':
                        $subproyectos[$subproyecto] = $request->oportunidades_puesto;
                    break;
                    case 'instituciones academicas':
                        $subproyectos[$subproyecto] = $request->instituciones_academicas_puesto;
                    break;
                    case 'chiapas puede':
                        $subproyectos[$subproyecto] = $request->chiapas_puede_puesto;
                    break;
                    case 'otro':
                        $subproyectos[$subproyecto] = $request->otrosub_puesto;
                    break;
                }
            }

            $urlalfa = null;
            if ($request->file('arch_alfa') != null)
            {
                $otraid = $request->file('arch_alfa'); # obtenemos el archivo
                $urlalfa = $this->pdf_upload($otraid, $id, 'archivo_alfa'); # invocamos el método
            }

            // $horarios_circulo = [
            //     '1' => ['dia' => $request->dia1, 'inicio' => $request->horario_inicio1, 'termino' => $request->horario_termino1],
            //     '1-2' => ['dia' => $request->dia1_2, 'inicio' => $request->horario_inicio1_2, 'termino' => $request->horario_termino1_2],
            //     '2' => ['dia' => $request->dia2, 'inicio' => $request->horario_inicio2, 'termino' => $request->horario_termino2],
            //     '2-2' => ['dia' => $request->dia2_2, 'inicio' => $request->horario_inicio2_2, 'termino' => $request->horario_termino2_2],
            //     '3' => ['dia' => $request->dia3, 'inicio' => $request->horario_inicio3, 'termino' => $request->horario_termino3],
            //     '3-2' => ['dia' => $request->dia3_2, 'inicio' => $request->horario_inicio3_2, 'termino' => $request->horario_termino3_2]
            // ];

            $datos_alfa = [
                'hispanohablante' => $request->hispanohablante,
                'lengua_indigena' => $request->lengua_indigena,
                'etnia' => $request->etnia,
                'hijos' => $request->hijos,
                'subproyectos' => $subproyectos,
                'tipo_vialidad' => $request->tipo_vialidad,
                'nombre_vialidad' => $request->nombre_vialidad,
                'numero_exterior' => $request->numero_exterior,
                'numero_interior' => $request->numero_interior,
                'entre_tipo_vialidad1' => $request->entre_tipo_vialidad1,
                'entre_vialidad1' => $request->entre_vialidad1,
                'entre_tipo_vialidad2' => $request->entre_tipo_vialidad2,
                'entre_vialidad2' => $request->entre_vialidad2,
                'vialidad_posterior' => $request->vialidad_posterior,
                'carretera' => $request->carretera,
                'tipo_asentamiento_humano' => $request->tipo_asentamiento_humano,
                'nombre_asentamiento_humano' => $request->nombre_asentamiento_humano,
                'descripcion_ubicacion' => $request->descripcion_ubicacion,
                'ocupacion' => $request->ocupacion,
                'sin_ocupacion' => $request->sin_ocupacion,
                'con_ocupacion' => $request->con_ocupacion,
                'ingreso_mensual' => $request->ingreso_mensual,
                'roles' => $request->roles_figura_operativa,
                'unidad_operativa' => $request->unidad_operativa,
                'circulo_estudio' => $request->circulo_estudio,
                'responsable_circulo' => $request->responsable_circulo,
                'fecha_inicio' => $request->fecha_inicio,
                'archivo_alfa' => $urlalfa,
                'pais_residencia' => $request->pais,
                // 'pais_residecia' = $request->
                // 'horario_circulo' => $horarios_circulo,

            ];

            $saveInstructor->datos_alfa = $datos_alfa;
        }

        return $saveInstructor;
    }

    private function guardado_ins_model($saveInstructor,$request,$id)
    {
        // dd($request);
        $arresp = $arrper = $arrtemp = array();
        $userId = Auth::user()->id;
        $useruni = Auth::user()->unidad;
        $perfiles = InstructorPerfil::WHERE('numero_control',$id)->GET();
        $especialidades = especialidad_instructor::WHERE('id_instructor', '=', $id)->GET();
        # Proceso de Guardado
        #----- Personal -----
        $saveInstructor->id = $id;
        $saveInstructor->nombre = $request->nombre;
        $saveInstructor->apellidoPaterno = $request->apellidoPaterno;
        $saveInstructor->apellidoMaterno = $request->apellidoMaterno;
        $saveInstructor->curp = $request->curp;
        $saveInstructor->rfc = $request->rfc;
        $saveInstructor->tipo_identificacion = $request->tipo_identificacion;
        $saveInstructor->expiracion_identificacion = $request->expiracion_identificacion;
        $saveInstructor->folio_ine = $request->folio_ine;
        $saveInstructor->sexo = $request->sexo;
        $saveInstructor->estado_civil = $request->estado_civil;
        $saveInstructor->fecha_nacimiento = $request->fecha_nacimiento;
        $saveInstructor->entidad = $request->entidad;
        $saveInstructor->municipio = $request->municipio;
        $saveInstructor->clave_loc = $request->clave_loc;
        $saveInstructor->localidad = $request->localidad;
        $saveInstructor->entidad_nacimiento = $request->entidad_nacimiento;
        $saveInstructor->municipio_nacimiento = $request->municipio_nacimiento;
        $saveInstructor->clave_loc_nacimiento = $request->clave_loc_nacimiento;
        $saveInstructor->localidad_nacimiento = $request->localidad_nacimiento;
        $saveInstructor->domicilio = $request->domicilio;
        $saveInstructor->telefono = $request->telefono;
        $saveInstructor->correo = $request->correo;
        $saveInstructor->banco = $request->banco;
        $saveInstructor->tipo_instructor = $request->tipo_instructor;
        $saveInstructor->interbancaria = $request->interbancaria;
        $saveInstructor->no_cuenta = $request->no_cuenta;
        $saveInstructor->numero_control = $request->numero_control;
        if(!isset($saveInstructor->numero_control))
        {
            $saveInstructor->numero_control = "Pendiente";
        }
        if($saveInstructor->status == 'RETORNO')
        {
            $perfil = $saveInstructor->data_perfil;
            $especialidad = $saveInstructor->data_especialidad;
            foreach($perfil as $llave => $luthier)
            {
                if($luthier['status'] == 'RETORNO')
                {
                    $perfil[$llave]['status'] = 'EN CAPTURA';
                }
            }
            foreach($especialidad as $rl => $tem)
            {
                if($tem['status'] == 'RETORNO')
                {
                    $especialidad[$rl]['status'] = 'EN CAPTURA';
                }
            }

            $saveInstructor->data_perfil = $perfil;
            $saveInstructor->data_especialidad = $especialidad;
        }
        $saveInstructor->status = "EN CAPTURA";
        $saveInstructor->unidades_disponible = $request->unidades_disponible;
        $saveInstructor->tipo_honorario = $request->tipo_honorario;
        $saveInstructor->clave_unidad = $request->clave_unidad;
        $saveInstructor->lastUserId = $userId;
        $saveInstructor->extracurricular = $request->extracurricular;
        $saveInstructor->stps = $request->stps;
        $saveInstructor->conocer = $request->conocer;
        $saveInstructor->turnado = 'UNIDAD';
        $saveInstructor->estado = TRUE;
        $saveInstructor->codigo_postal = $request->codigo_postal;
        $saveInstructor->telefono_casa = $request->telefono_casa;
        $saveInstructor->archivo_ine = $request->archivo_ine;
        $saveInstructor->archivo_domicilio = $request->archivo_domicilio;
        $saveInstructor->archivo_curp = $request->archivo_curp;
        $saveInstructor->archivo_alta = $request->archivo_alta;
        $saveInstructor->archivo_bancario = $request->archivo_bancario;
        $saveInstructor->archivo_rfc = $request->archivo_rfc;
        $saveInstructor->archivo_fotografia = $request->archivo_fotografia;
        $saveInstructor->archivo_estudios = $request->archivo_estudios;
        $saveInstructor->archivo_otraid = $request->archivo_otraid;
        $saveInstructor->archivo_curriculum_personal = $request->archivo_curriculum_personal;

        foreach($perfiles as $cadwell)
        {
            $arrtemp = [
                'id' => $cadwell->id,
                'area_carrera' => $cadwell->area_carrera,
                'estatus' => $cadwell->estatus,
                'pais_institucion' => $cadwell->pais_institucion,
                'entidad_institucion' => $cadwell->entidad_institucion,
                'fecha_expedicion_documento' => $cadwell->fecha_expedicion_documento,
                'folio_documento' => $cadwell->folio_documento,
                'numero_control' => $cadwell->numero_control,
                'ciudad_institucion' => $cadwell->ciudad_institucion,
                'nombre_institucion' => $cadwell->nombre_institucion,
                'grado_profesional' => $cadwell->grado_profesional,
                'experiencia_laboral' => $cadwell->experiencia_laboral,
                'experiencia_docente' => $cadwell->experiencia_docente,
                'cursos_recibidos' => $cadwell->cursos_recibidos,
                'capacitador_icatech' => $cadwell->capacitador_icatech,
                'recibidos_icatech' => $cadwell->recibidos_icatech,
                'cursos_impartidos' => $cadwell->cursos_impartidos,
                'lastUserId' => $cadwell->lastUserId,
                'carrera' => $cadwell->carrera,
                'status' => 'VALIDADO',
                'periodo' => $cadwell->periodo,
                'new' => FALSE
            ];
            array_push($arrper,$arrtemp);
            $arrtemp = array();
        }
        $saveInstructor->data_perfil = $arrper;

        foreach($especialidades as $moist)
        {
            // dd($moist);
            $arrtemp = [
                'id' => $moist->id,
                'especialidad_id' => $moist->especialidad_id,
                'perfilprof_id' => $moist->perfilprof_id,
                'unidad_solicita' => $moist->unidad_solicita,
                'memorandum_validacion' => $moist->memorandum_validacion,
                'fecha_validacion' => $moist->fecha_validacion,
                'memorandum_modificacion' => $moist->memorandum_modificacion,
                'observacion' => $moist->observacion,
                'criterio_pago_id' => $moist->criterio_pago_id,
                'lastUserId' => $userId,
                'activo' => TRUE,
                'id_instructor' => $moist->idInstructor,
                'cursos_impartir' => $moist->cursos_impartir,
                'fecha_solicitud' => $moist->fecha_solicitud,
                'status' => 'VALIDADO',
                'memorandum_solicitud' => $moist->memorandum_solicitud,
                'solicito' => $moist->solicito,
                'observacion_validacion' => $moist->observacion_validacion,
                'fecha_baja' => $moist->fecha_baja,
                'memorandum_baja' => $moist->memorandum_baja,
                'hvalidacion' => $moist->hvalidacion,
                'new' => FALSE
            ];
            array_push($arresp, $arrtemp);
            $arrtemp = array();
        }
        $saveInstructor->data_especialidad = $arresp;

        // dd($saveInstructor);
        return $saveInstructor;
    }

    private function new_history($newa, $instructor, $movimiento)
    {
        $historico = new instructor_history;
        $historico->id_instructor = $instructor->id;
        $historico->id_user = $instructor->lastUserId;
        $historico->movimiento = 'creacion de instructor por parte de la unidad';
        $historico->status = $instructor->status;
        $historico->turnado = $instructor->turnado;
        $historico->data_instructor = $newa["\x00*\x00attributes"];
        $historico->nrevision = $instructor->nrevision;
        $historico->save();

        return NULL;
    }

    private function make_collection($data)
    {
        if(isset($data))
        {
            $newarr = array();
            foreach($data as $cadwell)
            {
                array_push($newarr, (object) $cadwell);
            }
            $perfil = collect($newarr);
            return $perfil;
        }
        else
        {
            return FALSE;
        }
    }

    public function curso_extra_upd(Request $request)
    {
        // dd($request);
        //instructor::where('id', '=', $request->id_instructor_cursoext)->update(['curso_extra' => $request->extra]);
       // return redirect()->route('instructor-inicio');
        if($request->id_instructor and $request->estado){
            $id_instructor = $request->id_instructor;
            $estado = $request->estado;
            $result =  instructor::where('id', '=', $request->id_instructor)->update(['curso_extra' => $estado]);
        }
        if($result){
            if($estado == "true") $msg = "CURSO EXTRA ACTIVADO.";
            else $msg = "CURSO EXTRA DESACTIVADO.";
        }else $msg = "F5 para actualizar y volver a intentar";
        return $msg;

    }

    public function iestado(Request $request)
    {
        if($request->id_instructor and $request->estado){

            $id_instructor = $request->id_instructor;
            $estado = $request->estado;
            $result =  instructor::where('id', '=', $request->id_instructor)->update(['estado' => $estado]);
        }
        if($result){
            if($estado == "true") $msg = "INSTRUCTOR ACTIVADO.";
            else $msg = "INSTRUCTOR DESACTIVADO.";
        }else $msg = "F5 para actualizar y volver a intentar";
        return $msg;
    }

    protected function getlocalidades(Request $request)
    {
        if (isset($request->valor)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            // $nombreMuni = $request->valor;
            $idMuni = DB::TABLE('tbl_municipios')->SELECT('clave','id_estado')->WHERE('id', '=', $request->valor)->FIRST();
            $locals = DB::TABLE('tbl_localidades')->SELECT('clave', 'localidad')
                        ->WHERE('tbl_localidades.clave_municipio', '=', $idMuni->clave)
                        ->WHERE('tbl_localidades.id_estado', '=', $idMuni->id_estado)
                        ->ORDERBY('localidad','ASC')
                        ->GET();
            $json=json_encode($locals);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }


        return $json;
    }

    protected function getmunicipios(Request $request)
    {
        if (isset($request->valor)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $locals = DB::TABLE('tbl_municipios')->SELECT('id','muni')
                        ->WHERE('tbl_municipios.id_estado', '=', $request->valor)
                        ->ORDERBY('muni','ASC')
                        ->GET();
            $json=json_encode($locals);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function getcursos(Request $request)
    {
        if (isset($request->valor)){
            $cursos = curso::WHERE('id_especialidad', '=', $request->valor)->WHERE('estado', '=', TRUE)->ORDERBY('nombre_curso', 'ASC')->GET(['id', 'nombre_curso', 'modalidad', 'objetivo', 'costo', 'duracion', 'objetivo', 'tipo_curso', 'id_especialidad', 'rango_criterio_pago_minimo', 'rango_criterio_pago_maximo']);
            // $nomesp = especialidad::SELECT('nombre')->WHERE('id', '=', $id)->FIRST();
            // $cursos->nomesp = $nomesp->nombre;
            foreach($cursos as $item)
            {
                $item->btn = '<input type="checkbox" class="checkBoxClass" id="tgl' . $item->id . '"
                data-toggle="toggle"
                data-style="ios"
                data-on=" "
                data-off=" "
                data-onstyle="success"
                data-offstyle="danger"
                name="itemAdd[' . $item->id . '][check_cursos]"
                value="' . $item->id . '">';
            }
            $json=json_encode($cursos);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }


        return $json;
    }

    protected function getnrevision(Request $request)
    {
        if (isset($request->valor)){
            $rol = DB::TABLE('role_user')->WHERE('role_id', '=', '1')->WHERE('user_id', '=', Auth::user()->id)->FIRST();
            if(isset($rol))
            {
                $status = ['EN CAPTURA','RETORNO','PREVALIDACION','EN FIRMA','BAJA EN FIRMA','REACTIVACION EN PREVALIDACION','REACTIVACION EN FIRMA'];
            }
            else
            {
               $status = ['PREVALIDACION','EN FIRMA', 'BAJA EN PREVALIDACION','BAJA EN FIRMA','REACTIVACION EN PREVALIDACION','REACTIVACION EN FIRMA'];
            }
            $revisiones = pre_instructor::SELECT('nrevision')
                        ->WhereJsonContains('data_especialidad', [['unidad_solicita' => $request->valor]])
                        ->WHERE('registro_activo', TRUE)
                        ->WHERE('nrevision', '!=', NULL)
                        ->WHERE('turnado', 'DTA')
                        ->WHEREIN('status', $status)
                        ->ORDERBY('nrevision', 'ASC')
                        ->GET();
            $json=json_encode($revisiones);
        }
        else
        {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function getentrevista(Request $request)
    {
        $entrevista = DB::TABLE('instructores')->SELECT('entrevista')->WHERE('id','=',$request->id)->FIRST();
        $json = $entrevista->entrevista;
        return $json;
    }

    protected function nomesp(Request $request)
    {
        //Analiza si ya tiene la especialidad asignada
        $chckespecialidad = DB::TABLE('especialidad_instructores')
                            ->WHERE('id_instructor', '=', $request->idins)
                            ->WHERE('especialidad_id', '=', $request->valor)
                            ->FIRST();
        if($chckespecialidad == NULL){
            $esp = especialidad::SELECT('nombre')->WHERE('id', '=', $request->valor)->FIRST();
            $json=json_encode($esp);
        }
        else
        {
            $esp = especialidad::SELECT('nombre')->WHERE('id', '=', $request->valor)->FIRST();
            $mensaje = "Lo sentimos, la especialidad ".$esp->nombre." ya esta asociada a este instructor.";
            $json=json_encode(array($mensaje));
        }


        return $json;
    }

    public function perfilinstructor_detalles(Request $request)
    {
        $insper = InstructorPerfil::FIND($request->id);
        $json=json_encode($insper);
        return $json;
    }

    public function especialidadvalidada_detalles(Request $request)
    {
        $lista = NULL;
        $insesp = especialidad_instructor::FIND($request->id);

        $perfil = InstructorPerfil::SELECT('area_carrera', 'grado_profesional')->WHERE('id', '=', $insesp->perfilprof_id)->FIRST();
        $especialidad = especialidad::SELECT('nombre')->WHERE('id', '=', $insesp->especialidad_id)->FIRST();
        $cp = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')->WHERE('id', '=', $insesp->criterio_pago_id)->FIRST();
        $cursos = curso::SELECT('nombre_curso')->WHEREIN('id', $insesp->cursos_impartir)->GET();
        foreach($cursos as $key => $cadwell)
        {
            if($key == 0)
            {
                $lista = '<li>' . $cadwell->nombre_curso . '</li>';
            }
            else
            {
                $lista = $lista . '<li>' . $cadwell->nombre_curso . '</li>';
            }
        }
        $insesp->perfilprof = $perfil->grado_profesional . ' ' . $perfil->area_carrera;
        $insesp->especialidad = $especialidad->nombre;
        $insesp->cp = $cp->perfil_profesional;
        $insesp->cursos = $lista;
        $json=json_encode($insesp);
        return $json;
    }

    private function guardado_oficial($saveInstructor)
    {
        // dd($saveInstructor);
        // INICIO DE GUARDADO OFICIAL
        $instructor = instructor::find($saveInstructor->id);
        $instructor->nombre = trim($saveInstructor->nombre);
        $instructor->apellidoPaterno = trim($saveInstructor->apellidoPaterno);
        $instructor->apellidoMaterno = trim($saveInstructor->apellidoMaterno);
        $instructor->curp = trim($saveInstructor->curp);
        $instructor->rfc = $saveInstructor->rfc;
        $instructor->tipo_identificacion = trim($saveInstructor->tipo_identificacion);
        $instructor->expiracion_identificacion = trim($saveInstructor->expiracion_identificacion);
        $instructor->folio_ine = $saveInstructor->folio_ine;
        $instructor->sexo = $saveInstructor->sexo;
        $instructor->estado_civil = $saveInstructor->estado_civil;
        $instructor->fecha_nacimiento = $saveInstructor->fecha_nacimiento;
        $instructor->entidad = $saveInstructor->entidad;
        $instructor->municipio = $saveInstructor->municipio;
        $instructor->clave_loc = $saveInstructor->clave_loc;
        $instructor->localidad = $saveInstructor->localidad;
        $instructor->entidad_nacimiento = $saveInstructor->entidad_nacimiento;
        $instructor->municipio_nacimiento = $saveInstructor->municipio_nacimiento;
        $instructor->clave_loc_nacimiento = $saveInstructor->clave_localidad_nacimiento;
        $instructor->localidad_nacimiento = $saveInstructor->localidad_nacimiento;
        $instructor->domicilio = $saveInstructor->domicilio;
        $instructor->telefono = $saveInstructor->telefono;
        $instructor->correo = $saveInstructor->correo;
        $instructor->banco = $saveInstructor->banco;
        $instructor->interbancaria = $saveInstructor->interbancaria;
        $instructor->no_cuenta = $saveInstructor->no_cuenta;
        $instructor->numero_control = $saveInstructor->numero_control;
        $instructor->status = $saveInstructor->status;
        $instructor->unidades_disponible = $saveInstructor->unidades_disponible;
        $instructor->tipo_honorario = $saveInstructor->tipo_honorario;
        $instructor->clave_unidad = $saveInstructor->clave_unidad;
        $instructor->lastUserId = $saveInstructor->lastUserId;
        $instructor->extracurricular = $saveInstructor->extracurricular;
        $instructor->stps = $saveInstructor->stps;
        $instructor->conocer = $saveInstructor->conocer;
        $instructor->turnado = $saveInstructor->turnado;
        $instructor->estado = $saveInstructor->estado;
        $instructor->codigo_postal = $saveInstructor->codigo_postal;
        $instructor->telefono_casa = $saveInstructor->telefono_casa;
        $instructor->archivo_ine = $saveInstructor->archivo_ine;
        $instructor->archivo_domicilio = $saveInstructor->archivo_domicilio;
        $instructor->archivo_curp = $saveInstructor->archivo_curp;
        $instructor->archivo_alta = $saveInstructor->archivo_alta;
        $instructor->archivo_bancario = $saveInstructor->archivo_bancario;
        $instructor->archivo_rfc = $saveInstructor->archivo_rfc;
        $instructor->archivo_fotografia = $saveInstructor->archivo_fotografia;
        $instructor->archivo_estudios = $saveInstructor->archivo_estudios;
        $instructor->archivo_otraid = $saveInstructor->archivo_otraid;
        $instructor->nrevision = $saveInstructor->nrevision;
        $instructor->entrevista = $saveInstructor->entrevista;
        $instructor->exp_laboral = $saveInstructor->exp_laboral;
        $instructor->exp_docente = $saveInstructor->exp_docente;
        $instructor->tipo_instructor = $saveInstructor->tipo_instructor;
        $instructor->telefono_casa = $saveInstructor->telefono_casa;
        $instructor->curriculum = $saveInstructor->curriculum;
        $instructor->clave_loc_nacimiento = $saveInstructor->clave_loc_nacimiento;
        $instructor->instructor_alfa = $saveInstructor->instructor_alfa;
        $instructor->datos_alfa = $saveInstructor->datos_alfa;
        $instructor->nacionalidad = $saveInstructor->nacionalidad;
        $instructor->save();

        $data_ins_curso = tbl_curso::Select('tbl_cursos.id')
        ->LeftJoin('pagos','pagos.id_curso','tbl_cursos.id')
        ->Join('folios','folios.id_cursos', 'tbl_cursos.id')
        ->Where('id_instructor',$saveInstructor->id)
        ->where(function ($query) {
            $query->whereNotIn('pagos.status_recepcion', ['VALIDADO', 'recepcion tradicional'])
                ->orWhereNull('pagos.status_recepcion')
                ->orWhere('pagos.status_recepcion', 'Rechazado')
                ->orWhere('folios.edicion_pago','TRUE');
        })
        ->Get();

        if(isset($data_ins_curso)) {

            foreach($data_ins_curso as $prime) {
                $upd_curso = tbl_curso::Find($prime->id);
                $upd_curso->soportes_instructor = $instructor->soportesInstructor();
                $upd_curso->save();
            }
        }

        return $instructor;
    }

    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }

    protected function basic_array($data)
    {
        $entrevista = array();
        $arrtemp = explode(',',str_replace(['"','{','}',' ','[',']'], '', $data));dd($data);
        foreach( $arrtemp as $val )
        {
            $tmp = explode( ':', $val );

            $entrevista[ $tmp[0] ] = $tmp[1];
            if(isset($tmp[2]))
            {
                $entrevista[$tmp[0]] = $entrevista[$tmp[0]] . ':' . $tmp[2];
            }
        }// dd($entrevista);
        return $entrevista;
    }

    protected function complex_array($data)
    {
        $respond = $innerarr = array();
        $arrtemp = explode('},{',str_replace(' ','',$data));
        foreach($arrtemp as $cadwell)
        {
            $innerarr = NULL;
            $getin = explode(',',str_replace(['"','{','}',' ','[',']'], '',$cadwell));
            foreach( $getin as $val )
            {
                $tmp = explode( ':', $val );
                $innerarr[ $tmp[0] ] = $tmp[1];
                if(isset($tmp[2]))
                {
                    $innerarr[$tmp[0 ]] = $innerarr[$tmp[0]] . ':' . $tmp[2];
                }
            }
            array_push($respond, $innerarr);
        }
        // dd($respond);
        return $respond;
    }

    public function funcionarios($unidad) {
        $query = clone $dacademico = clone $dacademico_unidad = clone $gestionacademica = clone $dunidad = DB::Table('tbl_organismos AS o')->Select('f.titulo','f.nombre','f.cargo','f.direccion','f.telefono','f.correo_institucional')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true')
            ->Where('f.titular', true);

        $dacademico = $dacademico->Where('o.id',16)->First();
        $gestionacademica = $gestionacademica->Where('o.id',17)->First();

        $dacademico_unidad = $dacademico_unidad->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DEPARTAMENTO ACADEMICO%')
            ->Where('u.unidad', $unidad)
            ->First();

        $dunidad = $dunidad->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $funcionarios = [
            'dacademico' => ['titulo'=>$dacademico->titulo,'nombre'=>$dacademico->nombre,'puesto'=>$dacademico->cargo,'direccion'=>$dacademico->direccion,'telefono'=>$dacademico->telefono,'correo'=>$dacademico->correo_institucional],
            'gestionacademica' => ['titulo'=>$gestionacademica->titulo,'nombre'=>$gestionacademica->nombre,'puesto'=>$gestionacademica->cargo,'direccion'=>$gestionacademica->direccion,'telefono'=>$gestionacademica->telefono,'correo'=>$gestionacademica->correo_institucional],
            'dacademico_unidad' => ['titulo'=>$dacademico_unidad->titulo,'nombre'=>$dacademico_unidad->nombre,'puesto'=>$dacademico_unidad->cargo,'direccion'=>$dacademico_unidad->direccion,'telefono'=>$dacademico_unidad->telefono,'correo'=>$dacademico_unidad->correo_institucional],
            'dunidad' => ['titulo'=>$dunidad->titulo,'nombre'=>$dunidad->nombre,'puesto'=>$dunidad->cargo,'direccion'=>$dunidad->direccion,'telefono'=>$dunidad->telefono,'correo'=>$dunidad->correo_institucional],
            'elabora' => ['nombre'=>strtoupper(Auth::user()->name),'puesto'=>strtoupper(Auth::user()->puesto)]
        ];

        return $funcionarios;
    }

    private function instructor_alfa_nulo()
    {
        $datos_null = [
            "etnia" => null,
            "hijos" => null,
            "roles" => null,
            "carretera" => null,
            "ocupacion" => null,
            "archivo_alfa" => null,
            "fecha_inicio" => null,
            "subproyectos" => null,
            "con_ocupacion" => null,
            "sin_ocupacion" => null,
            "tipo_vialidad" => null,
            "circulo_estudio" => null,
            "entre_vialidad1" => null,
            "entre_vialidad2" => null,
            "hispanohablante" => null,
            "ingreso_mensual" => null,
            "lengua_indigena" => null,
            "nombre_vialidad" => null,
            "numero_exterior" => null,
            "numero_interior" => null,
            "unidad_operativa" => null,
            "vialidad_posterior" => null,
            "responsable_circulo" => null,
            "entre_tipo_vialidad1" => null,
            "entre_tipo_vialidad2" => null,
            "descripcion_ubicacion" => null,
            "tipo_asentamiento_humano" => null,
            "nombre_asentamiento_humano" => null
        ];
        return $datos_null;
    }
}

