<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convenio;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Municipio;

class ConveniosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $convenios = new Convenio();
        $data = $convenios->all();
        return view('layouts.pages.vstconvenios', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // mostrar formulario de convenio
        return view('layouts.pages.frmconvenio');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // convenios guardarlo en el metodo store

        $validador = Validator::make($request->all(), [
            'no_convenio' => 'required',
            'institucion' => 'required',
            'telefono' => 'required',
            'sector' => 'required',
            'fecha_firma' => 'required',
            'fecha_termino' => 'required',
            'poblacion' => 'required',
            'municipio' => 'required',
            'nombre_titular' => 'required',
            'nombre_enlace' => 'required',
            'status' => 'required',
            'direccion' => 'required',
        ]);

        if ($validador->fails()) {
            return redirect('/convenios/crear')
                        ->withErrors($validador)
                        ->withInput();
        }

        $convenios = new Convenio;
        $convenios['no_convenio'] = trim($request->input('no_convenio'));
        $convenios['institucion'] = trim($request->input('institucion'));
        $convenios['tipo_sector'] = $request->input('sector');
        $convenios['telefono'] = trim($request->input('telefono'));
        $convenios['fecha_firma'] = $convenios->getMyDateFormat($request->input('fecha_firma'));
        $convenios['fecha_vigencia'] = $convenios->getMyDateFormat($request->input('fecha_termino'));
        $convenios['poblacion'] = trim($request->input('poblacion'));
        $convenios['municipio'] = trim($request->input('municipio'));
        $convenios['nombre_titular'] = trim($request->input('nombre_titular'));
        $convenios['nombre_enlace'] = trim($request->input('nombre_enlace'));
        $convenios['status'] = trim($request->input('status'));
        $convenios['direccion'] = trim($request->input('direccion'));

        $convenios->save();

        # ==================================
        # Aquí tenemos el id recién guardado
        # ==================================
        $convenioId = $convenios->id;

        if ($request->hasFile('archivo_convenio')) {
            #ANTES DE GUARDAR EL ARCHIVO SI ES QUE HAY UNO VALIDAMOS QUE CUMPLA CON EL REQUERIMIENTO

            $validator = Validator::make($request->all(), [
                'archivo_convenio' => 'max:2048|mimes:pdf',
            ]);

            if ($validator->fails()) {
                # code...
                return redirect('convenios/crear')
                        ->withErrors($validator);
            } else {
                # vamos a trabajar en el documento para guardarlo
                $archivoConvenio = $request->file('archivo_convenio');
                $extension_archivo = $archivoConvenio->getClientOriginalExtension(); # extension de archivo del cliente
                $fileSize = $archivoConvenio->getClientSize(); # tamaño del archivo
                # nuevo nombre del archivo
                $fileName = "convenio".date('YmdHis')."_".$convenioId.".".$extension_archivo;
                $request->file('archivo_convenio')->storeAs('/convenios/'.$convenioId, $fileName);
                $docUrl = Storage::url('/convenios/'.$convenioId."/".$fileName);
                // guardamos en la base de datos
                $convenioUpdate = Convenio::find($convenioId);
                $convenioUpdate->archivo_convenio = $docUrl;
                $convenioUpdate->save();
            }
        }

        return redirect('/convenios/indice')->with('success', 'Convenio Agreado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $convenios = Convenio::findOrfail($id);
        return view('layouts.pages.editconvenio',['convenios'=> $convenios]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $idConvenio = base64_decode($id);
        //
        if (isset($idConvenio)) {
            $convenios_update = new Convenio();
            # code...
            $array_update = [
                'no_convenio' => trim($request->no_convenio),
                'tipo_sector' => trim($request->tipo),
                'institucion' => trim($request->institucion),
                'fecha_firma' => trim($request->fecha_firma),
                'fecha_vigencia' => trim($request->fecha_termino),
                'poblacion' => trim($request->poblacion),
                'municipio' => trim($request->municipio),
                'nombre_titular' => trim($request->nombre_titular),
                'nombre_enlace' => trim($request->nombre_enlace),
                'direccion' => trim($request->direccion),
                'telefono' => trim($request->telefono),
                'status' => trim($request->status),
            ];

            $convenios_update->findOrfail($idConvenio)->update($array_update);

            // validamos si hay archivos
            if ($request->hasFile('archivo_convenio')) {
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getClientSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        $file->storeAs('/convenios/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/convenios/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
