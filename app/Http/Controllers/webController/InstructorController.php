<?php

namespace App\Http\Controllers\webController;

use App\Models\instructor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Redirect,Response;
use App\Models\InstructorPerfil;
class InstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return view('layouts.pages.initinstructor');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function crear_instructor()
    {
        return view('layouts.pages.frminstructor');
    }

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
            $nco = '65D'; #No. Control prueba
            $nombre_completo = $request->nombre. ' ' . $request->apellido_paterno. ' ' . $request->apellido_materno;

            # Proceso de Guardado
            #----- Personal -----
            $saveInstructor->nombre = trim($nombre_completo);
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
            $saveInstructor->capacitados_icatech = trim($request->capacitado_icatech);
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
            $saveInstructor->observaciones = trim($request->obervacion);
            $saveInstructor->save();

            $paso = 'paso!';
            $path = $request;
            dd($paso);
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
        $instructor_perfil = new InstructorPerfil();
        $perfil = $instructor_perfil->WHERE('numero_control', '=', $id)->GET();
        $data = [
            'perfil' => $perfil,
        ];
        return view('layouts.pages.verinstructor')->with($data);
    }
    public function add_perfil()
    {
        return view('layouts.pages.frmperfilprof');
    }

    public function add_cursoimpartir()
    {
        return view('layouts.pages.frmcursoimpartir');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }

    protected function pdf_upload($pdf)
    {
                    $tamanio = $pdf->getClientSize(); #obtener el tamaño del archivo del cliente
                    $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
                    $pdfFile = trim( Str::slug($pdf->getClientOriginalName(), '-')) . "." . $extensionPdf; // nombre de la imagen al momento de subirla
                    $pdf->storeAs('/uploadFiles/', $pdfFile); // guardamos el archivo en la carpeta storage
                    $pdfUrl = Storage::url('/uploadFiles/'.$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
                    return $pdfUrl;
    }
}

