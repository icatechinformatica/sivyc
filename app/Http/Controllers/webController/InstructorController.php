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
use App\Models\especialidad_instructor;
use App\Models\criterio_pago;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class InstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     #----- instructor/inicio -----#
    public function index()
    {
        $instructor = new instructor();
        $data = $instructor::where('id', '!=', '0')->latest()->get();
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
        $data = tbl_unidades::SELECT('unidad','cct')->WHERE('id','!=','0')->GET();
        return view('layouts.pages.frminstructor',compact('data'));
    }

    #----- instructor/guardar -----#
    public function guardar_instructor(Request $request)
    {
            $saveInstructor = new instructor();
            # Proceso de Guardado
            #----- Personal -----
            $saveInstructor->nombre = trim($request->nombre);
            $saveInstructor->apellidoPaterno = trim($request->apellido_paterno);
            $saveInstructor->apellidoMaterno = trim($request->apellido_materno);
            $saveInstructor->curp = trim($request->curp);
            $saveInstructor->rfc = trim($request->rfc);
            $saveInstructor->folio_ine = trim($request->folio_ine);
            $saveInstructor->sexo = trim($request->sexo);
            $saveInstructor->estado_civil = trim($request->estado_civil);
            $saveInstructor->fecha_nacimiento = $request->fecha_nacimiento;
            $saveInstructor->entidad = trim($request->entidad);
            $saveInstructor->municipio = trim($request->municipio);
            $saveInstructor->asentamiento = trim($request->asentamiento);
            $saveInstructor->telefono = trim($request->telefono);
            $saveInstructor->correo = trim($request->correo);
            $saveInstructor->tipo_honorario = trim($request->honorario);
            $saveInstructor->clave_unidad = trim($request->unidad_registra);
            $saveInstructor->status = "En Proceso";

            //Creacion de el numero de control
            $uni = substr($request->unidad_registra, -2);
            $now = Carbon::now();
            $year = substr($now->year, -2);
            $numero_control = $uni.$year.$request->rfc;
            $saveInstructor->numero_control = trim($numero_control);
            $saveInstructor->save();

            return redirect()->route('instructor-inicio')
                        ->with('success','Perfil profesional agregado');
    }

    /**
     * modificaciones
     */
    public function institucional($id)
    {
        return view('layouts.pages.frminstructor_institucional');
    }
    /**
     * @param Request
     */
    public function institucional_save(Request $request)
    {
        $instructor_institucional = new InstructorPerfil();

        $instructor_institucional->tipo_honorario = trim($request->tipo_honorario); //
        $instructor_institucional->registro_agente_capacitador_externo = trim($request->registro_agente); //
        $instructor_institucional->unidad_capacitacion_solicita_validacion = trim($request->uncap_validacion); //
        $instructor_institucional->memorandum_validacion = trim($request->memo_validacion); //
        $instructor_institucional->fecha_validacion = trim($request->fecha_validacion);
        $instructor_institucional->modificacion_memo = trim($request->memo_mod);
        $instructor_institucional->numero_control = trim($request->idInstructor);
        $instructor_institucional->save(); // guardar registro

        return redirect()->route('instructor-inicio')
            ->with('success','Agregado datos institucionales');
    }

    public function validar($id)
    {   $instructor = new instructor();
        $getinstructor = $instructor->findOrFail($id);
        return view('layouts.pages.validarinstructor', compact('getinstructor'));
    }

    public function rechazo_save(Request $request)
    {
        $saveInstructor = instructor::find($request->id);
        $saveInstructor->rechazo = $request->comentario_rechazo;
        $saveInstructor->status = "Rechazado";
        $saveInstructor->save();

        return redirect()->route('instructor-inicio')
            ->with('success','Instructor Rechazado');
    }

    public function validado_save(Request $request)
    {
        $instructor = instructor::find($request->id);
        $instructor->banco = $request->banco;
        $instructor->interbancaria = $request->clabe;
        $instructor->no_cuenta = $request->numero_cuenta;
        $instructor->domicilio = $request->domicilio;
        $instructor->status = "Aprobado";

        $ine = $request->file('arch_ine'); # obtenemos el archivo
        $urline = $this->pdf_upload($ine, $request->id, 'ine'); # invocamos el método
        $instructor->archivo_ine = $urline; # guardamos el path

        $dom = $request->file('arch_domicilio'); # obtenemos el archivo
        $urldom = $this->pdf_upload($dom, $request->id, 'dom'); # invocamos el método
        $instructor->archivo_domicilio = $urldom; # guardamos el path

        $curp = $request->file('arch_curp'); # obtenemos el archivo
        $urlcurp = $this->pdf_upload($curp, $request->id, 'curp'); # invocamos el método
        $instructor->archivo_curp = $urlcurp; # guardamos el path

        $alta = $request->file('arch_alta'); # obtenemos el archivo
        $urlalta = $this->pdf_upload($alta, $request->id, 'alta'); # invocamos el método
        $instructor->archivo_alta = $urlalta; # guardamos el path

        $banco = $request->file('arch_banco'); # obtenemos el archivo
        $urlbanco = $this->pdf_upload($banco, $request->id, 'banco'); # invocamos el método
        $instructor->archivo_bancario = $urlbanco; # guardamos el path

        $foto = $request->file('arch_foto'); # obtenemos el archivo
        $urlfoto = $this->pdf_upload($foto, $request->id, 'foto'); # invocamos el método
        $instructor->archivo_fotografia = $urlfoto; # guardamos el path

        $estudio = $request->file('arch_estudio'); # obtenemos el archivo
        $urlestudio = $this->pdf_upload($estudio, $request->id, 'estudios'); # invocamos el método
        $instructor->archivo_estudios = $urlestudio; # guardamos el path

        $otraid = $request->file('arch_id'); # obtenemos el archivo
        $urlotraid = $this->pdf_upload($otraid, $request->id, 'oid'); # invocamos el método
        $instructor->archivo_otraid = $urlotraid; # guardamos el path

        $instructor->save();

        return redirect()->route('instructor-inicio')
            ->with('success','Instructor Validado');
    }

    public function editar($id)
    {
        $instructor = new instructor();
        $datains = instructor::WHERE('id', '=', $id)->FIRST();

        $estado_civil = estado_civil::WHERE('nombre', '=', $datains->estado_civil)->FIRST();
        $lista_civil = estado_civil::WHERE('nombre', '!=', $datains->estado_civil)->GET();

        $unidad = tbl_unidades::WHERE('cct', '=', $datains->clave_unidad)->FIRST();
        $lista_unidad = tbl_unidades::WHERE('cct', '!=', $datains->clave_unidad)->GET();

        return view('layouts.pages.editarinstructor', compact('datains','estado_civil','lista_civil','unidad','lista_unidad'));
    }

    public function guardar_mod(Request $request)
    {
        $modInstructor = instructor::find($request->id);

        $modInstructor->nombre = trim($request->nombre);
        $modInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $modInstructor->apellidoMaterno = trim($request->apellido_materno);
        $modInstructor->curp = trim($request->curp);
        $modInstructor->rfc = trim($request->rfc);
        $modInstructor->folio_ine = trim($request->folio_ine);
        $modInstructor->sexo = trim($request->sexo);
        $modInstructor->estado_civil = trim($request->estado_civil);
        $modInstructor->fecha_nacimiento = $request->fecha_nacimiento;
        $modInstructor->entidad = trim($request->entidad);
        $modInstructor->municipio = trim($request->municipio);
        $modInstructor->asentamiento = trim($request->asentamiento);
        $modInstructor->telefono = trim($request->telefono);
        $modInstructor->correo = trim($request->correo);
        $modInstructor->tipo_honorario = trim($request->honorario);
        $modInstructor->clave_unidad = trim($request->unidad_registra);
        $modInstructor->status = "En Proceso";
        $modInstructor->rechazo = "";

        $uni = substr($request->unidad_registra, -2);
        $nuco = substr($modInstructor->numero_control, -15);
        $numero_control = $uni.$nuco;
        $modInstructor->numero_control = trim($numero_control);
        $modInstructor->save();

        return redirect()->route('instructor-inicio')
                    ->with('success','Instructor Modificado');
    }

    public function ver_instructor($id)
    {
        $instructor_perfil = new InstructorPerfil();
        $curso_validado = new cursoValidado();
        $det_curso = new Curso();
        $datains = instructor::WHERE('id', '=', $id)->FIRST();

        $estado_civil = estado_civil::WHERE('nombre', '=', $datains->estado_civil)->FIRST();
        $lista_civil = estado_civil::WHERE('nombre', '!=', $datains->estado_civil)->GET();

        $unidad = tbl_unidades::WHERE('cct', '=', $datains->clave_unidad)->FIRST();
        $lista_unidad = tbl_unidades::WHERE('cct', '!=', $datains->clave_unidad)->GET();

        $perfil = $instructor_perfil->WHERE('numero_control', '=', $id)->GET();

        $validado = InstructorPerfil::SELECT('especialidades.nombre','criterio_pago.perfil_profesional',
                        'especialidad_instructores.zona','especialidad_instructores.observacion')
                        ->WHERE('numero_control', '=', $id)
                        ->LEFTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id','=','instructor_perfil.id')
                        ->LEFTJOIN('especialidades','especialidades.id','=','especialidad_instructores.especialidad_id')
                        ->LEFTJOIN('criterio_pago','criterio_pago.id','=','especialidad_instructores.pago_id')
                        ->GET();

        return view('layouts.pages.verinstructor', compact('datains','estado_civil','lista_civil','unidad','lista_unidad','perfil','validado'));
    }

    public function save_ins(Request $request)
    {
        $modInstructor = instructor::find($request->id);

        $modInstructor->nombre = trim($request->nombre);
        $modInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $modInstructor->apellidoMaterno = trim($request->apellido_materno);
        $modInstructor->curp = trim($request->curp);
        $modInstructor->rfc = trim($request->rfc);
        $modInstructor->folio_ine = trim($request->folio_ine);
        $modInstructor->sexo = trim($request->sexo);
        $modInstructor->estado_civil = trim($request->estado_civil);
        $modInstructor->fecha_nacimiento = $request->fecha_nacimiento;
        $modInstructor->entidad = trim($request->entidad);
        $modInstructor->municipio = trim($request->municipio);
        $modInstructor->asentamiento = trim($request->asentamiento);
        $modInstructor->telefono = trim($request->telefono);
        $modInstructor->correo = trim($request->correo);
        $modInstructor->tipo_honorario = trim($request->honorario);
        $modInstructor->clave_unidad = trim($request->unidad_registra);

        $uni = substr($request->unidad_registra, -2);
        $nuco = substr($modInstructor->numero_control, -15);
        $numero_control = $uni.$nuco;
        $modInstructor->numero_control = trim($numero_control);

        $modInstructor->banco = $request->banco;
        $modInstructor->interbancaria = $request->clabe;
        $modInstructor->no_cuenta = $request->numero_cuenta;
        $modInstructor->domicilio = $request->domicilio;

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

        if ($request->file('arch_foto') != null)
        {
        $foto = $request->file('arch_foto'); # obtenemos el archivo
        $urlfoto = $this->pdf_upload($foto, $request->id, 'foto'); # invocamos el método
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


    public function add_perfil($id)
    {
        $idins = $id;
        return view('layouts.pages.frmperfilprof', compact('idins'));
    }

    public function perfilinstructor_save(Request $request)
    {
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
        $perfilInstructor->estandar_conocer = trim($request->conocer);
        $perfilInstructor->registro_stps = trim($request->stps);
        $perfilInstructor->capacitador_icatech = trim($request->capacitador_icatech);
        $perfilInstructor->cursos_recibidos = trim($request->cursos_recibidos);
        $perfilInstructor->cursos_impartidos = trim($request->cursos_impartidos);
        $perfilInstructor->experiencia_laboral = trim($request->exp_lab);
        $perfilInstructor->experiencia_docente = trim($request->exp_doc);
        $perfilInstructor->numero_control = trim($request->idInstructor);
        $perfilInstructor->save(); // guardar registro

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional agregado');

    }

    public function add_cursoimpartir($id)
    {
        $idins = $id;
        $data_especialidad = especialidad::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.frmcursoimpartir', compact('data_especialidad','idins'));
    }

    public function cursoimpartir_form($id, $idins)
    {
        $perfil = instructorPerfil::SELECT('id','grado_profesional')->WHERE('numero_control', '=', $idins)->GET();
        $pago = criterio_pago::SELECT('id','perfil_profesional')->WHERE('id', '!=', '0')->GET();
        $data = tbl_unidades::SELECT('unidad','cct')->WHERE('id','!=','0')->GET();
        return view('layouts.pages.frmaddespecialidad', compact('id','idins','perfil','pago','data'));
    }

    public function espec_val_save(Request $request)
    {
        $espec_save = new especialidad_instructor;
        $espec_save->especialidad_id = $request->idespec;
        $espec_save->perfilprof_id = $request->valido_perfil;
        $espec_save->pago_id = $request->criterio_pago;
        $espec_save->zona = $request->zona;
        $espec_save->validado_impartir = $request->impartir;
        $espec_save->unidad_solicita = $request->unidad_validacion;
        $espec_save->memorandum_validacion = $request->memorandum;
        $espec_save->fecha_validacion = $request->fecha_validacion;
        $espec_save->memorandum_modificacion = $request->memorandum_modificacion;
        $espec_save->observacion = $request->observaciones;
        $espec_save->save();

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Especialidad Para Impartir Agregada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/instructor/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/instructor/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    public function paginate($items, $perPage = 5, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }
}

