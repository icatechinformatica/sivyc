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
use App\Models\grupos_vulnerables;
use App\Models\instructor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\FormatoTReport;
use App\Excel\xlsCursosMultiple;
use App\Excel\xlsCursosDV;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class CursosController extends Controller
{

    private $slug;
    function __construct() {
        $this->categorias = ['OFICIOS','PROFESIONALIZACIÓN','ESPECIALIZACIÓN','SALUD','CURSO ALFA'];
        $this->perfil = [
            'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO'
        ];
    }
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
        $this->slug = $roles[0]->role_name;
        $data = curso::searchporcurso($tipoCurso, $buscar_curso)->WHERE('cursos.id', '!=', '0');
        if($roles[0]->role_name != 'admin' && $roles[0]->role_name != 'auxiliar_paqueteias-todos' && $roles[0]->role_name != 'titular-innovacion'){
            $data = $data->WHERE('cursos.estado', '=', true);
        }
        $data = $data->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->leftJoin('users as user_created', 'cursos.iduser_created', '=', 'user_created.id')
            ->leftJoin('users as user_updated', 'cursos.iduser_updated', '=', 'user_updated.id')
            ->leftJoin(DB::raw("(
                                SELECT id_curso,
                                    FLOOR(SUM(EXTRACT(EPOCH FROM duracion::interval)) / 3600)::int as horas_tematico
                                FROM contenido_tematico
                                WHERE id_parent = 0
                                GROUP BY id_curso
                            ) as duracion_total"), 'duracion_total.id_curso', '=', 'cursos.id')
            ->PAGINATE(25, ['cursos.id', 'cursos.nombre_curso', 'cursos.modalidad', 'cursos.horas', 'cursos.clasificacion',
                       'cursos.costo', 'cursos.objetivo', 'cursos.perfil', 'cursos.solicitud_autorizacion',
                       'cursos.fecha_validacion', 'cursos.memo_validacion', 'cursos.memo_actualizacion',
                       'cursos.fecha_actualizacion', 'cursos.unidad_amovil', 'especialidades.nombre',
                       'cursos.tipo_curso', 'cursos.rango_criterio_pago_minimo', 'cursos.rango_criterio_pago_maximo',
                       DB::raw("CASE WHEN cursos.estado ='true' THEN 'ACTIVO' ELSE (CASE WHEN cursos.estado='false' THEN  'INACTIVO' ELSE 'BAJA' END) END as estado"),
                       'cursos.servicio','cursos.proyecto','cursos.file_carta_descriptiva',
                        DB::raw("REPLACE(user_created.name, 'BAJA', '') as user_created_name"),
                        DB::raw("REPLACE(user_updated.name, 'BAJA', '') as user_updated_name"),
                        'cursos.created_at', 'cursos.updated_at',
                        DB::raw("COALESCE(duracion_total.horas_tematico, 0) as horas_tematico")
                    ]);
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
        $unidadesMoviles = $unidades->SELECT('ubicacion')->orderBy('ubicacion', 'asc')->GROUPBY('ubicacion')->GET();
        $criterioPago = new criterio_pago;
        $cp = $criterioPago->Where('id','!=','0')->Where('activo', TRUE)->GET();
        $area = new Area();
        $areas = $area->all();
        $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo','ASC')->GET();
        $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
        $categorias = $this->categorias;
        $perfil = $this->perfil;
        // mostramos el formulario   de cursos
        return view('layouts.pages.frmcursos', compact('especialidades', 'areas', 'unidadesMoviles', 'cp', 'gruposvulnerables','dependencias','categorias','perfil'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        // dd($request);
        $chkcur = curso::WHERE('nombre_curso','=', $request->nombrecurso)->WHERE('tipo_curso','=', $request->tipo_curso)->FIRST();
        if(isset($chkcur))
        {
            return back()->withErrors(['msg' => 'EL CURSO YA ESTA REGISTRADO EN EL CATALOGO']);
        }
        try {
            //validación de archivos
            $gv = [];
            $dp = [];
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
                return redirect()->back()->withErrors([sprintf('EL CURSO %s YA SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS', $request->nombrecurso)]);
            } else {
                # por el contrario no hay registros se procede a guardar el registro en la base de datos
                $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo','ASC')->GET();
                $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
                if($request->a != NULL)
                {
                    foreach($gruposvulnerables as $cadwell)
                    {
                        foreach($request->a as $data)
                        {
                            if($cadwell->grupo == $data)
                            {
                                array_push($gv, $data);
                            }
                        }
                    }
                }
                if($request->b != NULL)
                {
                    foreach($dependencias as $cadwell)
                    {
                        foreach($request->b as $data)
                        {
                            if($cadwell->organismo == $data)
                            {
                                array_push($dp, $data);
                            }
                        }
                    }
                }
                // dd($gv);

                if($request->proyecto==true) $proyecto = true;
                else $proyecto =false;

                if($request->curso_riesgo==true) $riesgo = true;
                else $riesgo =false;

                if($request->estado==1) $estado = true;
                elseif($request->estado==2) $estado = false;
                else $estado = null;

                if($request->curso_alfa=='si') $curso_alfa = true;
                else $curso_alfa = false;

                $cursos = new curso;
                $cursos->nombre_curso = trim($request->nombrecurso);
                $cursos->modalidad = trim($request->modalidad);
                $cursos->clasificacion = trim($request->clasificacion);
                $cursos->costo = trim($request->costo);
                $cursos->horas = trim($request->duracion);
                $cursos->objetivo = trim($request->objetivo);
                $cursos->perfil = trim($request->perfil);
                $cursos->fecha_solicitud = $cursos->setFechaAttribute($request->fecha_solicitud);
                $cursos->fecha_validacion = $cursos->setFechaAttribute($request->fecha_validacion);
                $cursos->fecha_actualizacion = $cursos->setFechaAttribute($request->fecha_actualizacion);
                $cursos->descripcion = trim($request->descripcionCurso);
                $cursos->no_convenio = trim($request->no_convenio);
                $cursos->id_especialidad = $request->especialidadCurso;
                if($request->unidad_accion_movil == '0')
                {
                    $cursos->unidad_amovil = trim($request->unidad_ubicacion_especificar);
                }
                else
                {
                    $cursos->unidad_amovil = trim($request->unidad_accion_movil);
                }
                $cursos->area = $request->areaCursos;
                $cursos->solicitud_autorizacion = $request->solicitud_autorizacion;
                $cursos->memo_actualizacion = trim($request->memo_actualizacion);
                $cursos->memo_validacion = trim($request->memo_validacion);
                $cursos->cambios_especialidad = trim($request->cambios_especialidad);
                $cursos->categoria = trim($request->categoria);
                $cursos->tipo_curso = trim($request->tipo_curso);
                $cursos->rango_criterio_pago_minimo = trim($request->criterio_pago_minimo);
                $cursos->rango_criterio_pago_maximo = trim($request->criterio_pago_maximo);
                $cursos->unidades_disponible = $unidades;
                $cursos->estado = TRUE;
                // $cursos->observacion = $request->observaciones;
                $cursos->grupo_vulnerable = $gv;
                $cursos->dependencia = $dp;
                $cursos->created_at = date('Y-m-d h:m:s');

                $cursos->proyecto = $proyecto;
                $cursos->riesgo = $riesgo;
                $cursos->estado = $estado;
                $cursos->servicio = json_encode($request->servicio);
                $cursos->motivo = trim($request->motivo);
                $cursos->iduser_created = Auth::user()->id;
                $cursos->curso_alfa = $curso_alfa;

                $cursos->save();

                # ==================================
                # Aquí tenemos el id recién guardado
                # ==================================
                $cursosId = $cursos->id;
                $url_solicitud_autorizacion = $url_memo_validacion = $url_memo_actualizacion = $url_carta_descriptiva = null;
                // validamos si hay archivos
                if ($request->hasFile('documento_solicitud_autorizacion')) {
                    # Carga el archivo y obtener la url
                    $documento_solicitud_autorizacion = $request->file('documento_solicitud_autorizacion'); # obtenemos el archivo
                    $url_solicitud_autorizacion = $this->uploaded_file($documento_solicitud_autorizacion, $cursosId, 'documento_solicitud_autorizacion'); #invocamos el método
                }

                // validamos el siguiente archivo
                if ($request->hasFile('documento_memo_validacion')) {
                    # Carga el archivo y obtener la url
                    $documento_memo_validacion = $request->file('documento_memo_validacion'); # obtenemos el archivo
                    $url_memo_validacion = $this->uploaded_file($documento_memo_validacion, $cursosId, 'documento_memo_validacion'); #invocamos el método

                }

                // validamos el siguiente archivo
                if ($request->hasFile('documento_memo_actualizacion')) {
                    # Carga el archivo y obtener la url
                    $documento_memo_actualizacion = $request->file('documento_memo_actualizacion'); # obtenemos el archivo
                    $url_memo_actualizacion = $this->uploaded_file($documento_memo_actualizacion, $cursosId, 'documento_memo_actualizacion'); #invocamos el método

                }

                if ($request->hasFile('file_carta_descriptiva')) {
                    $file_carta_descriptiva = $request->file('file_carta_descriptiva');
                    $url_carta_descriptiva = $this->uploaded_file($file_carta_descriptiva, $cursosId, 'carta_descriptiva');
                }

                if($url_solicitud_autorizacion OR $url_memo_validacion OR $url_memo_actualizacion OR $url_carta_descriptiva ){
                    $cursoUpdate = curso::find($cursosId);
                    if($url_solicitud_autorizacion) $cursoUpdate->documento_solicitud_autorizacion = $url_solicitud_autorizacion;
                    if($url_memo_validacion) $cursoUpdate->documento_memo_validacion = $url_memo_validacion;
                    if($url_memo_actualizacion) $cursoUpdate->documento_memo_actualizacion = $url_memo_actualizacion;
                    if($url_carta_descriptiva) $cursoUpdate->file_carta_descriptiva = $url_carta_descriptiva;
                    $cursoUpdate->update();
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
        // try {
            //consulta sql
            $otrauni = FALSE;
            $area = new Area();
            $areas = $area->where('activo', true)->get();
            // $areas = $area->all();

            $Especialidad = new especialidad();
            $especialidades = $Especialidad->all();
            $unidades = new tbl_unidades();
            $unidadesMoviles = $unidades->SELECT('ubicacion')->GROUPBY('ubicacion')->ORDERBY('ubicacion','ASC')->GET();
            $criterioPago = new criterio_pago;
            $criterio_pago = $criterioPago->Where('id','!=','0')->Where('activo', TRUE)->GET();
            $servicios = ['CURSO'=>'CURSO','CERTIFACION'=>'CERTIFACION'];
            $idCurso = base64_decode($id);
            $curso = new curso();
            $cursos = $curso::SELECT('cursos.id','cursos.estado','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
                    'cursos.costo','cursos.duracion','cursos.tipo_curso','cursos.documento_memo_validacion','cursos.documento_memo_actualizacion','cursos.documento_solicitud_autorizacion',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.fecha_solicitud','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad', 'cursos.id_especialidad',
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.categoria', 'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
                    'cursos.rango_criterio_pago_minimo', 'rango_criterio_pago_maximo','cursos.observacion',
                    'cursos.grupo_vulnerable', 'cursos.dependencia','cursos.proyecto','cursos.motivo',
                    'cursos.servicio','cursos.file_carta_descriptiva','cursos.riesgo','cursos.curso_alfa')
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')->ORDERBY ('cursos.updated_at','DESC')
                    ->GET();

                   //dd($cursos[0]);

            $fechaSol = $curso->getMyDateFormat($cursos[0]->fecha_solicitud);
            $fechaVal = $curso->getMyDateFormat($cursos[0]->fecha_validacion);
            $fechaAct = $curso->getMyDateFormat($cursos[0]->fecha_actualizacion);
            $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo','ASC')->GET();
            $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
            $cadwell = $unidades->WHERE('ubicacion', '=', $cursos[0]->unidad_amovil)->FIRST();
            if($cadwell == NULL)
            {
                $otrauni = TRUE;
            }
            $gv = $cursos[0]->grupo_vulnerable;
            $dp = $cursos[0]->dependencia;
            // $dp = $cursos[0]->dependencia;

            // dd($gv);
            $categorias = $this->categorias;
            $perfil = $this->perfil;

            //informacion de carta descriptiva
            $carta_descriptiva = DB::Table('tbl_carta_descriptiva')->Where('id_curso',$cursos[0]->id)->Value('id');
            //Obtener la cantidad de horas capturadas del contenido tematico
            $duraCurso = DB::table('contenido_tematico')->where('id_curso', $cursos[0]->id)->where('id_parent', 0)
            ->select(DB::raw('SUM(EXTRACT(EPOCH FROM duracion::interval)) as total_seconds'))->first();
            $horas_tematico = (int)($duraCurso->total_seconds / 3600);
            return view('layouts.pages.frmedit_curso', compact('cursos', 'areas', 'especialidades','fechaSol', 'fechaVal', 'fechaAct', 'unidadesMoviles', 'criterio_pago','gruposvulnerables','otrauni','gv','dependencias','dp','servicios','categorias','perfil','carta_descriptiva', 'horas_tematico'));

        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

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
                    'cursos.costo','cursos.duracion','cursos.riesgo',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad','cursos.tipo_curso' ,
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.categoria',
                    'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
                    'cursos.rango_criterio_pago_minimo', 'cursos.rango_criterio_pago_maximo',
                    DB::raw("cursos.grupo_vulnerable::TEXT"),
                    DB::raw("cursos.dependencia::TEXT"),
                    DB::raw("CASE WHEN cursos.proyecto ='1' THEN 'SI' ELSE 'NO' END as proyecto"),
                    DB::raw("cursos.unidades_disponible::TEXT")
                    )
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')
                    ->FIRST();

            $cadwell = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')
                ->WHERE('id', '=', $curso->rango_criterio_pago_minimo)
                ->FIRST();
            $curso->rango_criterio_pago_minimo = $curso->rango_criterio_pago_minimo." - ".$cadwell->perfil_profesional;
            $cadwell = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')
                ->WHERE('id', '=', $curso->rango_criterio_pago_maximo)
                ->FIRST();
            $curso->rango_criterio_pago_maximo = $curso->rango_criterio_pago_maximo." - ".$cadwell->perfil_profesional;

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
        // dd($request);
        $cursos = new curso();
        // modificacion de un recurso guardado
        if (isset($id)) {
            $gv = [];
            $dp = [];
            if($request->unidad_accion_movil == '0') $uniamov = trim($request->unidad_ubicacion_especificar);
            else $uniamov = trim($request->unidad_accion_movil);

            $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo', 'ASC')->GET();
            $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
                if($request->a != NULL)
                {
                    foreach($gruposvulnerables as $cadwell)
                    {
                        foreach($request->a as $data)
                        {
                            if($cadwell->grupo == $data)
                            {
                                array_push($gv, $data);
                            }
                        }
                    }
                }

                if($request->b != NULL)
                {
                    foreach($dependencias as $cadwell)
                    {
                        foreach($request->b as $data)
                        {
                            if($cadwell->organismo == $data)
                            {
                                array_push($dp, $data);
                            }
                        }
                    }
                }


            # ==================================
            # Aquí modificamos el curso con id
            # ==================================
            $cursos = new curso();
            $curso = $cursos->WHERE('id', '=', $id)->GET();

            $url_solicitud_autorizacion = $url_memo_validacion = $url_memo_actualizacion = $url_carta_descriptiva = null;
            // validamos si hay archivos
            if ($request->hasFile('documento_solicitud_autorizacion')) {
                $documento_solicitud_autorizacion = $request->file('documento_solicitud_autorizacion'); # obtenemos el archivo
                $url_solicitud_autorizacion = $this->uploaded_file($documento_solicitud_autorizacion, $id, 'documento_solicitud_autorizacion_update', $curso[0]->documento_solicitud_autorizacion); #invocamos el método
            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_validacion')) {
                $documento_memo_validacion = $request->file('documento_memo_validacion'); # obtenemos el archivo
                $url_memo_validacion = $this->uploaded_file($documento_memo_validacion, $id, 'documento_memo_validacion_update',$curso[0]->documento_memo_validacion); #invocamos el método

            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_actualizacion')) {
                $documento_memo_actualizacion = $request->file('documento_memo_actualizacion'); # obtenemos el archivo
                $url_memo_actualizacion = $this->uploaded_file($documento_memo_actualizacion, $id, 'documento_memo_actualizacion_update',$curso[0]->documento_memo_actualizacion); #invocamos el método
            }

            if ($request->hasFile('file_carta_descriptiva')) {
                $file_carta_descriptiva = $request->file('file_carta_descriptiva');
                $url_carta_descriptiva = $this->uploaded_file($file_carta_descriptiva, $id, 'carta_descriptiva',$curso[0]->file_carta_descriptiva);
            }

            if($request->proyecto==true) $proyecto = true;
            else $proyecto =false;

            if($request->estado==1) $estado = true;
            elseif($request->estado==2) $estado = false;
            else $estado = null;

            if($request->curso_alfa=='si') $curso_alfa = true;
            else $curso_alfa = false;

            $array = [
                'nombre_curso' => trim($request->nombrecurso),
                'modalidad' => trim($request->modalidad),
                'horas' => trim($request->duracion),
                'clasificacion' => trim($request->clasificacion),
                'costo' => trim($request->costo),
                'objetivo' => trim($request->objetivo),
                'perfil' => trim($request->perfil),
                'fecha_solicitud' => $cursos->setFechaAttribute($request->fecha_solicitud),
                'fecha_validacion' => $cursos->setFechaAttribute($request->fecha_validacion),
                'fecha_actualizacion' => $cursos->setFechaAttribute($request->fecha_actualizacion),
                'descripcion' => trim($request->descripcionCurso),
                'no_convenio' => trim($request->no_convenio),
                'id_especialidad' => trim($request->especialidadCurso),
                'unidad_amovil' => $uniamov,
                'area' => $request->areaCursos,
                'solicitud_autorizacion' => trim($request->solicitud_autorizacion),
                'memo_actualizacion' => trim($request->memo_actualizacion),
                'memo_validacion' => trim($request->memo_validacion),
                'cambios_especialidad' => trim($request->cambios_especialidad),
                'categoria' => trim($request->categoria),
                'tipo_curso' => trim($request->tipo_curso),
                'rango_criterio_pago_minimo' => trim($request->criterio_pago_minimo_edit),
                'rango_criterio_pago_maximo' => trim($request->criterio_pago_maximo_edit),
                'grupo_vulnerable' => $gv,
                'dependencia' => $dp,
                'proyecto' => $proyecto,
                'riesgo' => (isset($request->curso_riesgo)) ? $request->curso_riesgo : false,
                'estado' => $estado,
                'servicio' => json_encode($request->servicio),
                'motivo' => trim($request->motivo),
                'updated_at' =>date('Y-m-d h:m:s'),
                'iduser_updated' => Auth::user()->id,
                'curso_alfa' => $curso_alfa

            ];
            if($url_solicitud_autorizacion!=NULL) $array += ['documento_solicitud_autorizacion' => $url_solicitud_autorizacion];
            if($url_memo_validacion!=NULL)$array += ['documento_memo_validacion' => $url_memo_validacion];
            if($url_memo_actualizacion!=NULL)$array += ['documento_memo_actualizacion' => $url_memo_actualizacion];
            if($url_carta_descriptiva!=NULL)$array += ['file_carta_descriptiva' => $url_carta_descriptiva];

            $cursos->WHERE('id', '=', $id)->UPDATE($array);

            //var_dump($array);exit;
            $nombreCurso = $request->nombrecurso;
            return redirect()->route('curso-inicio')
                    ->with('success', sprintf('CURSO:  " %s "  .- ACTUALIZACIÓN EXITOSA!!', $nombreCurso));
        }

    }

    public function alta_baja($id)
    {
        $av = curso::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        // dd($av);
        if($av->unidades_disponible == NULL || $av->unidades_disponible == '[]')
        {
            $reform = curso::find($id);

            $unidades = DB::Table('tbl_unidades')->OrderBy('unidad','ASC')->Get()->Pluck('unidad')->ToArray();

            $reform->unidades_disponible = $unidades;
            $reform->save();

            $av = curso::SELECT('unidades_disponible')->WHERE('id', '=', $id)->FIRST();
        }
        $unidades = DB::Table('tbl_unidades')->OrderBy('unidad','ASC')->Get()->Pluck('unidad')->ToArray();
        $available = $av->unidades_disponible;

        return view('layouts.pages.vstaltabajacur', compact('id','available','unidades'));
    }

    public function exportar_cursos($xls)
    {
        $fecha = date("dmy");
        switch($xls){
            case 'ACTIVOS':
                $nombreLayout = "Catalogo de Cursos Activos ".$fecha.".xlsx";
                return (new xlsCursosMultiple(1))->download($nombreLayout);
            break;
            case 'CURSOS'|| 'CERTIFICACION' || 'PROGRAMA':
                $file_name = ['CURSOS'=>'CURSOS', 'CERTIFICACION'=>'CERTIFICACIÓN', 'PROGRAMA'=>'PROGRAMA ESTRATÉGICO'];
                $nombreLayout = "CATALOGO DE ".$file_name[$xls]."_".$fecha.'.xlsx';
                return (new xlsCursosDV($xls))->download($nombreLayout);
            break;
        }
    }

    public function alta_baja_save(Request $request)
    {

        $unidades_lista = DB::Table('tbl_unidades')->OrderBy('unidad','ASC')->Get()->Pluck('unidad')->ToArray();
        $unidades = [];

        foreach($unidades_lista as $unidad) {
            if($this->checkComparator($request->{'chk_'.str_replace(' ', '_', $unidad)}) == TRUE)
            {
                array_push($unidades, $unidad);
            }
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

    protected function uploaded_file($file, $id, $name, $name_old=null){
        $ext = $file->getClientOriginalExtension(); // extension de la imagen
        $ext = strtolower($ext);
        $url = $mgs= null;
        if($ext == "pdf"){
            if ($name_old) {
                if (Storage::exists($name_old)) Storage::delete($name_old);
            }
            $fecha = date("ymdhms");
            $path_pdf = "/uploadFiles/cursos/".$id."/";

            $file_name = trim($name."_".$fecha."_".$id.".pdf");
            $path_file = $path_pdf.$file_name;

            $file->storeAs($path_pdf, $file_name);
            $msg = "El archivo ha sido cargado o reemplazado correctamente.";
        }else $msg= "Formato de Archivo no válido, sólo PDF.";

        $data_file = ["message"=>$msg, 'url_file'=>$path_file];

        return $path_file;
    }

    public function exportar_cursos_all()
    {
        $data = curso::SELECT('cursos.id','area.formacion_profesional','cursos.categoria','dependencia',
                        'grupo_vulnerable', 'especialidades.nombre as especialidad','cursos.nombre_curso',
                        'cursos.horas','cursos.objetivo','cursos.perfil',
                        DB::raw("(case when cursos.riesgo = 'true' then 'SI' else 'NO' end) as etnia"),
                        'cursos.fecha_validacion','cursos.memo_validacion','cursos.unidad_amovil',
                        'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.tipo_curso',
                        'cursos.modalidad','cursos.clasificacion','observacion','cursos.costo',
                        'cursos.rango_criterio_pago_minimo',
                        DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_minimo) as mini"),
                        'cursos.rango_criterio_pago_maximo',
                        DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_maximo) as maxi"),
                        'cursos.servicio',
                        DB::raw("(case when cursos.proyecto = 'true' then 'SI' else 'NO' end) as proyecto"),
                        DB::raw("(case when cursos.estado = true then 'ACTIVO' else case when cursos.estado = 'false' then 'INACTIVO' else 'BAJA' end end) as status"))
                        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
                        ->LEFTJOIN('area', 'area.id', '=', 'especialidades.id_areas')
                        ->ORDERBY('especialidades.nombre', 'ASC')
                        ->ORDERBY('cursos.nombre_curso', 'ASC')
                        ->GET();
                        //dd($data[0]);

        $cabecera = [
            'ID','CAMPO','CATEGORIA','DEPENDENCIA','GRUPO VULNERABLE','ESPECIALIDAD','NOMBRE','HORAS','OBJETIVO',
            'PERFIL DE INGRESO DEL ALUMNO','NIVEL ESTUDIO INSTRUCTOR','SOLICITUD DE AUTORIZACION','FECHA DE VALIDACION','MEMO DE VALIDACION',
            'UNIDAD MOVIL','MEMO DE ACTUALIZACION','FECHA DE ACTUALIZACION','TIPO CAPACITACION','MODALIDAD','CLASIFICACION',
            'OBSERVACION','COSTO','CRITERIO DE PAGO MINIMO','NOMBRE CRITERIO MINIMO','CRITERIO DE PAGO MAXIMO',
            'NOMBRE DE CRITERIO MAXIMO','CURSO/CERTIFICACION', 'PROYECTO','ESTATUS'
        ];
        $nombreLayout = "Catalogo de cursos completo.xlsx";
        $titulo = "Catalogo de cursos completo";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }

    ## By Jose Luis / FUNCION GUARDAR DATOS DE LA CARTA DESCRIPTIVA
    public function carta_descriptiva($id, $parte)
    {
        $idCurso = base64_decode($id);
        $tparte = $parte;
        $json_general = $json_tematico = $json_didactico = [];

        ##Año de ejercicio
        $bdEjercicio = DB::table('tbl_instituto')->select('fini', 'ffin')->first();
        $ejercicio = '';
        if ($bdEjercicio) {
            $date1 = Carbon::createFromFormat('d-M', $bdEjercicio->fini);
            $date2 = Carbon::createFromFormat('d-M', $bdEjercicio->ffin);
            $fActual = Carbon::now();
            if ($fActual->lessThan($date1)) {$ejercicio = ($fActual->year - 1) . "-" . $fActual->year;
            }else if($fActual->greaterThan($date2)) {$ejercicio = $fActual->year . "-" . ($fActual->year + 1);}
        }



        $curso = DB::Table('cursos as cu')->SELECT('cu.id','cu.nombre_curso','cu.modalidad','cu.horas', 'cu.duracion','cu.tipo_curso',
                    'especialidades.nombre AS especialidad', 'cu.id_especialidad', 'area.formacion_profesional')
                    ->WHERE('cu.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cu.id_especialidad')
                    ->LEFTJOIN('area', 'area.id', '=' , 'cu.area')
                    ->FIRST();

        $datos_carta = DB::table('tbl_carta_descriptiva')->select('id', 'datos_generales', 'cont_tematico', 'rec_didacticos')
        ->where('id_curso', '=', $idCurso)->first();

        if(isset($datos_carta->datos_generales)){$json_general = json_decode($datos_carta->datos_generales, true);}
        if(isset($datos_carta->cont_tematico)){$json_tematico = json_decode($datos_carta->cont_tematico, true);}
        if(isset($datos_carta->rec_didacticos)){$json_didactico = json_decode($datos_carta->rec_didacticos, true);}

        ##Obtenemos datos de la tabla de contenido_tematico

        $modulo_first = DB::table('contenido_tematico')->select('id', 'id_parent', 'id_curso', 'numeracion', 'nombre_modulo', 'nivel', 'duracion',
        'sincrona', 'asincrona', 'estra_didac', 'process_eval')->where('id_curso', $idCurso)->where('id_parent', 0)->orderBy('id', 'asc')->get();

        //Obtenemos los ids de los modulos padre
        $ids_modulos = $res_tematico = [];
        if($modulo_first) $ids_modulos = $modulo_first->pluck('id')->toArray();

        if(count($ids_modulos) > 0){
            foreach ($ids_modulos as $key => $value) {
                $data_tematico = DB::select("
                WITH RECURSIVE cte AS (
                    SELECT id, id_parent, numeracion, nombre_modulo
                    FROM contenido_tematico
                    WHERE id_parent = :parentId
                    UNION ALL
                    SELECT t.id, t.id_parent, t.numeracion, t.nombre_modulo
                    FROM contenido_tematico t
                    JOIN cte ON t.id_parent = cte.id
                )
                SELECT * FROM cte ORDER BY id", ['parentId' => $value]);

                $res_tematico[] = $data_tematico;
            }
        }

        ## Obtenemos la sumatoria de horas de los modulos registrados
        $sumaHorasMod = DB::table('contenido_tematico')
            ->where('id_curso', $idCurso)->where('id_parent', 0)
            ->select(DB::raw("
                SUM(
                    EXTRACT(EPOCH FROM CAST(duracion AS time))
                ) as total_seconds
            "))
            ->value('total_seconds');
        $tFormatHour = '';
        if($sumaHorasMod){
            $tHoras = floor($sumaHorasMod / 3600);
            $tMinutos = floor(($sumaHorasMod % 3600) / 60);
            $tFormatHour = sprintf('%02d:%02d', $tHoras, $tMinutos). ' ' . ($tHoras > 0 ? 'Horas' : 'Minutos');
        }




        return view('layouts.pages.frm_cartadescrip', compact('idCurso', 'tparte', 'curso', 'json_general', 'res_tematico', 'modulo_first', 'json_didactico', 'tFormatHour','ejercicio'));
    }


    public function edit_cartadescrip(Request $request)
    {
        $id_curso = $request->input('id_curso');
        $indice = $request->input('indice');
        $accion = $request->input('accion');

        $jsonBActual = DB::table('tbl_carta_descriptiva')->where('id_curso', $id_curso)->value('cont_tematico');
        $arrayObjetos = json_decode($jsonBActual, true);

        if($accion == 'eliminar'){
            try {
                if(!empty($indice)){
                    DB::table('contenido_tematico')->where('id', $indice)->orWhere('id_parent', $indice)->delete();
                    return response()->json(['status' => 200, 'mensaje' => '¡Registro eliminado!', 'accion' => $accion]);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 500, 'mensaje' => $th->getMessage()]);
            }

        }else if($accion == 'editar'){
            $name_modulo = DB::table('contenido_tematico')->select('id', 'id_parent', 'id_curso', 'nombre_modulo',
            DB::raw("EXTRACT(HOUR FROM duracion::time) as hr_dura"),
            DB::raw("EXTRACT(MINUTE FROM duracion::time) as min_dura"),
            DB::raw("EXTRACT(HOUR FROM sincrona::time) as hr_sinc"),
            DB::raw("EXTRACT(MINUTE FROM sincrona::time) as min_sinc"),
            DB::raw("EXTRACT(HOUR FROM asincrona::time) as hr_asin"),
            DB::raw("EXTRACT(MINUTE FROM asincrona::time) as min_asin"),
            'estra_didac', 'process_eval')->where('id_curso', $id_curso)->where('id', $indice)->first();

            $submodulos = DB::select("
                WITH RECURSIVE cte AS (
                    SELECT id, id_parent, numeracion, nombre_modulo
                    FROM contenido_tematico
                    WHERE id_parent = :parentId
                    UNION ALL
                    SELECT t.id, t.id_parent, t.numeracion, t.nombre_modulo
                    FROM contenido_tematico t
                    JOIN cte ON t.id_parent = cte.id
                )
                SELECT * FROM cte ORDER BY id", ['parentId' => $indice]);
            if($submodulos === null) $submodulos = ['id' => '1'];


            return response()->json(['status' => 200, 'mensaje' => 'Carga de datos del registro', 'accion' => $accion, 'datos_uno' => $name_modulo, 'datos_dos' => $submodulos, 'indice' => $indice]);
        }

    }

    //por post
    public function save_parte_uno(Request $request)
    {
        // dd($request->input('id_curso'));
        $data = [];
        $mensaje = "";
        $id_curso = $request->input('id_curso');
        if($id_curso == null || $id_curso == '') return "No se encontró el id del curso";

        $data = [
            // "entidad" => $request->input('entidad'),
            // "tipocap" => $request->input('tipocap'),
            // "ciclo_esc" => $request->input('ciclo_esc'),
            // "duracion" => $request->input('duracion'),
            // "form_profesion" => $request->input('form_profesion'),
            // "modalidad" => $request->input('modalidad'),
            // "especialidad" => $request->input('especialidad'),
            // "curso" => $request->input('curso'),
            "pogrm_estra" => $request->input('pogrm_estra'),
            "perfil_instruc" => $request->input('perfil_instruc'),
            "aprendizaje_esp" => $request->input('aprendizaje_esp'),
            "obj_especificos" => $request->input('obj_especificos'),
            "transversalidad" => $request->input('transversalidad'),
            "dirigido" => $request->input('dirigido'),
            "proces_evalua" => $request->input('proces_evalua'),
            "observaciones" => $request->input('observaciones')
        ];

        if(count($data) > 0){
            try {
                $result = DB::table('tbl_carta_descriptiva')
                ->UpdateOrInsert(
                    ['id_curso'=>$id_curso],
                    ['id_curso'=>$id_curso, 'datos_generales' => json_encode($data),'iduser_created'=> Auth::user()->id]);

                if ($result) {
                    $mensaje = "Datos guardados con exito";
                    return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'general'])->with('message', $mensaje);
                }
            } catch (\Throwable $th) {
                $mensaje = "Error: ".$th->getMessage();
                return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'general'])->with('message', $mensaje);
            }
        }else{
            $mensaje = "No contiene datos para guardar";
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'general'])->with('message', $mensaje);
        }

        // cursos-catalogo.cartadescriptiva
    }


    public function save_parte_tres(Request $request)
    {
        // dd($request->input('id_curso'));
        $data = [];
        $mensaje = "";
        $id_curso = $request->input('id_curso3');
        if($id_curso == null || $id_curso == '') return "No se encontró el id del curso";

        $data = [
            "elem_apoyo" => $request->input('elem_apoyo'),
            "auxiliares_ense" => $request->input('auxiliares_ense'),
            "referencias" => $request->input('referencias')
        ];

        if(count($data) > 0){
            try {
                $result = DB::table('tbl_carta_descriptiva')
                ->UpdateOrInsert(
                    ['id_curso'=>$id_curso],
                    ['id_curso'=>$id_curso, 'rec_didacticos' => json_encode($data),'iduser_created'=> Auth::user()->id]);

                if ($result) {
                    $mensaje = "Datos guardados con exito";
                    return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'didactico'])->with('message', $mensaje);
                }
            } catch (\Throwable $th) {
                $mensaje = "Error: ".$th->getMessage();
                return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'didactico'])->with('message', $mensaje);
            }
        }else{
            $mensaje = "No contiene datos para guardar";
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'didactico'])->with('message', $mensaje);
        }

        // cursos-catalogo.cartadescriptiva
    }


    public function save_parte_dos(Request $request)
    {
        ## Crear array separandolos por los enter
        $name_modulo = $request->input('name_modulo');
        $cadena_submodulos = $request->input('submodulos');
        $curso_hora = $request->input('curso_hora');
        $curso_min = $request->input('curso_minuto');
        $sincro_hora = $request->input('hora_sincro');
        $sincro_min = $request->input('minuto_sincro');
        $asinc_hora = $request->input('hora_asincro');
        $asinc_min = $request->input('minuto_asincro');
        $estra_didac = $request->input('estra_dida');
        $proces_eval = $request->input('proceso_evalua');
        $id_curso = $request->input('id_curso2');
        $update = false; $ids_updSub = []; $ids_forDelete = []; $id_modupd = null;
        //De actualización
        if(!empty($request->input('ids_subs')) || !empty($request->input('id_modupd'))){
            $update = true;
            $ids_updSub = json_decode($request->input('ids_subs'), true);
            $id_modupd = $request->input('id_modupd');
        }

        # Separamos textos con los retornos de carro a la cadena de sumbomulos
        $datosOrganizados = $data_sub = [];
        if(!empty($cadena_submodulos)){
            $data_sub = explode("\r\n", $cadena_submodulos);
            try {
                foreach ($data_sub as $item) {
                    // Extraer la parte numérica del título
                    preg_match('/^(\d+(\.\d+)* )(.+)$/', $item, $matches);

                    if (!empty($matches)) {
                        $numericPart = trim($matches[1]);
                        $textPart = trim($matches[3]);

                        // Determinar el nivel basado en la cantidad de puntos
                        $level = substr_count($numericPart, '.') + 1;

                        // Almacenar el título junto con su nivel
                        $datosOrganizados[] = [
                            'numeracion' => $numericPart,
                            'texto' => $textPart,
                            'level' => $level,
                            'id_sub' => null
                        ];
                    }
                }
            } catch (\Throwable $th) {
                return 'Error: '.$th->getMessage();
            }
        }

        //Validamos si es update para organizar el array
        if ($update) {
            //Si el usuario solo cambia texto
            $new_organizar = [];
            if(count($datosOrganizados) === count($ids_updSub)){
                foreach ($datosOrganizados as $key => $dato) {
                    $new_organizar[] = [
                        'numeracion' => $dato['numeracion'],
                        'texto' => $dato['texto'],
                        'level' => $dato['level'],
                        'id_sub' => $ids_updSub[$key]
                    ];
                }
            //Si el usuario agrega mas submodulos
            }else if(count($datosOrganizados) > count($ids_updSub)){
                foreach ($datosOrganizados as $key => $dato) {
                    $id_temp = null;
                    if($key < count($ids_updSub)) $id_temp = $ids_updSub[$key];
                    $new_organizar[] = [
                        'numeracion' => $dato['numeracion'],
                        'texto' => $dato['texto'],
                        'level' => $dato['level'],
                        'id_sub' => $id_temp
                    ];
                }
            //Si el usuario quita algunos submodulos
            }else if(count($ids_updSub) > count($datosOrganizados)){
                $ids_forDelete = [];
                foreach ($ids_updSub as $key => $id) {
                    if($key < count($datosOrganizados)) {
                        $new_organizar[] = [
                            'numeracion' => $datosOrganizados[$key]['numeracion'],
                            'texto' => $datosOrganizados[$key]['texto'],
                            'level' => $datosOrganizados[$key]['level'],
                            'id_sub' => $id
                        ];
                    }else{
                        $ids_forDelete [] = $id;
                    }
                }
            }
            $datosOrganizados = $new_organizar;
        }

        ##Validar si la entrada de datos coincide con el resultado ya procesado de los submodulos
        if(count($datosOrganizados) !== count($data_sub)){
            $mensaje = "Verifica que la numeración de los submódulos esté bien escrita.  (Ejemplo: 1.1 Electrónica)";
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'tematico'])->with('message', $mensaje);
        }

        //Validar nombre del modulo
        if(empty($name_modulo)){
            $mensaje = "Para seguir con el proceso, debe ingresar el titulo del modulo.";
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'tematico'])->with('message', $mensaje);
        }

        //Validar duración por modulo
        $duracion_mod = $sincrona_mod = $asincrona_mod = '00:00';
        if($curso_hora !== null && $curso_min !== null &&
        $sincro_hora !== null && $sincro_min !== null &&
        $asinc_hora !== null && $asinc_min !== null){
            //Agregamos a las cadenad de horas, min ceros a la izquierda en caso de que se requiera
            $curso_hora = str_pad($curso_hora, 2, '0', STR_PAD_LEFT);
            $curso_min = str_pad($curso_min, 2, '0', STR_PAD_LEFT);
            $sincro_hora = str_pad($sincro_hora, 2, '0', STR_PAD_LEFT);
            $sincro_min = str_pad($sincro_min, 2, '0', STR_PAD_LEFT);
            $asinc_hora = str_pad($asinc_hora, 2, '0', STR_PAD_LEFT);
            $asinc_min = str_pad($asinc_min, 2, '0', STR_PAD_LEFT);

            $duracion_mod = $curso_hora.':'.$curso_min;
            $sincrona_mod = $sincro_hora.':'.$sincro_min;
            $asincrona_mod = $asinc_hora.':'.$asinc_min;
        }else{
            $mensaje = "Falta datos en los campo de duracion de horas y minutos";
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'tematico'])->with('message', $mensaje);
        }

        ## Datos del modulo
        $datos_modulo = ['id_parent' => 0, 'id_curso'=> $id_curso, 'nombre_modulo'=>$name_modulo, 'nivel'=> 1, 'duracion'=> $duracion_mod,
        'sincrona'=> $sincrona_mod, 'asincrona'=> $asincrona_mod, 'estra_didac'=> $estra_didac, 'process_eval' => $proces_eval, 'iduser_created'=> Auth::user()->id];
        if($datosOrganizados != [] && $datosOrganizados[0]['numeracion'][0] != 0) {
            $datos_modulo['numeracion'] = $datosOrganizados[0]['numeracion'][0];
        } else {
            $datos_modulo['numeracion'] = 0;
        }

        //Nuevo codigo de inserción
        try {
            ##insertamos el modulo
            $id_modulo_req = $id_modupd;
            $id_modulo = $this->insertUpdModulo($id_modulo_req, $datos_modulo);

             if (!empty($id_modulo)){  //Insertamos los submodulos
                $ids_subs = [];
                foreach ($datosOrganizados as $key => $dato) {
                    if($dato['level'] == 2){
                        $idModOrSub = $this->insertUpdSubModulo($id_modulo, $id_curso, $dato);
                        $ids_subs[$dato['numeracion']] = $idModOrSub;

                    }else if($dato['level'] == 3){
                        //Cortamos la numeracion para ubicar su submodulo
                        $cut_number = $this->foundNumber($dato['numeracion'], 2);
                        $idModOrSub = $this->insertUpdSubModulo($ids_subs[$cut_number], $id_curso, $dato);
                        $ids_subs[$dato['numeracion']] = $idModOrSub;

                    }else if($dato['level'] == 4){
                        //Cortamos la numeracion para ubicar su submodulo
                        $cut_number = $this->foundNumber($dato['numeracion'], 3);
                        $idModOrSub = $this->insertUpdSubModulo($ids_subs[$cut_number], $id_curso, $dato);
                        $ids_subs[$dato['numeracion']] = $idModOrSub;
                    }
                }
                $mensaje = "Datos guardados con exito";
                //si es actualizacion hacemos la eliminacion en caso de que se requiera
                if($update){
                    if(count($ids_forDelete) > 0){
                        foreach ($ids_forDelete as $key => $id) {
                            DB::table('contenido_tematico')->where('id', $id)->delete();
                        }
                    }
                    $mensaje = "Datos actualizados con exito";
                }
                return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'tematico'])->with('message', $mensaje);
            }
        } catch (\Throwable $th) {
            return redirect()->route('cursos-catalogo.cartadescriptiva', ['id' => base64_encode($id_curso), 'parte' => 'tematico'])->with('message', 'Error en la estructura de numeración: '.$th->getMessage());
        }

    }

    public function carta_descriptiva_pdf($id) {

        $carta_descriptiva = DB::Table('tbl_carta_descriptiva')->Where('id_curso',$id)->First();
        $carta_descriptiva->datos_generales = json_decode($carta_descriptiva->datos_generales);
        $carta_descriptiva->rec_didacticos = json_decode($carta_descriptiva->rec_didacticos);

        $contenido_tematico = DB::Table('contenido_tematico')->Where('id_curso',$id)->OrderBy('id','ASC')->Get();

        $data_curso = DB::Table('cursos')->Select('modalidad','tipo_curso','nombre_curso','horas','e.nombre AS especialidad','a.formacion_profesional AS area')
            ->Join('especialidades AS e','e.id','cursos.id_especialidad')
            ->Join('area AS a','a.id','cursos.area')
            ->Where('cursos.id',$id)
            ->First();

        $bdEjercicio = DB::Table('tbl_instituto')->Select('fini','ffin')->First();
        $ejercicio = '';
        if($bdEjercicio) {
            $date1 = Carbon::createFromFormat('d-M', $bdEjercicio->fini);
            $date2 = Carbon::createFromFormat('d-M', $bdEjercicio->ffin);
            $fActual = Carbon::now();
            if($fActual->lessThan($date1)) {
                $ejercicio = ($fActual->year - 1) . "-" . $fActual->year;
            } else if($fActual->greaterThan($date2)) {
                $ejercicio = $fActual->year . "-" . ($fActual->year + 1);
            }
        }

        $pdf = PDF::loadView('layouts.pdfpages.cartaDescriptiva',compact('carta_descriptiva','contenido_tematico','data_curso','ejercicio'));
        $pdf->setPaper('letter', 'Landscape');
        return  $pdf->stream('medium.pdf');
    }

    public function dividirHtml($html, $particion = 2) {
        // Crear un objeto DOMDocument para parsear el HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true); // Para ignorar errores en el HTML mal formado
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        // Obtener todos los elementos de la página
        $body = $dom->getElementsByTagName('body')->item(0);
        $elementos = iterator_to_array($body->childNodes);

        // Determinar el punto de corte
        $mitad = ceil(count($elementos) / $particion);

        // Dividir los elementos en dos partes
        $parte1 = array_slice($elementos, 0, $mitad);
        $parte2 = array_slice($elementos, $mitad);

        // Convertir ambas partes de nuevo en HTML
        $parte1_html = '';
        foreach ($parte1 as $node) {
            $parte1_html .= $dom->saveHTML($node);
        }

        $parte2_html = '';
        foreach ($parte2 as $node) {
            $parte2_html .= $dom->saveHTML($node);
        }

        return [$parte1_html, $parte2_html];
    }


    function insertUpdSubModulo($id_modOrsub, $id_curso, $datos)
    {
        $registro = null;
        if(!empty($datos['id_sub'])){
            $registro = DB::table('contenido_tematico')
            ->select('id')
            ->where('id_curso', $id_curso)
            ->where('id', $datos['id_sub'])
            ->first();
        }

        if ($registro) {
            // Actualizar el registro existente
            DB::table('contenido_tematico')
            ->where('id', $registro->id)
            ->update([
                'id_parent' => $id_modOrsub,
                'id_curso' => $id_curso,
                'numeracion' => $datos['numeracion'],
                'nombre_modulo' => $datos['texto'],
                'nivel' => $datos['level'],
                'iduser_updated' => Auth::user()->id
            ]);

            return $registro->id;
        } else {
            // Insertar un nuevo registro
            $newId = DB::table('contenido_tematico')
                ->insertGetId([
                    'id_parent' => $id_modOrsub,
                    'id_curso' => $id_curso,
                    'numeracion' => $datos['numeracion'],
                    'nombre_modulo' => $datos['texto'],
                    'nivel' => $datos['level'],
                    'iduser_created' => Auth::user()->id
                ]);
            return $newId;
        }
    }


    function insertUpdModulo($id_modulo, $datos)
    {
        $registro = null;
        if(!empty($id_modulo)){
            $registro = DB::table('contenido_tematico')
            ->select('id')
            ->where('id_curso', $datos['id_curso'])
            ->where('id', $id_modulo)
            ->first();
        }

        if ($registro) {
            // Actualizar el registro existente
            DB::table('contenido_tematico')
            ->where('id', $registro->id)
            ->update([
                'id_parent' => 0,
                'id_curso'=> $datos['id_curso'],
                'numeracion'=> $datos['numeracion'],
                'nombre_modulo' => $datos['nombre_modulo'],
                'nivel' => $datos['nivel'],
                'duracion'=> $datos['duracion'],
                'sincrona'=> $datos['sincrona'],
                'asincrona'=> $datos['asincrona'],
                'estra_didac'=> $datos['estra_didac'],
                'process_eval' => $datos['process_eval'],
                'iduser_updated'=> $datos['iduser_created']
            ]);
            return $registro->id;
        } else {
            // Insertar un nuevo registro
            $newId = DB::table('contenido_tematico')
                    ->insertGetId([
                        'id_parent' => 0,
                        'id_curso'=> $datos['id_curso'],
                        'numeracion'=> $datos['numeracion'],
                        'nombre_modulo' => $datos['nombre_modulo'],
                        'nivel' => $datos['nivel'],
                        'duracion'=> $datos['duracion'],
                        'sincrona'=> $datos['sincrona'],
                        'asincrona'=> $datos['asincrona'],
                        'estra_didac'=> $datos['estra_didac'],
                        'process_eval' => $datos['process_eval'],
                        'iduser_created'=> $datos['iduser_created']
                    ]);
            return $newId;
        }
    }

    function foundNumber($numeracion, $level)
    {
        // Dividir la cadena en partes usando el punto como separador
        $parts = explode('.', $numeracion);

        // Verificar si el nivel deseado es válido
        if ($level > 0 && $level <= count($parts)) {
            // Unir las partes hasta el nivel deseado para obtener el resultado
            $result = implode('.', array_slice($parts, 0, $level));
            return $result;
        } else {
            // Nivel no válido, devolver la cadena original
            return $numeracion;
        }
    }

}
