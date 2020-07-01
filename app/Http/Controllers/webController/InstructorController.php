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
    public function index(Request $request)
    {
        $busquedaInstructor = $request->get('busquedaPorInstructor');

        $tipoInstructor = $request->get('tipo_busqueda_instructor');

        $data = instructor::searchinstructor($tipoInstructor, $busquedaInstructor)->where('id', '!=', '0')->PAGINATE(25, [
            'nombre', 'telefono', 'status', 'apellidoPaterno', 'apellidoMaterno', 'numero_control'
        ]);
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

        $verify = instructor::WHERE('curp','=', $request->curp)->FIRST();
        if(is_null($verify) == TRUE)
        {
            $uid = instructor::select('id')->WHERE('id', '!=', '0')->orderby('id','desc')->first();
            $saveInstructor = new instructor();
            $id = $uid->id + 1;
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

            if ($request->file('arch_foto') != null)
            {
                $foto = $request->file('arch_foto'); # obtenemos el archivo
                $urlfoto = $this->pdf_upload($foto, $id, 'foto'); # invocamos el método
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
            $mensaje = "Lo sentimos, la curp ".$request->curp." asociada a este registro ya se encuentra en la base de datos.";
            return redirect('/instructor/crear')->withErrors($mensaje);
        }
    }

    public function validar($id)
    {
        $instructor = new instructor();
        $getinstructor = $instructor->findOrFail($id);
        $data = tbl_unidades::SELECT('unidad','cct')->WHERE('id','!=','0')->GET();
        return view('layouts.pages.validarinstructor', compact('getinstructor','data'));
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

        $instructor->rfc = trim($request->rfc);
        $instructor->folio_ine = trim($request->folio_ine);
        $instructor->sexo = trim($request->sexo);
        $instructor->estado_civil = trim($request->estado_civil);
        $instructor->fecha_nacimiento = $request->fecha_nacimientoins;
        $instructor->entidad = trim($request->entidad);
        $instructor->municipio = trim($request->municipio);
        $instructor->asentamiento = trim($request->asentamiento);
        $instructor->telefono = trim($request->telefono);
        $instructor->correo = trim($request->correo);
        $instructor->tipo_honorario = trim($request->honorario);
        $instructor->clave_unidad = trim($request->unidad_registra);
        $instructor->status = "Validado";

        //Creacion de el numero de control
        $uni = substr($request->unidad_registra, -2);
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
        $modInstructor = instructor::find($request->id);

        $modInstructor->nombre = trim($request->nombre);
        $modInstructor->apellidoPaterno = trim($request->apellido_paterno);
        $modInstructor->apellidoMaterno = trim($request->apellido_materno);
        $modInstructor->banco = $request->banco;
        $modInstructor->interbancaria = $request->clabe;
        $modInstructor->no_cuenta = $request->numero_cuenta;
        $modInstructor->domicilio = $request->domicilio;
        $modInstructor->status = "En Proceso";

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

        $validado = $instructor_perfil->SELECT('especialidades.nombre','criterio_pago.perfil_profesional',
                        'especialidad_instructores.zona','especialidad_instructores.observacion', 'especialidad_instructores.id AS especialidadinsid')
                        ->WHERE('instructor_perfil.numero_control', '=', $id)
                        ->RIGHTJOIN('especialidad_instructores','especialidad_instructores.perfilprof_id','=','instructor_perfil.id')
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
        $modInstructor->fecha_nacimiento = $request->fecha_nacimientoins;
        $modInstructor->entidad = trim($request->entidad);
        $modInstructor->municipio = trim($request->municipio);
        $modInstructor->asentamiento = trim($request->asentamiento);
        $modInstructor->telefono = trim($request->telefono);
        $modInstructor->correo = trim($request->correo);
        $modInstructor->tipo_honorario = trim($request->honorario);
        $modInstructor->clave_unidad = trim($request->unidad_registra);

        $uni = substr($request->unidad_registra, -2);
        $nuco = substr($modInstructor->numero_control, -12);
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

    public function edit_especval($id,$idins)
    {
        $idesp = $id;
        $idins = $idins;
        $data_especialidad = especialidad::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.modcursoimpartir', compact('data_especialidad','idesp','idins'));
    }

    public function edit_especval2($id, $idins, $idesp)
    {
        $especvalid = especialidad_instructor::WHERE('id', '=', $idesp)->FIRST();

        $sel_espec = InstructorPerfil::WHERE('id', '=', $especvalid->perfilprof_id)->FIRST();
        $data_espec = InstructorPerfil::where('id', '!=', $especvalid->perfilprof_id)->get();

        $sel_pago = criterio_pago::WHERE('id', '=', $especvalid->pago_id)->FIRST();
        $data_pago = criterio_pago::WHERE('id', '!=', $especvalid->pago_id)->GET();

        $sel_unidad = tbl_unidades::WHERE('unidad', '=', $especvalid->unidad_solicita)->FIRST();
        $data_unidad = tbl_unidades::WHERE('unidad', '!=', $especvalid->unidad_solicita)->GET();

        return view('layouts.pages.frmmodespecialidad', compact('especvalid','sel_espec','data_espec','sel_pago','data_pago','sel_unidad','data_unidad', 'idesp','id','idins'));
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
        $perfilInstructor->estandar_conocer = trim($request->conocer);
        $perfilInstructor->registro_stps = trim($request->stps);
        $perfilInstructor->capacitador_icatech = trim($request->capacitador_icatech);
        $perfilInstructor->recibidos_icatech = trim($request->recibidos_icatech);
        $perfilInstructor->cursos_impartidos = trim($request->cursos_impartidos);
        $perfilInstructor->experiencia_laboral = trim($request->exp_lab);
        $perfilInstructor->experiencia_docente = trim($request->exp_doc);
        $perfilInstructor->numero_control = trim($request->idInstructor);
        $perfilInstructor->save(); // guardar registro

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional modificado');

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
        $perfilInstructor->recibidos_icatech = trim($request->recibidos_icatech);
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

    public function especval_mod_save(Request $request)
    {

        $espec_mod = especialidad_instructor::find($request->idespec);
        $espec_mod->especialidad_id = $request->idesp;
        $espec_mod->perfilprof_id = $request->valido_perfil;
        $espec_mod->pago_id = $request->criterio_pago;
        $espec_mod->zona = $request->zona;
        $espec_mod->validado_impartir = $request->impartir;
        $espec_mod->unidad_solicita = $request->unidad_validacion;
        $espec_mod->memorandum_validacion = $request->memorandum;
        $espec_mod->fecha_validacion = $request->fecha_validacion;
        $espec_mod->memorandum_modificacion = $request->memorandum_modificacion;
        $espec_mod->observacion = $request->observaciones;
        $espec_mod->save();

        return redirect()->route('instructor-ver', ['id' => $request->idins])
                        ->with('success','Especialidad Para Impartir Modificada');
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

