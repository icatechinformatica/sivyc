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
use App\Models\convenioAvailable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $municipios = DB::table('tbl_municipios')->get();
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
                $fileSize = $archivoConvenio->getSize(); # tamaño del archivo
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
        $id_convenio = base64_decode($id);
        $convenios = Convenio::WHERE('id', '=', $id_convenio)->GET();
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $fecha = Carbon::parse($convenios[0]->fecha_firma);
        $mes = $meses[($fecha->format('n')) - 1];
        $fecha_firma = $fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y');
        $fecha_vigencia = Carbon::parse($convenios[0]->fecha_vigencia);
        $mes_vigencia = $meses[($fecha_vigencia->format('n')) - 1];
        $fecha_vig = $fecha_vigencia->format('d') . ' de ' . $mes_vigencia . ' de ' . $fecha_vigencia->format('Y');

        return view('layouts.pages.vstshowconvenio',['convenios'=> $convenios, 'fecha_firma' => $fecha_firma, 'fecha_vigencia' => $fecha_vig]);
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
        $idConvenio = base64_decode($id);
        $convenios = Convenio::findOrfail($idConvenio);
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

            Convenio::findOrfail($idConvenio)->update($array_update);

            // validamos si hay archivos
            if ($request->hasFile('archivo_convenio')) {
                $convenios = new Convenio();
                $getConvenio = $convenios->WHERE('id', '=', $idConvenio)->GET();

                if (!is_null($getConvenio[0]->archivo_convenio)) {
                    # si no está nulo
                    $docConvenio = explode("/",$getConvenio[0]->archivo_convenio, 5);
                    // validación de documento en el servidor
                    if (Storage::exists($docConvenio[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docConvenio[4]);
                    }
                }

                $archivo_convenio = $request->file('archivo_convenio'); # obtenemos el archivo
                $url_archivo_convenio = $this->uploaded_file($archivo_convenio, $idConvenio, 'arcivo_convenio'); #invocamos el método
                // guardamos en la base de datos
                $convenioUpdate = Convenio::findOrfail($idConvenio);
                $convenioUpdate->update([
                    'archivo_convenio' => $url_archivo_convenio
                ]);
            }

            $noConvenio = $request->no_convenio;
            return redirect()->route('curso-inicio')
                    ->with('success', sprintf('CONVENIO %s  ACTUALIZADO EXTIOSAMENTE!', $noConvenio));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function alta_baja($id)
    {
        $available = convenioAvailable::WHERE('convenio_id', '=', $id)->FIRST();
        if($available == NULL)
        {
            $conv_available = new convenioAvailable();
            $conv_available->convenio_id = $id;
            $conv_available->CHK_TUXTLA = TRUE;
            $conv_available->CHK_TAPACHULA = TRUE;
            $conv_available->CHK_COMITAN = TRUE;
            $conv_available->CHK_REFORMA = TRUE;
            $conv_available->CHK_TONALA = TRUE;
            $conv_available->CHK_VILLAFLORES = TRUE;
            $conv_available->CHK_JIQUIPILAS = TRUE;
            $conv_available->CHK_CATAZAJA = TRUE;
            $conv_available->CHK_YAJALON = TRUE;
            $conv_available->CHK_SAN_CRISTOBAL = TRUE;
            $conv_available->CHK_CHIAPA_DE_CORZO = TRUE;
            $conv_available->CHK_MOTOZINTLA = TRUE;
            $conv_available->CHK_BERRIOZABAL = TRUE;
            $conv_available->CHK_PIJIJIAPAN = TRUE;
            $conv_available->CHK_JITOTOL = TRUE;
            $conv_available->CHK_LA_CONCORDIA = TRUE;
            $conv_available->CHK_VENUSTIANO_CARRANZA = TRUE;
            $conv_available->CHK_TILA = TRUE;
            $conv_available->CHK_TEOPISCA = TRUE;
            $conv_available->CHK_OCOSINGO = TRUE;
            $conv_available->CHK_CINTALAPA = TRUE;
            $conv_available->CHK_COPAINALA = TRUE;
            $conv_available->CHK_SOYALO = TRUE;
            $conv_available->CHK_ANGEL_ALBINO_CORZO = TRUE;
            $conv_available->CHK_ARRIAGA = TRUE;
            $conv_available->CHK_PICHUCALCO = TRUE;
            $conv_available->CHK_JUAREZ = TRUE;
            $conv_available->CHK_SIMOJOVEL = TRUE;
            $conv_available->CHK_MAPASTEPEC = TRUE;
            $conv_available->CHK_VILLA_CORZO = TRUE;
            $conv_available->CHK_CACAHOTAN = TRUE;
            $conv_available->CHK_ONCE_DE_ABRIL = TRUE;
            $conv_available->CHK_TUXTLA_CHICO = TRUE;
            $conv_available->CHK_OXCHUC = TRUE;
            $conv_available->CHK_CHAMULA = TRUE;
            $conv_available->CHK_OSTUACAN = TRUE;
            $conv_available->CHK_PALENQUE = TRUE;
            $conv_available->save();

            $available = convenioAvailable::WHERE('instructor_id', '=', $id)->FIRST();
        }
        return view('layouts.pages.vstaltabajains', compact('id','available'));
    }

    public function alta_baja_save(Request $request)
    {
        dd($request);
        $av_mod = convenioAvailable::find($request->id_available);
        $answer = $this->checkComparator($request->chk_tuxtla);
        $av_mod->CHK_TUXTLA = $answer;
        $answer = $this->checkComparator($request->chk_tapachula);
        $av_mod->CHK_TAPACHULA = $answer;
        $answer = $this->checkComparator($request->chk_comitan);
        $av_mod->CHK_COMITAN = $answer;
        $answer = $this->checkComparator($request->chk_reforma);
        $av_mod->CHK_REFORMA = $answer;
        $answer = $this->checkComparator($request->chk_tonala);
        $av_mod->CHK_TONALA = $answer;
        $answer = $this->checkComparator($request->chk_villaflores);
        $av_mod->CHK_VILLAFLORES = $answer;
        $answer = $this->checkComparator($request->chk_jiquipilas);
        $av_mod->CHK_JIQUIPILAS = $answer;
        $answer = $this->checkComparator($request->chk_catazaja);
        $av_mod->CHK_CATAZAJA = $answer;
        $answer = $this->checkComparator($request->chk_yajalon);
        $av_mod->CHK_YAJALON = $answer;
        $answer = $this->checkComparator($request->chk_san_cristobal);
        $av_mod->CHK_SAN_CRISTOBAL = $answer;
        $answer = $this->checkComparator($request->chk_chiapa_de_corzo);
        $av_mod->CHK_CHIAPA_DE_CORZO = $answer;
        $answer = $this->checkComparator($request->chk_motozintla);
        $av_mod->CHK_MOTOZINTLA = $answer;
        $answer = $this->checkComparator($request->chk_berriozabal);
        $av_mod->CHK_BERRIOZABAL = $answer;
        $answer = $this->checkComparator($request->chk_pijijiapan);
        $av_mod->CHK_PIJIJIAPAN = $answer;
        $answer = $this->checkComparator($request->chk_jitotol);
        $av_mod->CHK_JITOTOL = $answer;
        $answer = $this->checkComparator($request->chk_la_concordia);
        $av_mod->CHK_LA_CONCORDIA = $answer;
        $answer = $this->checkComparator($request->chk_venustiano_carranza);
        $av_mod->CHK_VENUSTIANO_CARRANZA = $answer;
        $answer = $this->checkComparator($request->chk_tila);
        $av_mod->CHK_TILA = $answer;
        $answer = $this->checkComparator($request->chk_teopisca);
        $av_mod->CHK_TEOPISCA = $answer;
        $answer = $this->checkComparator($request->chk_ocosingo);
        $av_mod->CHK_OCOSINGO = $answer;
        $answer = $this->checkComparator($request->chk_cintalapa);
        $av_mod->CHK_CINTALAPA = $answer;
        $answer = $this->checkComparator($request->chk_copainala);
        $av_mod->CHK_COPAINALA = $answer;
        $answer = $this->checkComparator($request->chk_soyalo);
        $av_mod->CHK_SOYALO = $answer;
        $answer = $this->checkComparator($request->chk_angel_albino_corzo);
        $av_mod->CHK_ANGEL_ALBINO_CORZO = $answer;
        $answer = $this->checkComparator($request->chk_arriaga);
        $av_mod->CHK_ARRIAGA = $answer;
        $answer = $this->checkComparator($request->chk_pichucalco);
        $av_mod->CHK_PICHUCALCO = $answer;
        $answer = $this->checkComparator($request->chk_juarez);
        $av_mod->CHK_JUAREZ = $answer;
        $answer = $this->checkComparator($request->chk_simojovel);
        $av_mod->CHK_SIMOJOVEL = $answer;
        $answer = $this->checkComparator($request->chk_mapastepec);
        $av_mod->CHK_MAPASTEPEC = $answer;
        $answer = $this->checkComparator($request->chk_villa_corzo);
        $av_mod->CHK_VILLA_CORZO = $answer;
        $answer = $this->checkComparator($request->chk_cacahoatan);
        $av_mod->CHK_CACAHOTAN = $answer;
        $answer = $this->checkComparator($request->chk_once_de_abril);
        $av_mod->CHK_ONCE_DE_ABRIL = $answer;
        $answer = $this->checkComparator($request->chk_tuxtla_chico);
        $av_mod->CHK_TUXTLA_CHICO = $answer;
        $answer = $this->checkComparator($request->chk_oxchuc);
        $av_mod->CHK_OXCHUC = $answer;
        $answer = $this->checkComparator($request->chk_chamula);
        $av_mod->CHK_CHAMULA = $answer;
        $answer = $this->checkComparator($request->chk_ostuacan);
        $av_mod->CHK_OSTUACAN = $answer;
        $answer = $this->checkComparator($request->chk_palenque);
        $av_mod->CHK_PALENQUE = $answer;
        $av_mod->save();

        return redirect('/convenios/indice')->with('success', 'Convenio Modificado exitosamente!');
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

    // protected function uploaded_file($file, $id, $name)
    // {
    //     $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
    //     $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
    //     # nuevo nombre del archivo
    //     $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
    //     $file->storeAs('/convenios/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
    //     $documentUrl = Storage::url('/convenios/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
    //     return $documentUrl;
    // }

    protected function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        //$path = $file->storeAs('/filesUpload/alumnos/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        //$documentUrl = $documentFile;
        $path = 'convenios/'.$id.'/'.$documentFile;
        Storage::disk('mydisk')->put($path, file_get_contents($file));
        //$path = storage_path('app/filesUpload/alumnos/'.$id.'/'.$documentFile);
        $documentUrl = Storage::disk('mydisk')->url('/uploadFiles/convenios/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
