<?php
/**
 * Elaborado por Daniel Méndez Cruz v.1.0
 */
namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\curso;
use App\Models\cursoAvailable;
use App\Models\especialidad;
use App\Models\Area;
use App\Models\tbl_unidades;
use App\Models\criterio_pago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport;

class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        /**
         *
         */
        $buscar_curso = $request->get('busquedaPorCurso');
        $tipoCurso = $request->get('tipo_curso');

        $unidadUser = Auth::user()->unidad;

        $userId = Auth::user()->id;

        $roles = DB::table('role_user')
            ->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')
            ->SELECT('roles.slug AS role_name')
            ->WHERE('role_user.user_id', '=', $userId)
            ->GET();

        if($roles[0]->role_name == 'admin' || $roles[0]->role_name == 'depto_academico')
        {
        $data = curso::searchporcurso($tipoCurso, $buscar_curso)->WHERE('cursos.id', '!=', '0')
        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
        ->PAGINATE(25, ['cursos.id', 'cursos.nombre_curso', 'cursos.modalidad', 'cursos.horas', 'cursos.clasificacion',
                   'cursos.costo', 'cursos.objetivo', 'cursos.perfil', 'cursos.solicitud_autorizacion',
                   'cursos.fecha_validacion', 'cursos.memo_validacion', 'cursos.memo_actualizacion',
                   'cursos.fecha_actualizacion', 'cursos.unidad_amovil', 'especialidades.nombre', 'cursos.tipo_curso']);
        }
        else
        {
            $data = curso::searchporcurso($tipoCurso, $buscar_curso)->WHERE('cursos.id', '!=', '0')
            ->WHERE('cursos.estado', '=', true)
            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->PAGINATE(25, ['cursos.id', 'cursos.nombre_curso', 'cursos.modalidad', 'cursos.horas', 'cursos.clasificacion',
                       'cursos.costo', 'cursos.objetivo', 'cursos.perfil', 'cursos.solicitud_autorizacion',
                       'cursos.fecha_validacion', 'cursos.memo_validacion', 'cursos.memo_actualizacion',
                       'cursos.fecha_actualizacion', 'cursos.unidad_amovil', 'especialidades.nombre', 'cursos.tipo_curso']);
        }
        return view('layouts.pages.vstacursosinicio',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $especialidad = new especialidad();
        $especialidades = $especialidad->all();
        $unidades = new tbl_unidades();
        $unidadesMoviles = $unidades->SELECT('ubicacion')->GROUPBY('ubicacion')->GET();
        $criterioPago = new criterio_pago;
        $cp = $criterioPago->all();
        $area = new Area();
        $areas = $area->all();
        // mostramos el formulario de cursos
        return view('layouts.pages.frmcursos', compact('especialidades', 'areas', 'unidadesMoviles', 'cp'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //
        try {
            //validación de archivos
            $unidades = ['TUXTLA', 'TAPACHULA', 'COMITAN', 'REFORMA', 'TONALA', 'VILLAFLORES', 'JIQUIPILAS', 'CATAZAJA',
            'YAJALON', 'SAN CRISTOBAL', 'CHIAPA DE CORZO', 'MOTOZINTLA', 'BERRIOZABAL', 'PIJIJIAPAN', 'JITOTOL',
            'LA CONCORDIA', 'VENUSTIANO CARRANZA', 'TILA', 'TEOPISCA', 'OCOSINGO', 'CINTALAPA', 'COPAINALA',
            'SOYALO', 'ANGEL ALBINO CORZO', 'ARRIAGA', 'PICHUCALCO', 'JUAREZ', 'SIMOJOVEL', 'MAPASTEPEC',
            'VILLA CORZO', 'CACAHOATAN', 'ONCE DE ABRIL', 'TUXTLA CHICO', 'OXCHUC', 'CHAMULA', 'OSTUACAN',
            'PALENQUE'];

            /**
             * MODIFICACION DE RESTRICCIÓN DE GUARDADO DE CURSOS
             * 26-ABRIL-2021 CREANDO RESTRICCIONES
             */
            $consulta_curso_existente = DB::table('cursos')
                ->where([
                    ['memo_actualizacion', '=', trim($request->memo_actualizacion)],
                    ['memo_validacion', '=', trim($request->memo_validacion)],
                    ['tipo_curso', '=', trim($request->tipo_curso)],
                    ['nombre_curso', 'LIKE', '%'.trim($request->nombrecurso).'%']
                ])
                ->get();

            if (count($consulta_curso_existente) > 0) {
                # si es mayor a cero hay registros en la consulta
                return redirect()->back()->withErrors(['msg', sprintf('EL CURSO %s YA SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS', $request->nombrecurso)]);
            } else {
                # por el contrario no hay registros se procede a guardar el registro en la base de datos

                $cursos = new curso;
                $cursos->nombre_curso = trim($request->nombrecurso);
                $cursos->modalidad = trim($request->modalidad);
                $cursos->clasificacion = trim($request->clasificacion);
                $cursos->costo = trim($request->costo);
                $cursos->horas = trim($request->duracion);
                $cursos->objetivo = trim($request->objetivo);
                $cursos->perfil = trim($request->perfil);
                $cursos->fecha_validacion = $cursos->setFechaAttribute($request->fecha_validacion);
                $cursos->fecha_actualizacion = $cursos->setFechaAttribute($request->fecha_actualizacion);
                $cursos->descripcion = trim($request->descripcionCurso);
                $cursos->no_convenio = trim($request->no_convenio);
                $cursos->id_especialidad = $request->especialidadCurso;
                $cursos->unidad_amovil = trim($request->unidad_accion_movil);
                $cursos->area = $request->areaCursos;
                $cursos->solicitud_autorizacion = $request->solicitud_autorizacion;
                $cursos->memo_actualizacion = trim($request->memo_actualizacion);
                $cursos->memo_validacion = trim($request->memo_validacion);
                $cursos->cambios_especialidad = trim($request->cambios_especialidad);
                $cursos->nivel_estudio = trim($request->nivel_estudio);
                $cursos->categoria = trim($request->categoria);
                $cursos->tipo_curso = trim($request->tipo_curso);
                $cursos->rango_criterio_pago_minimo = trim($request->criterio_pago_minimo);
                $cursos->rango_criterio_pago_maximo = trim($request->criterio_pago_maximo);
                $cursos->unidades_disponible = $unidades;
                $cursos->estado = TRUE;
                $cursos->save();

                # ==================================
                # Aquí tenemos el id recién guardado
                # ==================================
                $cursosId = $cursos->id;

                // validamos si hay archivos
                if ($request->hasFile('documento_solicitud_autorizacion')) {
                    # Carga el archivo y obtener la url
                    $documento_solicitud_autorizacion = $request->file('documento_solicitud_autorizacion'); # obtenemos el archivo
                    $url_solicitud_autorizacion = $this->uploaded_file($documento_solicitud_autorizacion, $cursosId, 'documento_solicitud_autorizacion'); #invocamos el método
                    // guardamos en la base de datos
                    $cursoUpdate = curso::find($cursosId);
                    $cursoUpdate->documento_solicitud_autorizacion = $url_solicitud_autorizacion;
                    $cursoUpdate->save();
                }

                // validamos el siguiente archivo
                if ($request->hasFile('documento_memo_validacion')) {
                    # Carga el archivo y obtener la url
                    $documento_memo_validacion = $request->file('documento_memo_validacion'); # obtenemos el archivo
                    $url_memo_validacion = $this->uploaded_file($documento_memo_validacion, $cursosId, 'documento_memo_validacion'); #invocamos el método
                    // guardamos en la base de datos
                    $cursoUp = curso::find($cursosId);
                    $cursoUp->documento_memo_validacion = $url_memo_validacion;
                    $cursoUp->save();
                }

                // validamos el siguiente archivo
                if ($request->hasFile('documento_memo_actualizacion')) {
                    # Carga el archivo y obtener la url
                    $documento_memo_actualizacion = $request->file('documento_memo_actualizacion'); # obtenemos el archivo
                    $url_memo_actualizacion = $this->uploaded_file($documento_memo_actualizacion, $cursosId, 'documento_memo_actualizacion'); #invocamos el método
                    // guardamos en la base de datos
                    $cursoU = curso::find($cursosId);
                    $cursoU->documento_memo_actualizacion = $url_memo_actualizacion;
                    $cursoU->save();
                }

                return redirect()->route('curso-inicio')->with('success', 'Nuevo Curso Agregado!');

            }

        } catch (Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //consulta sql
            $area = new Area();
            $areas = $area->all();

            $Especialidad = new especialidad();
            $especialidades = $Especialidad->all();
            $unidades = new tbl_unidades();
            $unidadesMoviles = $unidades->SELECT('ubicacion')->GROUPBY('ubicacion')->GET();
            $criterioPago = new criterio_pago;
            $criterio_pago = $criterioPago->all();

            $idCurso = base64_decode($id);
            $curso = new curso();
            $cursos = $curso::SELECT('cursos.id','cursos.estado','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
                    'cursos.costo','cursos.duracion','cursos.tipo_curso',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad', 'cursos.id_especialidad',
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria', 'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
                    'cursos.rango_criterio_pago_minimo', 'rango_criterio_pago_maximo')
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')
                    ->GET();

            $fechaVal = $curso->getMyDateFormat($cursos[0]->fecha_validacion);
            $fechaAct = $curso->getMyDateFormat($cursos[0]->fecha_actualizacion);

            return view('layouts.pages.frmedit_curso', compact('cursos', 'areas', 'especialidades', 'fechaVal', 'fechaAct', 'unidadesMoviles', 'criterio_pago'));

        } catch (\Throwable $th) {
            //throw $th;
        }

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
    }

    protected function get_by_area($idAreas)
    {
        if (isset($idAreas)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idAreas = $idAreas;
            $Especialidad = new especialidad();

            $Especialidades = $Especialidad->WHERE('id_areas', '=', $idAreas)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($Especialidades);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function get_by_id($idCurso)
    {
        if (isset($idCurso)) {
            # code...
            $cursos = new curso();
            $curso = $cursos::SELECT('cursos.id','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
                    'cursos.costo','cursos.duracion',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad','cursos.tipo_curso' ,
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria',
                    'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion')
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')
                    ->GET();

            $json= response()->json($curso, 200);
        } else {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Curso para filtar'));
        }
        return $json;
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
        $cursos = new curso();
        // modificacion de un recurso guardado
        if (isset($id)) {
            $array = [
                'nombre_curso' => trim($request->nombrecurso),
                'modalidad' => trim($request->modalidad),
                'horas' => trim($request->duracion),
                'clasificacion' => trim($request->clasificacion),
                'costo' => trim($request->costo),
                'objetivo' => trim($request->objetivo),
                'perfil' => trim($request->perfil),
                'fecha_validacion' => $cursos->setFechaAttribute($request->fecha_validacion),
                'fecha_actualizacion' => $cursos->setFechaAttribute($request->fecha_actualizacion),
                'descripcion' => trim($request->descripcionCurso),
                'no_convenio' => trim($request->no_convenio),
                'id_especialidad' => trim($request->especialidadCurso),
                'unidad_amovil' => trim($request->unidad_accion_movil),
                'area' => $request->areaCursos,
                'solicitud_autorizacion' => (isset($request->solicitud_autorizacion)) ? $request->solicitud_autorizacion : false,
                'memo_actualizacion' => trim($request->memo_actualizacion),
                'memo_validacion' => trim($request->memo_validacion),
                'cambios_especialidad' => trim($request->cambios_especialidad),
                'nivel_estudio' => trim($request->nivel_estudio),
                'categoria' => trim($request->categoria),
                'tipo_curso' => trim($request->tipo_curso),
            ];

            $cursos->WHERE('id', '=', $id)->UPDATE($array);
            if($request->estado != NULL)
            {
                $cursos->WHERE('id', '=', $id)
                ->UPDATE(['estado' => TRUE,
                          'rango_criterio_pago_minimo' => trim($request->criterio_pago_minimo_edit),
                          'rango_criterio_pago_maximo' => trim($request->criterio_pago_maximo_edit)]);
            }
            else
            {
                $cursos->WHERE('id', '=', $id)
                ->UPDATE(['estado' => FALSE,
                          'rango_criterio_pago_minimo' => trim($request->criterio_pago_minimo_edit),
                          'rango_criterio_pago_maximo' => trim($request->criterio_pago_maximo_edit)]);
            }

            # ==================================
            # Aquí modificamos el curso con id
            # ==================================

            // validamos si hay archivos
            if ($request->hasFile('documento_solicitud_autorizacion')) {
                // obtenemos el valor de documento_solicitud_autorizacion
                $cursos = new curso();
                $curso = $cursos->WHERE('id', '=', $id)->GET();
                // checamos que no sea nulo
                if (!is_null($curso[0]->documento_solicitud_autorizacion)) {
                    # si no está nulo
                    $docSolicitudAutorizacion = explode("/",$curso[0]->documento_solicitud_autorizacion, 5);
                    //dd($docSolicitudAutorizacion[4]);
                    //dd(Storage::exists($docSolicitudAutorizacion[4]));
                    if (Storage::exists($docSolicitudAutorizacion[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docSolicitudAutorizacion[4]);
                    }
                }

                # Carga el archivo y obtener la url
                $documento_solicitud_autorizacion = $request->file('documento_solicitud_autorizacion'); # obtenemos el archivo
                $url_solicitud_autorizacion = $this->uploaded_file($documento_solicitud_autorizacion, $id, 'documento_solicitud_autorizacion_update'); #invocamos el método
                // guardamos en la base de datos
                $cursoUpdate = curso::find($id);
                $cursoUpdate->documento_solicitud_autorizacion = $url_solicitud_autorizacion;
                $cursoUpdate->update([
                    'documento_solicitud_autorizacion' => $url_solicitud_autorizacion
                ]);
            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_validacion')) {
                # Carga el archivo y obtener la url
                $cursos = new curso();
                $curso = $cursos->WHERE('id', '=', $id)->GET();

                if (!is_null($curso[0]->documento_memo_validacion)) {
                    # si no está nulo
                    $docMemoValidacion = explode("/",$curso[0]->documento_memo_validacion, 5);
                    // validación de documento en el servidor
                    if (Storage::exists($docMemoValidacion[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docMemoValidacion[4]);
                    }
                }

                $documento_memo_validacion = $request->file('documento_memo_validacion'); # obtenemos el archivo
                $url_memo_validacion = $this->uploaded_file($documento_memo_validacion, $id, 'documento_memo_validacion_update'); #invocamos el método
                // guardamos en la base de datos
                $cursoUp = curso::find($id);
                $cursoUp->documento_memo_validacion = $url_memo_validacion;
                $cursoUp->update([
                    'documento_memo_validacion' => $url_memo_validacion
                ]);
            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_actualizacion')) {
                # Carga el archivo y obtener la url
                $cursos = new curso();
                $curso = $cursos->WHERE('id', '=', $id)->GET();
                if (!is_null($curso[0]->documento_memo_actualizacion)) {
                    # si no está nulo
                    $docMemoActualizacion = explode("/", $curso[0]->documento_memo_actualizacion, 5);
                    // validación de documento en el servidor
                    if (Storage::exists($docMemoActualizacion[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docMemoActualizacion[4]);
                    }
                }

                $documento_memo_actualizacion = $request->file('documento_memo_actualizacion'); # obtenemos el archivo
                $url_memo_actualizacion = $this->uploaded_file($documento_memo_actualizacion, $id, 'documento_memo_actualizacion_update'); #invocamos el método
                // guardamos en la base de datos
                $cursoU = curso::find($id);
                $cursoU->documento_memo_actualizacion = $url_memo_actualizacion;
                $cursoU->update([
                    'documento_memo_actualizacion' => $url_memo_actualizacion
                ]);
            }

            $nombreCurso = $request->nombrecurso;
            return redirect()->route('curso-inicio')
                    ->with('success', sprintf('CURSO %s  ACTUALIZADO EXTIOSAMENTE!', $nombreCurso));
        }

    }

    public function alta_baja($id)
    {
        $av = curso::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        if($av == NULL)
        {
            $reform = curso::find($id);
            $unidades = ['TUXTLA', 'TAPACHULA', 'COMITAN', 'REFORMA', 'TONALA', 'VILLAFLORES', 'JIQUIPILAS', 'CATAZAJA',
            'YAJALON', 'SAN CRISTOBAL', 'CHIAPA DE CORZO', 'MOTOZINTLA', 'BERRIOZABAL', 'PIJIJIAPAN', 'JITOTOL',
            'LA CONCORDIA', 'VENUSTIANO CARRANZA', 'TILA', 'TEOPISCA', 'OCOSINGO', 'CINTALAPA', 'COPAINALA',
            'SOYALO', 'ANGEL ALBINO CORZO', 'ARRIAGA', 'PICHUCALCO', 'JUAREZ', 'SIMOJOVEL', 'MAPASTEPEC',
            'VILLA CORZO', 'CACAHOATAN', 'ONCE DE ABRIL', 'TUXTLA CHICO', 'OXCHUC', 'CHAMULA', 'OSTUACAN',
            'PALENQUE'];

            $reform->unidades_disponible = $unidades;
            $reform->save();

            $av = curso::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        }

        $available = $av->unidades_disponible;

        return view('layouts.pages.vstaltabajacur', compact('id','available'));
    }

    public function exportar_cursos()
    {
        $data = curso::SELECT('cursos.id','area.formacion_profesional','especialidades.nombre as especialidad',
                        'cursos.nombre_curso','cursos.tipo_curso','cursos.modalidad','cursos.categoria',
                        'cursos.clasificacion','cursos.costo','cursos.horas','cursos.objetivo','cursos.perfil',
                        'cursos.nivel_estudio',
                        DB::raw("(case when cursos.solicitud_autorizacion <> 'FALSE' then 'SI' else 'NO' end) as etnia"),
                        'cursos.memo_validacion','cursos.fecha_validacion','cursos.memo_actualizacion',
                        'cursos.fecha_actualizacion','cursos.rango_criterio_pago_minimo',
                        'cursos.rango_criterio_pago_maximo','cursos.unidad_amovil')
                        ->WHERE('cursos.estado', '=', 'TRUE')
                        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
                        ->LEFTJOIN('area', 'area.id', '=', 'especialidades.id_areas')
                        ->ORDERBY('especialidades.nombre', 'ASC')
                        ->ORDERBY('cursos.nombre_curso', 'ASC')
                        ->GET();
                        //dd($data[0]);

        $cabecera = [
            'ID','CAMPO','ESPECIALIDAD','NOMBRE','TIPO CURSO','MODALIDAD','CATEGORIA','CLASIFICACION','COSTO','HORAS',
            'OBJETIVO','PERFIL','NIVEL DE ESTUDIO','SOLICITUD DE AUTORIZACION','MEMO DE VALIDACION',
            'FECHA DE VALIDACION','MEMO DE ACTUALIZACION','FECHA DE ACTUALIZACION','CRITERIO DE PAGO MINIMO',
            'CRITERIO DE PAGO MAXIMO','UNIDAD MOVIL'
        ];
        $nombreLayout = "Catalogo de cursos.xlsx";
        $titulo = "Catalogo de cursos";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    public function alta_baja_save(Request $request)
    {
        $unidades = [];
        if($this->checkComparator($request->chk_tuxtla) == TRUE)
        {
            array_push($unidades, 'TUXTLA');
        }
        if($this->checkComparator($request->chk_tapachula) == TRUE)
        {
            array_push($unidades, 'TAPACHULA');
        }
        if($this->checkComparator($request->chk_comitan) == TRUE)
        {
            array_push($unidades, 'COMITAN');
        }
        if($this->checkComparator($request->chk_reforma) == TRUE)
        {
            array_push($unidades, 'REFORMA');
        }
        if($this->checkComparator($request->chk_tonala) == TRUE)
        {
            array_push($unidades, 'TONALA');
        }
        if($this->checkComparator($request->chk_villaflores) == TRUE)
        {
            array_push($unidades, 'VILLAFLORES');
        }
        if($this->checkComparator($request->chk_jiquipilas) == TRUE)
        {
            array_push($unidades, 'JIQUIPILAS');
        }
        if($this->checkComparator($request->chk_catazaja) == TRUE)
        {
            array_push($unidades, 'CATAZAJA');
        }
        if($this->checkComparator($request->chk_yajalon) == TRUE)
        {
            array_push($unidades, 'YAJALON');
        }
        if($this->checkComparator($request->chk_san_cristobal) == TRUE)
        {
            array_push($unidades, 'SAN CRISTOBAL');
        }
        if($this->checkComparator($request->chk_chiapa_de_corzo) == TRUE)
        {
            array_push($unidades, 'CHIAPA DE CORZO');
        }
        if($this->checkComparator($request->chk_motozintla) == TRUE)
        {
            array_push($unidades, 'MOTOZINTLA');
        }
        if($this->checkComparator($request->chk_berriozabal) == TRUE)
        {
            array_push($unidades, 'BERRIOZABAL');
        }
        if($this->checkComparator($request->chk_pijijiapan) == TRUE)
        {
            array_push($unidades, 'PIJIJIAPAN');
        }
        if($this->checkComparator($request->chk_jitotol) == TRUE)
        {
            array_push($unidades, 'JITOTOL');
        }
        if($this->checkComparator($request->chk_la_concordia) == TRUE)
        {
            array_push($unidades, 'LA CONCORDIA');
        }
        if($this->checkComparator($request->chk_venustiano_carranza) == TRUE)
        {
            array_push($unidades, 'VENUSTIANO CARRANZA');
        }
        if($this->checkComparator($request->chk_tila) == TRUE)
        {
            array_push($unidades, 'TILA');
        }
        if($this->checkComparator($request->chk_teopisca) == TRUE)
        {
            array_push($unidades, 'TEOPISCA');
        }
        if($this->checkComparator($request->chk_ocosingo) == TRUE)
        {
            array_push($unidades, 'OCOSINGO');
        }
        if($this->checkComparator($request->chk_cintalapa) == TRUE)
        {
            array_push($unidades, 'CINTALAPA');
        }
        if($this->checkComparator($request->chk_copainala) == TRUE)
        {
            array_push($unidades, 'COPAINALA');
        }
        if($this->checkComparator($request->chk_soyalo) == TRUE)
        {
            array_push($unidades, 'SOYALO');
        }
        if($this->checkComparator($request->chk_angel_albino_corzo) == TRUE)
        {
            array_push($unidades, 'ANGEL ALBINO CORZO');
        }
        if($this->checkComparator($request->chk_arriaga) == TRUE)
        {
            array_push($unidades, 'ARRIAGA');
        }
        if($this->checkComparator($request->chk_pichucalco) == TRUE)
        {
            array_push($unidades, 'PICHUCALCO');
        }
        if($this->checkComparator($request->chk_juarez) == TRUE)
        {
            array_push($unidades, 'JUAREZ');
        }
        if($this->checkComparator($request->chk_simojovel) == TRUE)
        {
            array_push($unidades, 'SIMOJOVEL');
        }
        if($this->checkComparator($request->chk_mapastepec) == TRUE)
        {
            array_push($unidades, 'MAPASTEPEC');
        }
        if($this->checkComparator($request->chk_villa_corzo) == TRUE)
        {
            array_push($unidades, 'VILLA CORZO');
        }
        if($this->checkComparator($request->chk_cacahoatan) == TRUE)
        {
            array_push($unidades, 'CACAHOATAN');
        }
        if($this->checkComparator($request->chk_once_de_abril) == TRUE)
        {
            array_push($unidades, 'ONCE DE ABRIL');
        }
        if($this->checkComparator($request->chk_tuxtla_chico) == TRUE)
        {
            array_push($unidades, 'TUXTLA CHICO');
        }
        if($this->checkComparator($request->chk_oxchuc) == TRUE)
        {
            array_push($unidades, 'OXCHUC');
        }
        if($this->checkComparator($request->chk_chamula) == TRUE)
        {
            array_push($unidades, 'CHAMULA');
        }
        if($this->checkComparator($request->chk_ostuacan) == TRUE)
        {
            array_push($unidades, 'OSTUACAN');
        }
        if($this->checkComparator($request->chk_palenque) == TRUE)
        {
            array_push($unidades, 'PALENQUE');
        }

        $reform = curso::find($request->id_available);
        $reform->unidades_disponible = $unidades;
        $reform->save();

        return redirect()->route('curso-inicio')
                ->with('success','Curso Modificado');
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

    protected function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/cursos/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/cursos/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
