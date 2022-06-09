<?php

namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\cursoValidado;
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
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\tbl_curso;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport;

class InstructorController extends Controller
{
    public function prueba()
    {
        $idesin = DB::table('especialidad_instructores')->SELECT('id')->OrderBy('id', 'ASC')->GET();

        foreach ($idesin as $key => $cadwell)
        {
            $cursos = DB::table('especialidad_instructor_curso')->SELECT('curso_id')
                          ->WHERE('id_especialidad_instructor', '=', $cadwell->id)
                          ->WHERE('activo', '=', TRUE)
                          ->OrderBy('curso_id', 'ASC')
                          ->GET();

            $array = [];
            foreach ($cursos as $data)
            {
                array_push($array, $data->curso_id);
            }

            especialidad_instructor::WHERE('id', '=', $cadwell->id)
                                ->update(['cursos_impartir' => $array]);
        }
        dd('Lock&Load');
    }

    public function index(Request $request)
    {
        $busquedaInstructor = $request->get('busquedaPorInstructor');
        $tipoInstructor = $request->get('tipo_busqueda_instructor');

        $unidadUser = Auth::user()->unidad;

        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();
        if($roles[0]->role_name == 'admin' || $roles[0]->role_name == 'depto_academico' || $roles[0]->role_name == 'depto_academico_instructor' || $roles[0]->role_name == 'auxiliar_cursos')
        {
            $data = instructor::searchinstructor($tipoInstructor, $busquedaInstructor)->WHERE('id', '!=', '0')
            ->WHEREIN('estado' , [TRUE,FALSE])
            ->PAGINATE(25, ['nombre', 'telefono', 'status', 'apellidoPaterno', 'apellidoMaterno', 'numero_control', 'id']);
        }
        else
        {
            $data = instructor::searchinstructor($tipoInstructor, $busquedaInstructor)->WHERE('id', '!=', '0')
            ->WHERE('estado' ,'=', true)
            ->PAGINATE(25, ['nombre', 'telefono', 'status', 'apellidoPaterno', 'apellidoMaterno', 'numero_control', 'id']);
        }
        return view('layouts.pages.initinstructor', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    #----- instructor/crear -----#
    public function crear_instructor()
    {
        return view('layouts.pages.frminstructor');
    }

    #----- instructor/guardar -----#
    public function guardar_instructor(Request $request)
    {
        // dd('hola');
        $userId = Auth::user()->id;

        $verify = instructor::WHERE('curp','=', $request->curp)->FIRST();
        // dd($verify);
        if(is_null($verify) == TRUE)
        {
            $uid = instructor::select('id')->WHERE('id', '!=', '0')->orderby('id','desc')->first();
            $saveInstructor = new instructor();
            if ($uid['id'] === null) {
                # si es nulo entra una vez y se le asigna un valor
                $id = 1;
            } else {
                # entra pero no se le asigna valor
                $id = $uid->id + 1;
            }

            # Proceso de Guardado
            #----- Personal -----
            $saveInstructor->id = $id;
            $saveInstructor->nombre = trim($request->nombre);
            $saveInstructor->apellidoPaterno = trim($request->apellido_paterno);
            $saveInstructor->apellidoMaterno = trim($request->apellido_materno);
            $saveInstructor->curp = trim($request->curp);
            $saveInstructor->banco = $request->banco;
            $saveInstructor->interbancaria = $request->clabe;
            $saveInstructor->no_cuenta = $request->numero_cuenta;
            $saveInstructor->domicilio = $request->domicilio;
            $saveInstructor->numero_control = "Pendiente";
            $saveInstructor->status = "En Proceso";
            $saveInstructor->estado = TRUE;
            $saveInstructor->lastUserId = $userId;

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
                $urlotraid = $this->pdf_upload($otraid, $id, 'oid'); # invocamos el método
                $saveInstructor->archivo_otraid = $urlotraid; # guardamos el path
            }

            $saveInstructor->save();

            return redirect()->route('instructor-inicio')
                        ->with('success','Perfil profesional agregado');
        }
        else
        {
            $clave_instructor = instructor::WHERE('curp', '=', $request->curp)->VALUE('numero_control');
            $mensaje = "Lo sentimos, la curp ".$request->curp." asociada a este registro ya se encuentra en la base de datos al instructor con clave ".$clave_instructor.".";
            return redirect('/instructor/crear')->withErrors($mensaje);
        }
    }

    public function validar($id)
    {
        $instructor = new instructor();
        $getinstructor = $instructor->findOrFail($id);
        $data2 = tbl_unidades::SELECT('unidad','cct')->WHERE('id','!=','0')->ORDERBY('unidad', 'ASC')->GET();
        $localidades = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.id','localidad','muni')
                        ->WHERE('tbl_localidades.id','!=','0')
                        ->LEFTJOIN('tbl_municipios','tbl_municipios.id','=','tbl_localidades.clave_municipio')
                        ->ORDERBY('localidad','ASC')->GET();
        $municipios = DB::TABLE('tbl_municipios')->SELECT('muni')->WHERE('id_estado', '=', '7')
                        ->ORDERBY('muni','ASC')->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->ORDERBY('nombre','ASC')->GET();

        // dd($municipios);
        // var_dump($data2);
        // echo $data2[0]['unidad'];
        return view('layouts.pages.validarinstructor', compact('getinstructor','data2','localidades','municipios','estados'));
    }

    public function rechazo_save(Request $request)
    {
        $userId = Auth::user()->id;

        $saveInstructor = instructor::find($request->id);
        $saveInstructor->rechazo = $request->comentario_rechazo;
        $saveInstructor->status = "Rechazado";
        $saveInstructor->lastUserId = $userId;
        $saveInstructor->save();

        return redirect()->route('instructor-inicio')
            ->with('success','Instructor Rechazado');
    }

    public function validado_save(Request $request)
    {
        $userId = Auth::user()->id;
        $unidades = ['TUXTLA', 'TAPACHULA', 'COMITAN', 'REFORMA', 'TONALA', 'VILLAFLORES', 'JIQUIPILAS', 'CATAZAJA',
        'YAJALON', 'SAN CRISTOBAL', 'CHIAPA DE CORZO', 'MOTOZINTLA', 'BERRIOZABAL', 'PIJIJIAPAN', 'JITOTOL',
        'LA CONCORDIA', 'VENUSTIANO CARRANZA', 'TILA', 'TEOPISCA', 'OCOSINGO', 'CINTALAPA', 'COPAINALA',
        'SOYALO', 'ANGEL ALBINO CORZO', 'ARRIAGA', 'PICHUCALCO', 'JUAREZ', 'SIMOJOVEL', 'MAPASTEPEC',
        'VILLA CORZO', 'CACAHOATAN', 'ONCE DE ABRIL', 'TUXTLA CHICO', 'OXCHUC', 'CHAMULA', 'OSTUACAN',
        'PALENQUE'];
        $locali = DB::TABLE('tbl_localidades')->SELECT('localidad')
                    ->WHERE('clave','=', $request->localidad)->FIRST();
        $estado = DB::TABLE('estados')->SELECT('nombre')->WHERE('id', '=', $request->entidad)->FIRST();
        $munic = DB::TABLE('tbl_municipios')->SELECT('muni')->WHERE('id', '=', $request->municipio)->FIRST();
        // dd($request->localidad);

        $instructor = instructor::find($request->id);

        $instructor->rfc = trim($request->rfc);
        $instructor->folio_ine = trim($request->folio_ine);
        $instructor->sexo = trim($request->sexo);
        $instructor->estado_civil = trim($request->estado_civil);
        $instructor->fecha_nacimiento = $request->fecha_nacimientoins;
        $instructor->entidad = $estado->nombre;
        $instructor->municipio = $munic->muni;
        $instructor->asentamiento = trim($request->asentamiento);
        $instructor->telefono = trim($request->telefono);
        $instructor->correo = trim($request->correo);
        $instructor->tipo_honorario = trim($request->honorario);
        $instructor->clave_unidad = trim($request->unidad_registra);
        $instructor->status = "Validado";
        $instructor->estado = TRUE;
        $instructor->unidades_disponible = $unidades;
        $instructor->lastUserId = $userId;
        $instructor->clave_loc = $request->localidad;
        $instructor->localidad = $locali->localidad;

        //Creacion de el numero de control
        $uni = substr($request->unidad_registra, -3, 2) * 1 . substr($request->unidad_registra, -1);
        $now = Carbon::now();
        $year = substr($now->year, -2);
        $rfcpart = substr($request->rfc, 0, 10);
        $numero_control = $uni.$year.$rfcpart;
        $instructor->numero_control = trim($numero_control);
        $instructor->save();

            return redirect()->route('instructor-inicio')
            ->with('success','Instructor Validado');
    }

    public function editar($id)
    {
        $instructor = new instructor();
        $datains = instructor::WHERE('id', '=', $id)->FIRST();

        return view('layouts.pages.editarinstructor', compact('datains'));
    }

    public function guardar_mod(Request $request)
    {
        $userId = Auth::user()->id;
        $modInstructor = instructor::find($request->id);

        $modInstructor->nombre = trim($request->nombre);
        $modInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $modInstructor->apellidoMaterno = trim($request->apellido_materno);
        $modInstructor->banco = $request->banco;
        $modInstructor->interbancaria = $request->clabe;
        $modInstructor->no_cuenta = $request->numero_cuenta;
        $modInstructor->domicilio = $request->domicilio;
        $modInstructor->status = "En Proceso";
        $modInstructor->lastUserId = $userId;

        if ($request->file('arch_ine') != null)
        {
            $ine = $request->file('arch_ine'); # obtenemos el archivo
            $urline = $this->pdf_upload($ine, $request->id, 'ine'); # invocamos el método
            $modInstructor->archivo_ine = $urline; # guardamos el path
        }

        if ($request->file('arch_domicilio') != null)
        {
            $dom = $request->file('arch_domicilio'); # obtenemos el archivo
            $urldom = $this->pdf_upload($dom, $request->id, 'dom'); # invocamos el método
            $modInstructor->archivo_domicilio = $urldom; # guardamos el path
        }

        if ($request->file('arch_curp') != null)
        {
            $curp = $request->file('arch_curp'); # obtenemos el archivo
            $urlcurp = $this->pdf_upload($curp, $request->id, 'curp'); # invocamos el método
            $modInstructor->archivo_curp = $urlcurp; # guardamos el path
        }

        if ($request->file('arch_alta') != null)
        {
            $alta = $request->file('arch_alta'); # obtenemos el archivo
            $urlalta = $this->pdf_upload($alta, $request->id, 'alta'); # invocamos el método
            $modInstructor->archivo_alta = $urlalta; # guardamos el path
        }

        if ($request->file('arch_banco') != null)
        {
            $banco = $request->file('arch_banco'); # obtenemos el archivo
            $urlbanco = $this->pdf_upload($banco, $request->id, 'banco'); # invocamos el método
            $modInstructor->archivo_bancario = $urlbanco; # guardamos el path
        }

        if ($request->file('arch_rfc') != null)
            {
                $rfc = $request->file('arch_rfc'); # obtenemos el archivo
                $urlrfc = $this->pdf_upload($rfc, $request->id, 'rfc'); # invocamos el método
                $modInstructor->archivo_rfc = $urlrfc; # guardamos el path
            }

        if ($request->file('arch_foto') != null)
        {
            $foto = $request->file('arch_foto'); # obtenemos el archivo
            $urlfoto = $this->jpg_upload($foto, $request->id, 'foto'); # invocamos el método
            $modInstructor->archivo_fotografia = $urlfoto; # guardamos el path
        }

        if ($request->file('arch_estudio') != null)
        {
            $estudio = $request->file('arch_estudio'); # obtenemos el archivo
            $urlestudio = $this->pdf_upload($estudio, $request->id, 'estudios'); # invocamos el método
            $modInstructor->archivo_estudios = $urlestudio; # guardamos el path
        }

        if ($request->file('arch_id') != null)
        {
            $otraid = $request->file('arch_id'); # obtenemos el archivo
            $urlotraid = $this->pdf_upload($otraid, $request->id, 'oid'); # invocamos el método
            $modInstructor->archivo_otraid = $urlotraid; # guardamos el path
        }

        $modInstructor->save();

        return redirect()->route('instructor-inicio')
                    ->with('success','Instructor Modificado');
    }

    public function ver_instructor($id)
    {
        $localidades = null;
        $estado_civil = null;
        $instructor_perfil = new InstructorPerfil();
        $curso_validado = new cursoValidado();
        $det_curso = new Curso();
        $datains = instructor::WHERE('id', '=', $id)->FIRST();

        $lista_civil = estado_civil::WHERE('nombre', '!=', $datains->estado_civil)->GET();
        if ($datains->estado_civil != NULL )
        {
            $estado_civil = estado_civil::WHERE('nombre', '=', $datains->estado_civil)->FIRST();
        }
        $idest = DB::TABLE('estados')->WHERE('nombre','=',$datains->entidad)->FIRST();
        $unidad = tbl_unidades::WHERE('cct', '=', $datains->clave_unidad)->FIRST();
        $lista_unidad = tbl_unidades::WHERE('cct', '!=', $datains->clave_unidad)->GET();
        $estados = DB::TABLE('estados')->SELECT('id','nombre')->GET();
        $municipios = DB::TABLE('tbl_municipios')->SELECT('id','muni')->WHERE('id_estado', '=', $idest->id)
                        ->ORDERBY('muni','ASC')->GET();

        if($datains->municipio != NULL)
        {
            $munix = DB::TABLE('tbl_municipios')->SELECT('clave', 'id_estado')->WHERE('muni', '=', $datains->municipio)->FIRST();

            if($munix != NULL)
            {
                $localidades = DB::TABLE('tbl_localidades')->SELECT('tbl_localidades.clave','localidad')
                                ->WHERE('tbl_localidades.clave_municipio', '=', $munix->clave)
                                ->WHERE('tbl_localidades.id_estado', '=', $munix->id_estado)
                                ->ORDERBY('tbl_localidades.localidad', 'ASC')
                                ->GET();
            }
            // dd($localidades);
        }

        $perfil = $instructor_perfil->WHERE('numero_control', '=', $id)->GET();
        // consulta
        $validado = $instructor_perfil->SELECT('especialidades.nombre', 'especialidad_instructores.id as espinid',
        'especialidad_instructores.observacion', 'especialidad_instructores.id AS especialidadinsid',
        'especialidad_instructores.memorandum_validacion','especialidad_instructores.criterio_pago_id',
        'especialidad_instructores.fecha_validacion','especialidad_instructores.activo')
                        ->WHERE('instructor_perfil.numero_control', '=', $id)
                        ->RIGHTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id','=','instructor_perfil.id')
                        ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                        ->GET();
        return view('layouts.pages.verinstructor', compact('datains','estado_civil','lista_civil','unidad','lista_unidad','perfil','validado', 'localidades','municipios','estados'));
    }

    public function save_ins(Request $request)
    {
        // dd($request->localidad);
        $userId = Auth::user()->id;
        $modInstructor = instructor::find($request->id);
        $locali = DB::TABLE('tbl_localidades')
                    ->WHERE('clave','=', $request->localidad)->VALUE('localidad');
        $estado = DB::TABLE('estados')->SELECT('nombre')->WHERE('id', '=', $request->entidad)->FIRST();
        $munic = DB::TABLE('tbl_municipios')->SELECT('muni')->WHERE('id', '=', $request->municipio)->FIRST();
        // dd ($locali);

        $old = $modInstructor->apellidoPaterno . ' ' . $modInstructor->apellidoMaterno . ' ' . $modInstructor->nombre;
        $new = $request->apellido_paterno . ' ' . $request->apellido_materno . ' ' . $request->nombre;

        $modInstructor->nombre = trim($request->nombre);
        $modInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $modInstructor->apellidoMaterno = trim($request->apellido_materno);
        $modInstructor->curp = trim($request->curp);
        $modInstructor->rfc = trim($request->rfc);
        $modInstructor->tipo_identificacion = trim($request->tipo_identificacion);
        $modInstructor->folio_ine = trim($request->folio_ine);
        $modInstructor->expiracion_identificacion = trim($request->expiracion_identificacion);
        $modInstructor->sexo = trim($request->sexo);
        $modInstructor->estado_civil = trim($request->estado_civil);
        $modInstructor->fecha_nacimiento = $request->fecha_nacimientoins;
        $modInstructor->entidad = $estado->nombre;
        $modInstructor->municipio = $munic->muni;
        $modInstructor->asentamiento = trim($request->asentamiento);
        $modInstructor->telefono = trim($request->telefono);
        $modInstructor->correo = trim($request->correo);
        $modInstructor->tipo_honorario = trim($request->honorario);
        $modInstructor->clave_unidad = trim($request->unidad_registra);
        $modInstructor->extracurricular = trim($request->extracurricular);
        $modInstructor->stps = trim($request->stps);
        $modInstructor->conocer = trim($request->conocer);
        $modInstructor->clave_loc = $request->localidad;
        $modInstructor->localidad = $locali;
        if($request->estado != NULL)
        {
            $modInstructor->estado = TRUE;
        }
        else
        {
            $modInstructor->estado = FALSE;
        }

        $uni = substr($request->unidad_registra, -3, 2) * 1 . substr($request->unidad_registra, -1);
        $nuco = substr($modInstructor->numero_control, -12);
        $numero_control = $uni.$nuco;
        $modInstructor->numero_control = trim($numero_control);

        $modInstructor->banco = $request->banco;
        $modInstructor->interbancaria = $request->clabe;
        $modInstructor->no_cuenta = $request->numero_cuenta;
        $modInstructor->domicilio = $request->domicilio;
        $modInstructor->lastUserId = $userId;


        if ($request->file('arch_ine') != null)
        {
            $ine = $request->file('arch_ine'); # obtenemos el archivo
            $urline = $this->pdf_upload($ine, $request->id, 'ine'); # invocamos el método
            $modInstructor->archivo_ine = $urline; # guardamos el path
        }

        if ($request->file('arch_domicilio') != null)
        {
        $dom = $request->file('arch_domicilio'); # obtenemos el archivo
        $urldom = $this->pdf_upload($dom, $request->id, 'dom'); # invocamos el método
        $modInstructor->archivo_domicilio = $urldom; # guardamos el path
        }

        if ($request->file('arch_curp') != null)
        {
        $curp = $request->file('arch_curp'); # obtenemos el archivo
        $urlcurp = $this->pdf_upload($curp, $request->id, 'curp'); # invocamos el método
        $modInstructor->archivo_curp = $urlcurp; # guardamos el path
        }

        if ($request->file('arch_alta') != null)
        {
        $alta = $request->file('arch_alta'); # obtenemos el archivo
        $urlalta = $this->pdf_upload($alta, $request->id, 'alta'); # invocamos el método
        $modInstructor->archivo_alta = $urlalta; # guardamos el path
        }

        if ($request->file('arch_banco') != null)
        {
        $banco = $request->file('arch_banco'); # obtenemos el archivo
        $urlbanco = $this->pdf_upload($banco, $request->id, 'banco'); # invocamos el método
        $modInstructor->archivo_bancario = $urlbanco; # guardamos el path
        }

        if ($request->file('arch_rfc') != null)
        {
            $rfc = $request->file('arch_rfc'); # obtenemos el archivo
            $urlrfc = $this->pdf_upload($rfc, $request->id, 'rfc'); # invocamos el método
            $modInstructor->archivo_rfc = $urlrfc; # guardamos el path
        }

        if ($request->file('arch_foto') != null)
        {
        $foto = $request->file('arch_foto'); # obtenemos el archivo
        $urlfoto = $this->jpg_upload($foto, $request->id, 'foto'); # invocamos el método
        $modInstructor->archivo_fotografia = $urlfoto; # guardamos el path
        }

        if ($request->file('arch_estudio') != null)
        {
        $estudio = $request->file('arch_estudio'); # obtenemos el archivo
        $urlestudio = $this->pdf_upload($estudio, $request->id, 'estudios'); # invocamos el método
        $modInstructor->archivo_estudios = $urlestudio; # guardamos el path
        }

        if ($request->file('arch_id') != null)
        {
        $otraid = $request->file('arch_id'); # obtenemos el archivo
        $urlotraid = $this->pdf_upload($otraid, $request->id, 'oid'); # invocamos el método
        $modInstructor->archivo_otraid = $urlotraid; # guardamos el path
        }

        $modInstructor->save();

        Inscripcion::where('instructor', '=', $old)->update(['instructor' => $new]);
        //Calificacion::where('instructor', '=', $old)->update(['instructor' => $new]);
        tbl_curso::where('nombre', '=', $old)->update(['nombre' => $new]);
        tbl_curso::where('id_instructor', '=', $request->id)->update(['curp' => $request->curp]);


        return redirect()->route('instructor-inicio')
                ->with('success','Instructor Modificado');
    }

    public function edit_especval($id,$idins)
    {
        $especvalid = especialidad_instructor::WHERE('id', '=', $id)->FIRST();
        $data_espec = InstructorPerfil::WHERE('numero_control', '=', $idins)->GET();
        $data_pago = criterio_pago::ALL();
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

        $listacursos= DB::table('especialidad_instructor_curso')->SELECT('especialidad_instructor_curso.activo',
                'cursos.id','cursos.id_especialidad','cursos.nombre_curso','cursos.modalidad','cursos.objetivo',
                'cursos.costo','cursos.duracion','cursos.objetivo','cursos.tipo_curso','cursos.id_especialidad',
                'cursos.rango_criterio_pago_minimo','cursos.rango_criterio_pago_maximo')
                ->JOIN('cursos','cursos.id','=','especialidad_instructor_curso.curso_id')
                ->WHERE('id_especialidad_instructor', '=', $id)
                ->WHERE('id_especialidad','=',$especvalid->especialidad_id)
                ->WHERE('cursos.estado', '=', TRUE)
                ->orderby('cursos.nombre_curso','asc')
                ->GET();
                //   dd($listacursos);
        if(count($listacursos) != $count->count)
        {
            if(count($listacursos) == '0')
            {
                //  dd($listacursos);
                 $listacursos = DB::table('cursos')->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                        ->orderby('nombre_curso', 'asc')
                        ->GET();

                foreach ($listacursos as $cadwell)
                {
                    DB::table('especialidad_instructor_curso')
                        ->insert(['id_especialidad_instructor' => $id,
                                'curso_id' => $cadwell->id,
                                'activo' => FALSE]);

                }

                $listacursos= DB::table('especialidad_instructor_curso')->SELECT('especialidad_instructor_curso.activo',
                'cursos.id','cursos.id_especialidad','cursos.nombre_curso','cursos.modalidad','cursos.objetivo','cursos.costo',
                'cursos.duracion','cursos.objetivo','cursos.tipo_curso','cursos.id_especialidad',
                'cursos.rango_criterio_pago_minimo','cursos.rango_criterio_pago_maximo')
                ->JOIN('cursos','cursos.id','=','especialidad_instructor_curso.curso_id')
                ->WHERE('id_especialidad_instructor', '=', $id)
                ->WHERE('id_especialidad','=',$especvalid->especialidad_id)
                ->WHERE('cursos.estado', '=', TRUE)
                ->orderby('cursos.nombre_curso','asc')
                ->GET();
            }
            else
            {
                $cursoupd = DB::table('cursos')->WHERE('id_especialidad', '=', $especvalid->especialidad_id)
                        ->orderby('nombre_curso', 'asc')
                        ->GET();
                $lstarr = count($listacursos) -1;
                // dd('ea');
                foreach ($cursoupd as $upd)
                {
                    foreach ($listacursos as $key => $cadwell)
                    {
                        if($cadwell->id == $upd->id)
                        {
                            // printf($cadwell->id . ' - ' . $upd->id);
                            // printf(' /// ');
                            break;
                        }
                        else if($lstarr == $key)
                        {
                            DB::table('especialidad_instructor_curso')
                                ->insert(['id_especialidad_instructor' => $id,
                                        'curso_id' => $upd->id,
                                        'activo' => FALSE]);
                            break;
                        }
                    }
                }//A
                $listacursos= DB::table('especialidad_instructor_curso')->SELECT('especialidad_instructor_curso.activo',
                'cursos.id','cursos.id_especialidad','cursos.nombre_curso','cursos.modalidad','cursos.objetivo','cursos.costo',
                'cursos.duracion','cursos.objetivo','cursos.tipo_curso','cursos.id_especialidad',
                'cursos.rango_criterio_pago_minimo','cursos.rango_criterio_pago_maximo')
                ->JOIN('cursos','cursos.id','=','especialidad_instructor_curso.curso_id')
                ->WHERE('id_especialidad_instructor', '=', $id)
                ->WHERE('id_especialidad','=',$especvalid->especialidad_id)
                ->WHERE('cursos.estado', '=', TRUE)
                ->orderby('cursos.nombre_curso','asc')
                ->GET();
            }
        }
        // dd($listacursos);
        return view('layouts.pages.frmmodespecialidad', compact('especvalid','data_espec','data_pago','data_unidad', 'id','idins','nomesp', 'catcursos','listacursos'));
    }

    public function especval_mod_save(Request $request)
    {
            // dd($request);
        set_time_limit(0);
        $userId = Auth::user()->id;

        $espec_mod = especialidad_instructor::findOrFail($request->idespec);
        //$espec_mod->especialidad_id = $request->idesp;
        $espec_mod->perfilprof_id = $request->valido_perfil;
        $espec_mod->unidad_solicita = $request->unidad_validacion;
        $espec_mod->memorandum_validacion = $request->memorandum;
        $espec_mod->fecha_validacion = $request->fecha_validacion;
        $espec_mod->memorandum_modificacion = $request->memorandum_modificacion;
        $espec_mod->observacion = $request->observaciones;
        $espec_mod->criterio_pago_id = $request->criterio_pago_mod;
        if(isset($request->estado))
        {
            $espec_mod->activo = TRUE;
        }
        else
        {
            $espec_mod->activo = FALSE;
        }
        $espec_mod->lastUserId = $userId;
        $espec_mod->id_instructor = $request->idins;
        $espec_mod->save();
        $espe = $espec_mod->especialidad_id;
        // $idespval = $espec_mod->id;

        $listacursos = DB::table('especialidad_instructor_curso')
        ->WHERE('id_especialidad_instructor', '=', $request->idespec)->GET();
        if($listacursos == '[]')
        {
            $listacursos = DB::table('cursos')->WHERE('id_especialidad', '=', $espe)
                    ->orderby('nombre_curso', 'asc')
                    ->GET();
            // dd($listacursos);
            foreach ($listacursos as $cadwell)
            {
                // dd($request);
                // print('que pedo ' . $cadwell->id . ' **** ');
                foreach ($request->itemEdit as $key=>$new)
                {
                    // dd($new);
                    // print('ahi va '. $new['check_cursos']. ' -_-_- ');
                    if($cadwell->id == $new['check_cursos_edit'])
                    {
                        // print('true ' . $cadwell->nombre_curso . 'id= ' . $cadwell->id .' // ');
                        DB::table('especialidad_instructor_curso')
                            ->insert(['id_especialidad_instructor' => $request->idespec,
                                    'curso_id' => $cadwell->id,
                                    'activo' => TRUE]);
                        break;
                    }
                    else if(array_key_last($request->itemEdit) == $key)
                    {
                        // print('false ' . $cadwell->nombre_curso . 'id= ' . $cadwell->id .' // ');
                        DB::table('especialidad_instructor_curso')
                            ->insert(['id_especialidad_instructor' => $request->idespec,
                                    'curso_id' => $cadwell->id,
                                    'activo' => FALSE]);
                        break;
                    }
                }
            }
        }
        else
        {
            foreach ($listacursos as $cadwell)
            {
                foreach ($request->itemEdit as $new)
                {
                    //dd($new['check_cursos_edit']);
                    if($cadwell->curso_id == $new['check_cursos_edit'])
                    {
                        DB::table('especialidad_instructor_curso')->where('curso_id', '=', $cadwell->curso_id)
                            ->where('id_especialidad_instructor', '=', $request->idespec)
                            ->update(['activo' => TRUE,
                                    'id_especialidad_instructor'  => $request->idespec]);
                        break;
                    }
                    else
                    {
                        DB::table('especialidad_instructor_curso')->where('curso_id', '=', $cadwell->curso_id)
                            ->where('id_especialidad_instructor', '=', $request->idespec)
                            ->update(['activo' => FALSE,
                                    'id_especialidad_instructor'  => $request->idespec]);
                    }
                }
            }
        }

        //dd($request->itemEdit);
        return back();
        return redirect()->route('instructor-ver', ['id' => $request->idins])
                        ->with('success','Especialidad Para Impartir Modificada');
    }

    public function edit_especval2($id, $idins, $idesp)
    {
        $especvalid = especialidad_instructor::WHERE('id', '=', $idesp)->FIRST();

        $data_espec = InstructorPerfil::all();

        $data_pago = criterio_pago::all();

        $data_unidad = tbl_unidades::all();
        // cursos totales
        $catcursos = curso::WHERE('id_especialidad', '=', $id)->GET(['id', 'nombre_curso', 'modalidad', 'objetivo', 'costo', 'duracion', 'objetivo', 'tipo_curso', 'id_especialidad', 'rango_criterio_pago_minimo', 'rango_criterio_pago_maximo']);

        $nomesp = especialidad::SELECT('nombre')->WHERE('id', '=', $id)->FIRST();

        return view('layouts.pages.frmmodespecialidad', compact('especvalid','data_espec','data_pago','data_unidad', 'idesp','id','idins','nomesp', 'catcursos'));
    }

    public function add_perfil($id)
    {
        $idins = $id;
        return view('layouts.pages.frmperfilprof', compact('idins'));
    }

    public function mod_perfil($id, $idins)
    {
        $perfil_ins = InstructorPerfil::WHERE('id', '=', $id)->FIRST();

        $sel_status = Status::WHERE('estatus', '=', $perfil_ins->estatus)->FIRST();
        $data_status = Status::WHERE('estatus', '!=', $perfil_ins->estatus)
                              ->WHERE('perfil_profesional', '=', 'true')->GET();

        return view('layouts.pages.modperfilprof', compact('idins','perfil_ins','sel_status','data_status','id'));
    }

    public function modperfilinstructor_save(Request $request)
    {
        $userId = Auth::user()->id;

        $perfilInstructor = InstructorPerfil::find($request->id);
        #proceso de guardado
        $perfilInstructor->grado_profesional = trim($request->grado_prof); //
        $perfilInstructor->area_carrera = trim($request->area_carrera); //
        $perfilInstructor->estatus = trim($request->estatus); //
        $perfilInstructor->pais_institucion = trim($request->institucion_pais); //
        $perfilInstructor->entidad_institucion = trim($request->institucion_entidad); //
        $perfilInstructor->ciudad_institucion = trim($request->institucion_ciudad);
        $perfilInstructor->nombre_institucion = trim($request->institucion_nombre);
        $perfilInstructor->fecha_expedicion_documento = trim($request->fecha_documento); //
        $perfilInstructor->folio_documento = trim($request->folio_documento); //
        $perfilInstructor->cursos_recibidos = trim($request->cursos_recibidos);
        // $perfilInstructor->estandar_conocer = trim($request->conocer);
        // $perfilInstructor->registro_stps = trim($request->stps);
        $perfilInstructor->capacitador_icatech = trim($request->capacitador_icatech);
        $perfilInstructor->recibidos_icatech = trim($request->recibidos_icatech);
        $perfilInstructor->cursos_impartidos = trim($request->cursos_impartidos);
        $perfilInstructor->experiencia_laboral = trim($request->exp_lab);
        $perfilInstructor->experiencia_docente = trim($request->exp_doc);
        $perfilInstructor->numero_control = trim($request->idInstructor);
        $perfilInstructor->lastUserId = $userId;
        $perfilInstructor->save(); // guardar registro

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional modificado');

    }

    public function perfilinstructor_save(Request $request)
    {
        $userId = Auth::user()->id;

        $perfilInstructor = new InstructorPerfil();
        #proceso de guardado
        $perfilInstructor->grado_profesional = trim($request->grado_prof); //
        $perfilInstructor->area_carrera = trim($request->area_carrera); //
        $perfilInstructor->estatus = trim($request->estatus); //
        $perfilInstructor->pais_institucion = trim($request->institucion_pais); //
        $perfilInstructor->entidad_institucion = trim($request->institucion_entidad); //
        $perfilInstructor->ciudad_institucion = trim($request->institucion_ciudad);
        $perfilInstructor->nombre_institucion = trim($request->institucion_nombre);
        $perfilInstructor->fecha_expedicion_documento = trim($request->fecha_documento); //
        $perfilInstructor->folio_documento = trim($request->folio_documento); //
        $perfilInstructor->cursos_recibidos = trim($request->cursos_recibidos);
        // $perfilInstructor->estandar_conocer = trim($request->conocer);
        // $perfilInstructor->registro_stps = trim($request->stps);
        $perfilInstructor->capacitador_icatech = trim($request->capacitador_icatech);
        $perfilInstructor->recibidos_icatech = trim($request->recibidos_icatech);
        $perfilInstructor->cursos_impartidos = trim($request->cursos_impartidos);
        $perfilInstructor->experiencia_laboral = trim($request->exp_lab);
        $perfilInstructor->experiencia_docente = trim($request->exp_doc);
        $perfilInstructor->numero_control = trim($request->idInstructor);
        $perfilInstructor->lastUserId = $userId;
        $perfilInstructor->save(); // guardar registro

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional agregado');

    }

    public function add_cursoimpartir($id)
    {
        $idins = $id;
        $data_especialidad = especialidad::where('id', '!=', '0')->orderBy('nombre','asc')->paginate(20);
        return view('layouts.pages.frmcursoimpartir', compact('data_especialidad','idins'));
    }

    public function cursoimpartir_form($id, $idins)
    {
        $chckespecialidad = DB::TABLE('especialidad_instructores')
                            ->WHERE('id_instructor', '=', $idins)
                            ->WHERE('especialidad_id', '=', $id)
                            ->FIRST();
        if($chckespecialidad == NULL)
        {
            $perfil = InstructorPerfil::WHERE('numero_control', '=', $idins)->GET(['id','grado_profesional','area_carrera']);
            $pago = criterio_pago::SELECT('id','perfil_profesional')->WHERE('id', '!=', '0')->GET();
            $data = tbl_unidades::SELECT('unidad','cct')->WHERE('id','!=','0')->GET();
            $cursos = curso::WHERE('id_especialidad', '=', $id)->WHERE('estado', '=', TRUE)->ORDERBY('nombre_curso', 'ASC')->GET(['id', 'nombre_curso', 'modalidad', 'objetivo', 'costo', 'duracion', 'objetivo', 'tipo_curso', 'id_especialidad', 'rango_criterio_pago_minimo', 'rango_criterio_pago_maximo']);
            $nomesp = especialidad::SELECT('nombre')->WHERE('id', '=', $id)->FIRST();
            return view('layouts.pages.frmaddespecialidad', compact('id','idins','perfil','pago','data', 'cursos','nomesp'));
        }
        else
        {
            $esp = especialidad::SELECT('nombre')->WHERE('id', '=', $id)->FIRST();
            $mensaje = "Lo sentimos, la especialidad ".$esp->nombre." ya esta asociada a este instructor.";
            return redirect('instructor/add/curso-impartir/' . $idins)->withErrors($mensaje);
        }
    }

    public function espec_val_save(Request $request)
    {
        $userId = Auth::user()->id;
        //dd($request);
        $espec_save = new especialidad_instructor;
        $espec_save->especialidad_id = $request->idespec;
        $espec_save->perfilprof_id = $request->valido_perfil;
        $espec_save->unidad_solicita = $request->unidad_validacion;
        $espec_save->memorandum_validacion = $request->memorandum;
        $espec_save->fecha_validacion = $request->fecha_validacion;
        $espec_save->memorandum_modificacion = $request->memorandum_modificacion;
        $espec_save->observacion = $request->observaciones;
        $espec_save->criterio_pago_id = $request->criterio_pago_instructor;
        $espec_save->lastUserId = $userId;
        $espec_save->activo = TRUE;
        $espec_save->id_instructor = $request->idInstructor;
        $espec_save->save();
        // obtener el ultimo id que se ha registrado
        $especialidadInstrcutorId = $espec_save->id;
        // declarar un arreglo
        $pila = array();

        $listacursos = DB::table('cursos')->WHERE('id_especialidad', '=', $request->idespec)->GET();
        // eliminar registros previamente
        //$cursos_mod->cursos()->detach();

        foreach ($listacursos as $cadwell)
        {
            if($request->itemAdd != NULL)
            {
            // print('qhe ' . $cadwell->id . ' **** ');
                foreach ($request->itemAdd as $key=>$new)
                {
                    // print('ahi va '. $new['check_cursos']. ' -_-_- ');
                    if($cadwell->id == $new['check_cursos'])
                    {
                        // print('true ' . $cadwell->nombre_curso . 'id= ' . $cadwell->id .' // ');
                        DB::table('especialidad_instructor_curso')
                            ->insert(['id_especialidad_instructor' => $especialidadInstrcutorId,
                                    'curso_id' => $cadwell->id,
                                    'activo' => TRUE]);
                        break;
                    }
                    else if(array_key_last($request->itemAdd) == $key)
                    {
                        // print('false ' . $cadwell->nombre_curso . 'id= ' . $cadwell->id .' // ');
                        DB::table('especialidad_instructor_curso')
                            ->insert(['id_especialidad_instructor' => $especialidadInstrcutorId,
                                    'curso_id' => $cadwell->id,
                                    'activo' => FALSE]);
                        break;
                    }
                }
            }
            else
            {
                DB::table('especialidad_instructor_curso')
                            ->insert(['id_especialidad_instructor' => $especialidadInstrcutorId,
                                    'curso_id' => $cadwell->id,
                                    'activo' => FALSE]);
            }
        }
        //dd($especialidadInstrcutorId);
        /*foreach( (array) $request->itemAdd as $key => $value)
        {
            if(isset($value['check_cursos']))
            {
                $arreglos = [
                    'curso_id' => $value['check_cursos'],
                    'activo' => TRUE
                ];
                array_push($pila, $arreglos);
            }
        }
         hacemos la llamada al módelo
        $instructorEspecialidad = new especialidad_instructor();
        $especialidadesInstructoresCurso = $instructorEspecialidad->findOrFail($especialidadInstrcutorId);

        $especialidadesInstructoresCurso->cursos()->attach($pila);

         limpiar array
        unset($pila);*/

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Especialidad Para Impartir Agregada');
    }

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
                DB::raw("CASE WHEN instructores.estado = true THEN 'ACTIVO' ELSE 'INACTIVO' END"),
                DB::raw("array(select observacion from especialidad_instructores
                LEFT JOIN instructor_perfil on instructor_perfil.numero_control = instructores.id
                where especialidad_instructores.perfilprof_id = instructor_perfil.id) as obs"))
                ->WHERE('instructores.estado', '=', TRUE)
                // ->whereRaw("array(select especialidades.nombre from especialidad_instructores
                // LEFT JOIN especialidades on especialidades.id = especialidad_instructores.especialidad_id
                // LEFT JOIN instructor_perfil ip on ip.numero_control = instructores.id
                // where especialidad_instructores.perfilprof_id = ip.id) != '{}'")
                ->LEFTJOIN('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
                ->ORDERBY('apellidoPaterno', 'ASC')
                ->GET();

        $cabecera = ['ID','UNIDAD DE CAPACITACION/ACCION MOVIL','APELLIDO PATERNO','APELLIDO MATERNO','NOMBRE','CURP','RFC','NUMERO COTROL','ESPECIALIDAD','FECHA DE VALIDACION','CLAVE','CRITERIO PAGO',
                    'GRADO PROFESIONAL QUE CUBRE PARA LA ESPECIALIDAD','PERFIL PROFESIONAL CON EL QUE SE VALIDO',
                    'FORMACION PROFESIONAL CON EL QUE SE VALIDO','INSTITUCION','SEXO','ESTADO_CIVIL',
                    'ASENTAMIENTO','DOMICILIO','TELEFONO','CORREO','MEMORANDUM DE VALIDACION','ACTIVO/INACTIVO',
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
}

