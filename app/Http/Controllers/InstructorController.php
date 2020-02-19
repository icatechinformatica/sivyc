<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers;

use App\Models\instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Redirect,Response;

class InstructorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
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
            'nombre' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'curp' => 'required',
            'rfc' => 'required',
            'fecha_nacimiento' => 'required',
            'lugar_nacimiento' => 'required',
            'lugar_residencia' => 'required',
            'domicilio' => 'required',
            'telefono' => 'required',
            'correo' => 'required',
            'clabe' => 'required',
            'banco' => 'required',
            'numero_cuenta' => 'required',
            'grado_estudio' => 'required',
            'perfil_profesional' => 'required',
            'area_carrera' => 'required',
            'licenciatura' => 'required',
            'estatus' => 'required',
            'institucion_pais' => 'required',
            'institucion_entidad' => 'required',
            'institucion_nombre' => 'required',
            'fecha_documento' => 'required',
            'folio_documento' => 'required',
            'cv' => 'required|mimes:pdf|max:2048',
            'numero_control' => 'required',
            'honorario' => 'required',
            'registro_agente' => 'required|mimes:pdf|max:2048',
            'uncap_validacion' => 'required',
            'memo_validacion' => 'required|mimes:pdf|max:2048',
        ]);
        if ($validator->fails()) {
            # code...
            $error =  $validator->errors()->first();
            dd($error);
        } else {
            $saveComunicado = new instructor();
            $file = $request->file('cv'); # obtenemos el archivo
            $urlcv = $this->pdf_upload($file);
           /* $file = $request->file('registro_agente');
            $urlRegistroAgente = $this->pdf_upload($file);
            $file = $request->file('memo_validacion');*/

            $saveComunicado->nombre = trim($request->nombre);
            $saveComunicado->apellido_paterno = trim($request->apellido_paterno);
            $saveComunicado->apellido_materno = trim($request->apellido_materno);
            $saveComunicado->curp = trim($request->curp);
            $saveComunicado->rfc = trim($request->rfc);
            $saveComunicado->cv = trim($urlcv);
            $saveComunicado->save();

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
    public function show()
    {
       // return view('show',compact('instructor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\instructor  $instructor
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        //
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
