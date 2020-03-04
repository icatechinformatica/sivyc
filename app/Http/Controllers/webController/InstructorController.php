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
use Illuminate\Support\Str;
use Redirect,Response;
use App\Models\InstructorPerfil;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
        return view('layouts.pages.frminstructor');
    }

    #----- instructor/guardar -----#
    public function guardar_instructor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cv' => 'required|mimes:pdf|max:2048'
        ]);
        if ($validator->fails()) {
            # code...
            $error =  $validator->errors()->first();
            dd($error);
        } else {
            $saveInstructor = new instructor();
            $file = $request->file('cv'); # obtenemos el archivo
            $urlcv = $this->pdf_upload($file);
            $nco = '404Prueba'; #No. Control prueba
            $nombre_completo = $request->nombre. ' ' . $request->apellido_paterno. ' ' . $request->apellido_materno;

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
            $saveInstructor->fecha_nacimiento = trim($request->fecha_nacimiento);
            $saveInstructor->entidad = trim($request->entidad);
            $saveInstructor->municipio = trim($request->municipio);
            $saveInstructor->asentamiento = trim($request->asentamiento);
            $saveInstructor->domicilio = trim($request->domicilio);
            $saveInstructor->telefono = trim($request->telefono);
            $saveInstructor->correo = trim($request->correo);
            $saveInstructor->banco = trim($request->banco);
            $saveInstructor->interbancaria = trim($request->clabe);
            $saveInstructor->no_cuenta = trim($request->numero_cuenta);

            #----- Academico -----
            $saveInstructor->experiencia_laboral = trim($request->exp_laboral);
            $saveInstructor->experiencia_docente = trim($request->exp_docente);
            $saveInstructor->cursos_recibidos = trim($request->cursos_recibidos);
            $saveInstructor->cursos_conocer = trim($request->cursos_conocer);
            $saveInstructor->cursos_impartidos = trim($request->cursos_impartidos);
            $saveInstructor->capacitados_icatech = trim($request->cap_icatech);
            $saveInstructor->curso_recibido_icatech =trim($request->cursos_recicatech);
            $saveInstructor->archivo_cv = trim($urlcv);

            #----- Institucional -----
            $saveInstructor->numero_control = $nco;
            $saveInstructor->tipo_honorario = trim($request->tipo_honorario);
            $saveInstructor->registro_agente_capacitador_externo = trim($request->registro_agente);
            $saveInstructor->unidad_capacitacion_solicita_validacion_instructor = trim($request->uncap_validacion);
            $saveInstructor->memoramdum_validacion = trim($request->memo_validacion);
            $saveInstructor->modificacion_memo = trim($request->memo_mod);
            $saveInstructor->fecha_validacion = trim($request->fecha_validacion);
            $saveInstructor->observaciones = trim($request->observacion);
            $saveInstructor->save();

            return redirect()->route('instructor/inicio')
                        ->with('success','Perfil profesional agregado');
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function ver_instructor($id)
    {
        $instructor = new instructor();
        $instructor_perfil = new InstructorPerfil();
        $curso_validado = new cursoValidado();
        $det_curso = new Curso();
        // consulta para mostrar los datos de determinado
        $getinstructor = $instructor->findOrFail($id);
        $perfil = $instructor_perfil->WHERE('numero_control', '=', $id)->GET();
        $cursvali = $curso_validado->SELECT('curso_validado.clave_curso AS clavecurso', 'cursos.nombre_curso AS nombre', 'cursos.id AS id_c')
                    ->WHERE('curso_validado.numero_control', '=', $id)
                    ->LEFTJOIN('cursos', 'cursos.id', '=', 'curso_validado.id_curso')
                    ->GET();
        //$curso = $det_curso->WHERE('id','=', $cursvali->id_curso)->GET;

        return view('layouts.pages.verinstructor', compact('perfil','getinstructor','cursvali'));
    }
    public function add_perfil($id)
    {
        $idInstructor = $id;
        return view('layouts.pages.frmperfilprof', compact('idInstructor'));
    }

    public function perfilinstructor_save(Request $request)
    {
        $perfilInstructor = new InstructorPerfil();
        #proceso de guardado
        $perfilInstructor->area_carrera = trim($request->area_carrera); //
        $perfilInstructor->especialidad = trim($request->especialidad); //
        $perfilInstructor->clave_especialidad = trim($request->clave_especialidad); //
        $perfilInstructor->nivel_estudios_cubre_especialidad = trim($request->grado_estudio); //
        $perfilInstructor->perfil_profesional = trim($request->perfil_profesional); //
        $perfilInstructor->carrera = trim($request->nombre_carrera); //
        $perfilInstructor->estatus = trim($request->estatus); //
        $perfilInstructor->pais_institucion = trim($request->institucion_pais); //
        $perfilInstructor->entidad_institucion = trim($request->institucion_entidad); //
        $perfilInstructor->fecha_expedicion_documento = trim($request->fecha_documento); //
        $perfilInstructor->folio_documento = trim($request->folio_documento); //
        $perfilInstructor->numero_control = trim($request->idInstructor); //
        $perfilInstructor->save(); // guardar registro

        return redirect()->route('instructor-inicio', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional agregado');

    }

    public function add_cursoimpartir($id)
    {
        $curso = new Curso();
        $idInstructor = $id;
        $data_curso = $curso::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.frmcursoimpartir', compact('data_curso','idInstructor'));
    }

    public function cursoimpartir_save(Request $request)
    {
        $curso_validado = new cursoValidado();

        $curso_validado->id_curso = $request->id;
        $curso_validado->numero_control = $request->idInstructor;
        $curso_validado->clave_curso = "null";
        $curso_validado->save();

        return redirect()->route('instructor-ver', ['id' => $request->idInstructor])
                        ->with('success','Perfil profesional agregado');

        #Proceso de Guardado
        #$curso_validado->clave_curso = trim($request->)
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */

    protected function pdf_upload($pdf)
    {
                    $tamanio = $pdf->getClientSize(); #obtener el tamaño del archivo del cliente
                    $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
                    $pdfFile = trim( Str::slug($pdf->getClientOriginalName(), '-')) . "." . $extensionPdf; // nombre de la imagen al momento de subirla
                    $pdf->storeAs('/uploadFiles/', $pdfFile); // guardamos el archivo en la carpeta storage
                    $pdfUrl = Storage::url('/uploadFiles/'.$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
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

