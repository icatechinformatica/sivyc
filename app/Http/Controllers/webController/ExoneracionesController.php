<?php

namespace App\Http\Controllers\webController;
use App\Http\Controllers\Controller;
use App\Models\api\Municipio;
use App\Models\Exoneraciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExoneracionesController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->busqueda_exoneracionpor != 'TODAS LAS UNIDADES') {
            $exoneraciones = Exoneraciones::Busqueda($request->get('busqueda'), $request->get('busqueda_exoneracionpor'))
                ->leftjoin('tbl_unidades', 'exoneraciones.id_unidad_capacitacion', '=', 'tbl_unidades.id')
                ->select('exoneraciones.*', 'tbl_unidades.unidad as unidad_capacitacion')
                ->orderByDesc('exoneraciones.id')
                ->paginate(15, ['exoneraciones.*', 'tbl_unidades.unidad as unidad_capacitacion']);
        } else {
            $exoneraciones = Exoneraciones::Busqueda($request->get('busqueda'), '')
                ->leftjoin('tbl_unidades', 'exoneraciones.id_unidad_capacitacion', '=', 'tbl_unidades.id')
                ->select('exoneraciones.*', 'tbl_unidades.unidad as unidad_capacitacion')
                ->orderByDesc('exoneraciones.id')
                ->paginate(15, ['exoneraciones.*', 'tbl_unidades.unidad as unidad_capacitacion']);
        }
        
        return view('layouts.pages.inicio_exoneraciones', compact('exoneraciones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $unidades = \DB::table('tbl_unidades')->orderBy('tbl_unidades.id')->get();
        $estados = \DB::table('estados')->orderBy('estados.id')->get();
        return view('layouts.pages.frmadd_exoneraciones', compact('unidades', 'estados'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $exoneracion = new Exoneraciones();

        $exoneracion->id_unidad_capacitacion = $request->unidad;
        $exoneracion->no_memorandum = $request->no_memorandum;
        $exoneracion->id_estado = $request->estadoE;
        $exoneracion->id_municipio = $request->municipioE;
        $exoneracion->localidad = $request->localidad;
        $exoneracion->fecha_memorandum = $request->fecha_memorandum;
        $exoneracion->tipo_exoneracion = $request->tipo_exoneracion;
        $exoneracion->porcentaje = $request->porcentaje;
        $exoneracion->razon_exoneracion = $request->razon_exoneracion;
        $exoneracion->observaciones = $request->observaciones;
        $exoneracion->no_convenio = $request->numero_convenio;
        $exoneracion->status = $request->status;
        $activo = 'false';
        if ($request->input('activo') != null) {
            $activo = 'true';
        }
        $exoneracion->activo = $activo;
        $exoneracion->iduser_created = Auth::user()->id;
        $exoneracion->save();

        $exoneracionId = $exoneracion->id;
        if ($request->hasFile('memo_soporte')) {
            $doc_exoneracion = DB::table('exoneraciones')->WHERE('id', $exoneracionId)->VALUE('memo_soporte_dependencia');
            if (!is_null($doc_exoneracion)) {
                if (!empty($doc_exoneracion)) {
                    $docExo = explode("/", $doc_exoneracion, 5);
                    if (Storage::exists($docExo[4])) {
                        Storage::delete($docExo[4]);
                    }
                }
            }
            $doc_exoneracion = $request->file('memo_soporte');
            $url_doc_exoneracion = $this->uploaded_file($doc_exoneracion, $exoneracionId, 'memo_soporte');
            $arregloExoneracion = [
                'memo_soporte_dependencia' => $url_doc_exoneracion
            ];
            \DB::table('exoneraciones')->WHERE('id', $exoneracionId)->update($arregloExoneracion);
            unset($arregloExoneracion);
        }

        return redirect()->route('exoneraciones.inicio')->with('success', 'Exoneracion agregada exitosamente');
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
    public function edit($id) {
        $idExo = base64_decode($id);
        $exoneracion = Exoneraciones::findOrfail($idExo);
        $unidades = \DB::table('tbl_unidades')->orderBy('tbl_unidades.id')->get();
        $estados = \DB::table('estados')->orderBy('estados.id')->get();
        $municipios = \DB::table('tbl_municipios')->where('tbl_municipios.id_estado', '=', $exoneracion->id_estado)->get();
        return view('layouts.pages.edit_exoneraciones', compact('exoneracion', 'unidades', 'estados', 'municipios'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $idExo = base64_decode($id);
        $exoneracion = Exoneraciones::find($idExo);

        $exoneracion->id_unidad_capacitacion = $request->unidad;
        $exoneracion->no_memorandum = $request->no_memorandum;
        $exoneracion->id_estado = $request->estadoEx;
        $exoneracion->id_municipio = $request->municipioEx;
        $exoneracion->localidad = $request->localidad;
        $exoneracion->fecha_memorandum = $request->fecha_memorandum;
        $exoneracion->tipo_exoneracion = $request->tipo_exoneracion;
        $exoneracion->porcentaje = $request->porcentaje;
        $exoneracion->razon_exoneracion = $request->razon_exoneracion;
        $exoneracion->observaciones = $request->observaciones;
        $exoneracion->no_convenio = $request->numero_convenio;
        $exoneracion->status = $request->status;
        $activo = 'false';
        if ($request->input('activo') != null) {
            $activo = 'true';
        }
        $exoneracion->activo = $activo;
        $exoneracion->iduser_updated = Auth::user()->id;
        $exoneracion->save();

        $exoneracionId = $exoneracion->id;
        if ($request->hasFile('memo_soporte')) {
            $doc_exoneracion = DB::table('exoneraciones')->WHERE('id', $exoneracionId)->VALUE('memo_soporte_dependencia');
            if (!is_null($doc_exoneracion)) {
                if (!empty($doc_exoneracion)) {
                    $docExo = explode("/", $doc_exoneracion, 5);
                    if (Storage::exists($docExo[4])) {
                        Storage::delete($docExo[4]);
                    }
                }
            }
            $doc_exoneracion = $request->file('memo_soporte');
            $url_doc_exoneracion = $this->uploaded_file($doc_exoneracion, $exoneracionId, 'memo_soporte');
            $arregloExoneracion = [
                'memo_soporte_dependencia' => $url_doc_exoneracion
            ];
            \DB::table('exoneraciones')->WHERE('id', $exoneracionId)->update($arregloExoneracion);
            unset($arregloExoneracion);
        }

        $no_memo = $request->no_memorandum;
        return redirect()->route('exoneraciones.inicio')->with('success', sprintf('EXONERACIÓN %s ACTUALIZADO EXITOSAMENTE', $no_memo));
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

    protected function uploaded_file($file, $id, $name) {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."".date('YmdHis')."".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/exoneraciones/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/exoneraciones/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function getmunicipios(Request $request) {
        if (isset($request->idEst)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEstado=$request->idEst;
            $municipio = new Municipio();
            $municipios = $municipio->WHERE('id_estado', '=', $idEstado)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($municipios);
        } else {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
        return $json;
    }
}
