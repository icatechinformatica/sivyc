<?php

namespace App\Http\Controllers\webController;

use App\Excel\xlsConvenios;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Convenio;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\File;
use App\Models\Municipio;
use App\Models\convenioAvailable;
use App\Models\organismosPublicos;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpParser\Node\Stmt\Foreach_;
use SebastianBergmann\Environment\Console;
use Maatwebsite\Excel\Facades\Excel;

use function Complex\add;

class ConveniosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $datos = [
            'selectOpcion' => $request->get('busqueda'),
            'campo_buscar' => $request->get('busqueda_conveniopor'),
            'fecha1' => $request->get('fecha1'),
            'fecha2' => $request->get('fecha2')
        ];

        $data = Convenio::Busqueda($request->get('busqueda'), $datos)
            ->select('convenios.*')
            ->orderByDesc('convenios.id')
            ->paginate(15, ['convenios.*']);

        return view('layouts.pages.vstconvenios', compact('data', 'datos', 'request'));
    }

    //generamos el excel
    protected function generar_excel(Request $request) {
        $datos = [
            'campo_buscar' => $request->get('busqueda_conveniopor'),
            'fecha1' => $request->get('fecha1'),
            'fecha2' => $request->get('fecha2')
        ];

        $data = Convenio::Busqueda($request->get('busqueda'), $datos)
            ->select('convenios.*')
            ->orderByDesc('convenios.id')
            ->get();

        $head = ['NO. DE CONVENIO', 'INSTITUCIÓN', 'FECHA DE FIRMA', 'FECHA DE TERMINO', 'TIPO DE CONVENIO',
                'SECTOR', 'STATUS'];
        $title = "CONVENIOS";
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.excelConvenios.excel_convenio';
        $datos_vista = [
            'data' => $data,
            'fecha1' => $request->get('fecha1'),
            'fecha2' => $request->get('fecha2'),
            'opcionsel' => $request->get('busqueda')
        ];

        if(count($data)>0)return Excel::download(new xlsConvenios($datos_vista,$head, $title,$view), $name);

    }

    public function conveniosAutocomplete(Request $request) {
        $search = $request->search;
        $tipoCurso = $request->tipoCurso;

        if($tipoCurso != '' && $tipoCurso != 'fechas'){
            if (isset($search) && $search != '') {
                $data = Convenio::select($tipoCurso)
                    ->where($tipoCurso, 'like', '%'.$search.'%')
                    ->limit(10)->get();
            }
            $response = array();
            foreach ($data as $value) {
                $response[] = array('label' => $value->$tipoCurso);
            }
            return json_encode($response);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        // mostrar formulario de convenio
        $unidades = DB::table('tbl_unidades')->orderBy('tbl_unidades.id')->get();
        $estados = DB::table('estados')->orderBy('estados.id')->get();
        $organismos = DB::table('organismos_publicos')
                        ->where('activo',true)
                        ->orderBy('organismo')
                        ->pluck('organismo','id');
        return view('layouts.pages.frmconvenio', compact('estados', 'unidades','organismos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // convenios guardarlo en el metodo store
        $unity = [];
        foreach ($request->unidades as $unidad) {
            $unity[] = $unidad;
        }

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

        $organismo = DB::table('organismos_publicos')->where('id', $request->institucion)->first();

        $convenios = new Convenio;
        $convenios['no_convenio'] = trim($request->input('no_convenio'));
        $convenios['institucion'] = $organismo->organismo;

        $convenios['tipo_sector'] = $request->no_convenio[0];

        $convenios['telefono'] = trim($request->input('telefono'));
        $convenios['fecha_firma'] = $convenios->getMyDateFormat($request->input('fecha_firma'));

        $convenios['fecha_vigencia'] = null;
        if ($request->input('fecha_termino') != null) {
            $convenios['fecha_vigencia'] = $convenios->getMyDateFormat($request->input('fecha_termino'));
        }

        $convenios['poblacion'] = trim($request->input('poblacion'));
        $convenios['municipio'] = trim($request->input('municipio'));
        $convenios['nombre_titular'] = trim($request->input('nombre_titular'));
        $convenios['nombre_enlace'] = trim($request->input('nombre_enlace'));
        $convenios['direccion'] = trim($request->input('direccion'));

        $convenios['tipo_convenio'] = trim($request->input('tipo_convenio'));
        $convenios['nombre_firma'] = trim($request->input('nombre_firma'));
        $convenios['tipo_convenio'] = trim($request->input('tipo_convenio'));
        $convenios['telefono_enlace'] = trim($request->input('telefono_enlace'));
        // $convenios['id_municipio'] = trim($request->input('municipio'));

        $convenios['correo_institucion'] = null;
        if ($request->input('correo_ins') != null) {
            $convenios['correo_institucion'] = trim($request->input('correo_ins'));
        }
        $convenios['correo_enlace'] = null;
        if ($request->input('correo_en') != null) {
            $convenios['correo_enlace'] = trim($request->input('correo_en'));
        }
        // $convenios['id_estado'] = trim($request->input('estadoG'));
        $convenios['id_organismo'] = trim($request->input('institucion'));

        $publicar = 'false';
        if ($request->input('publicar') != null) {
            $publicar = 'true';
        }
        $convenios['activo'] = $publicar;
        $convenios['sector'] = trim($request->input('sector'));
        $convenios['unidades'] = json_encode($unity);
        //agregamos la fecha updated_at
        $fecha_updated_at = date('Y-m-d');
        $convenios['updated_at'] = $fecha_updated_at;

        $convenios->save();

        # ==================================
        # Aquí tenemos el id recién guardado
        # ==================================
        $convenioId = $convenios->id;

        # trabajamos cargando el acta de nacimiento al servidor
        if ($request->hasFile('archivo_convenio')) {

            // obtenemos el valor de acta_nacimiento
            $doc_convenio = DB::table('convenios')->WHERE('id', $convenioId)->VALUE('archivo_convenio');
            // checamos que no sea nulo
            if (!is_null($doc_convenio)) {
                # si no está nulo
                if (!empty($doc_convenio)) {
                    $docFichaCerss = explode("/", $doc_convenio, 5);
                    if (Storage::exists($docFichaCerss[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docFichaCerss[4]);
                    }
                }
            }

            $doc_convenio = $request->file('archivo_convenio'); # obtenemos el archivo
            $url_doc_convenio = $this->uploaded_file($doc_convenio, $convenioId, 'archivo_convenio'); #invocamos el método
            // creamos un arreglo
            $arregloConvenio = [
                'archivo_convenio' => $url_doc_convenio
            ];

            // vamos a actualizar el registro con el arreglo que trae diferentes variables y carga de archivos
            DB::table('convenios')->WHERE('id', $convenioId)->update($arregloConvenio);

            // limpiamos el arreglo
            unset($arregloConvenio);
        }

        return redirect('/convenios/indice')->with('success', 'Convenio Agreado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
        $id_convenio = base64_decode($id);
        $convenios = Convenio::WHERE('id', '=', $id_convenio)->GET();

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $fecha = Carbon::parse($convenios[0]->fecha_firma);
        $mes = $meses[($fecha->format('n')) - 1];
        $fecha_firma = $fecha->format('d') . ' de ' . $mes . ' de ' . $fecha->format('Y');
        $fecha_vigencia = Carbon::parse($convenios[0]->fecha_vigencia);
        $mes_vigencia = $meses[($fecha_vigencia->format('n')) - 1];
        $fecha_vig = $fecha_vigencia->format('d') . ' de ' . $mes_vigencia . ' de ' . $fecha_vigencia->format('Y');

        return view('layouts.pages.vstshowconvenio', ['convenios' => $convenios, 'fecha_firma' => $fecha_firma, 'fecha_vigencia' => $fecha_vig]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $idConvenio = base64_decode($id);
        $convenios = Convenio::findOrfail($idConvenio);
        // $municipios = \DB::table('tbl_municipios')->where('tbl_municipios.id_estado', '=', $convenios->id_estado)->get();
        $unidades = DB::table('tbl_unidades')->orderBy('tbl_unidades.id')->get();
        // $estados = \DB::table('estados')->orderBy('estados.id')->get();
        $organismos = DB::table('organismos_publicos')
                        //->where('activo',true)
                        ->orderBy('organismo')
                        ->get();
                        // ->pluck('organismo','id');

        if ($convenios->id_organismo == null) {
            $id_organismo = DB::table('organismos_publicos')->select('id')
                        //->where('activo', true)
                        ->where('organismo', $convenios->institucion)->first();
            if($id_organismo) {
                $convenios->id_organismo = $id_organismo->id;
            }
        }

        $organismo = DB::table('organismos_publicos as o')->select(
                            'o.organismo',
                            'o.nombre_titular',
                            'o.telefono',
                            'o.correo as correo',
                            'o.direccion',
                            'l.localidad',
                            'e.nombre as estado',
                            'm.muni as municipio'
                        )
                        ->leftJoin('tbl_localidades as l','o.clave_localidad','=','l.clave')
                        ->join('estados as e', 'o.id_estado', 'e.id')
                        ->join('tbl_municipios as m', 'o.id_municipio', 'm.id')
                        ->where('o.id',$convenios->id_organismo)
                        ->first();

        return view('layouts.pages.editconvenio', ['convenios' => $convenios, 'unidades' => $unidades, 'organismos' => $organismos, 'organismo' => $organismo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $idConvenio = base64_decode($id);

        if (isset($idConvenio)) {
            $publicar = 'false';
            if ($request->input('publicar') != null) {
                $publicar = 'true';
            }

            $unity = [];
            if ($request->unidades != null) {
                foreach ($request->unidades as $unidad) {
                    $unity[] = $unidad;
                }
            }

            $organismo = DB::table('organismos_publicos')->where('id', $request->institucion)->first();
            //agregamos la fecha updated_at

            $fecha_updated_at = date('Y-m-d');
            $array_update = [
                'no_convenio' => trim($request->no_convenio),
                'tipo_sector' => $request->no_convenio[0],
                // 'institucion' => trim($request->institucion),
                'institucion' => $organismo->organismo,

                'fecha_firma' => Carbon::parse(trim($request->fecha_firma))->format('Y-m-d'),
                'fecha_vigencia' => Carbon::parse(trim($request->fecha_termino))->format('Y-m-d'),
                'poblacion' => trim($request->poblacion),
                'municipio' => trim($request->municipio),
                'nombre_titular' => trim($request->nombre_titular),
                'nombre_enlace' => trim($request->nombre_enlace),
                'direccion' => trim($request->direccion),
                'telefono' => trim($request->telefono),
                'tipo_convenio' => trim($request->tipo_convenio),
                'nombre_firma' => trim($request->nombre_firma),
                'telefono_enlace' => trim($request->telefono_enlace),
                // 'id_municipio' => trim($request->id_municipio),
                'activo' => trim($publicar),
                'sector' => trim($request->tipo),
                'correo_institucion' => $request->correo_ins,
                'correo_enlace' => $request->correo_en,
                // 'id_estado' => $request->estadoG,
                'id_organismo' => trim($request->input('institucion')),
                'updated_at' => $fecha_updated_at,
                'unidades' => json_encode($unity)
            ];

            Convenio::findOrfail($idConvenio)->update($array_update);

            // validamos si hay archivos
            if ($request->hasFile('archivo_convenio')) {
                $convenios = new Convenio();
                $getConvenio = $convenios->WHERE('id', '=', $idConvenio)->GET();

                if (!is_null($getConvenio[0]->archivo_convenio)) {
                    # si no está nulo
                    $docConvenio = explode("/", $getConvenio[0]->archivo_convenio, 5);
                    // validación de documento en el servidor
                    if (Storage::exists($docConvenio[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docConvenio[4]);
                    }
                }

                $archivo_convenio = $request->file('archivo_convenio'); # obtenemos el archivo
                $url_archivo_convenio = $this->uploaded_file($archivo_convenio, $idConvenio, 'archivo_convenio'); #invocamos el método
                // guardamos en la base de datos
                $convenioUpdate = Convenio::findOrfail($idConvenio);
                $convenioUpdate->update([
                    'archivo_convenio' => $url_archivo_convenio
                ]);
            }

            $noConvenio = $request->no_convenio;
            return redirect()->route('convenios.index')
                ->with('success', sprintf('CONVENIO %s  ACTUALIZADO EXTIOSAMENTE!', $noConvenio));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function alta_baja($id) {
        $available = convenioAvailable::WHERE('convenio_id', '=', $id)->FIRST();
        if ($available == NULL) {
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
        return view('layouts.pages.vstaltabajains', compact('id', 'available'));
    }

    public function alta_baja_save(Request $request) {
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

    protected function checkComparator($check) {
        if (isset($check)) {
            $stat = TRUE;
        } else {
            $stat = FALSE;
        }
        return $stat;
    }

    /* protected function uploaded_file($file, $id, $name) {
        $tamanio = $file->getSize();
        $extensionFile = $file->getClientOriginalExtension();
        $documentFile = trim($name . "_" . date('YmdHis') . "_" . $id . "." . $extensionFile);
        $path = 'convenios/' . $id . '/' . $documentFile;
        Storage::disk('mydisk')->put($path, file_get_contents($file));
        $documentUrl = Storage::disk('mydisk')->url('/uploadFiles/convenios/' . $id . "/" . $documentFile);
        return $documentUrl;
    } */

    protected function getmunicipios(Request $request) {
        if (isset($request->idEst)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEstado=$request->idEst;
            $municipio = new municipio();
            $municipios = $municipio->WHERE('id_estado', '=', $idEstado)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($municipios);
        } else {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
        return $json;
    }

    protected function uploaded_file($file, $id, $name) {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."".date('YmdHis')."".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/convenios/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/convenios/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function getcampos(Request $request){
        $organismo = DB::table('organismos_publicos as o')
                    ->select(
                        'o.nombre_titular',
                        'o.telefono','o.correo',
                        'o.direccion','l.localidad',
                        'e.nombre as estado',
                        'm.muni as municipio'
                    )
                    ->leftJoin('tbl_localidades as l','o.clave_localidad','=','l.clave')
                    ->join('estados as e', 'o.id_estado', 'e.id')
                    ->join('tbl_municipios as m', 'o.id_municipio', 'm.id')
                    ->where('o.id',$request->organismo_id)
                    ->first();  //dd($organismo);
        $data['titular']= $organismo->nombre_titular;
        $data['telefono']= $organismo->telefono;
        $data['correo']= $organismo->correo;
        $data['direccion']= $organismo->direccion;
        $data['localidad']= $organismo->localidad;
        $data['estado']= $organismo->estado;
        $data['municipio']= $organismo->municipio;
        return response()->json($data);
    }

}
