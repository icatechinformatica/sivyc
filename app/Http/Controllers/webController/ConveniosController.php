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
        $municipio = new Municipio();
        $municipios = $municipio->all();
        // mostrar formulario de convenio
        return view('layouts.pages.frmconvenio', compact('municipios'));
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
        $municipio = new Municipio();
        $municipios = $municipio->all();
        $convenios = Convenio::findOrfail($id);
        return view('layouts.pages.editconvenio',['convenios'=> $convenios, 'municipios' => $municipios]);
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
        //
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
}
