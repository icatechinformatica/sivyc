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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport;

class CursosController extends Controller
{

    protected function prueba2()
    {
        $data = curso::SELECT('id','unidades_disponible')->GET();
        $insdata = instructor::SELECT('id','unidades_disponible')->GET();
        foreach($data as $cadwell)
        {
            $newlist = [];
            $newlistins = [];
            foreach($cadwell->unidades_disponible as $key=>$lista)
            {
                if($lista == 'TILA')
                {
                    array_push($newlist, 'SALTO DE AGUA');
                }
                else
                {
                    array_push($newlist, $lista);
                }
            }
            $cursoUpdate = curso::find($cadwell->id);
            $cursoUpdate->unidades_disponible = $newlist;
            $cursoUpdate->save();

        }
        foreach($insdata as $inscadwell)
        {
            $newlistins = [];
            if($inscadwell->unidades_disponible != null)
            {
                foreach($inscadwell->unidades_disponible as $keyins=>$listains)
                {
                    if($listains == 'TILA')
                    {
                        array_push($newlistins, 'SALTO DE AGUA');
                    }
                    else
                    {
                        array_push($newlistins, $listains);
                    }
                }
                $insUpdate = instructor::find($inscadwell->id);
                $insUpdate->unidades_disponible = $newlist;
                $insUpdate->save();
            }

        }
        dd('listo');
    }

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
        $data = curso::searchporcurso($tipoCurso, $buscar_curso)->WHERE('cursos.id', '!=', '0');
        if($roles[0]->role_name != 'admin' && $roles[0]->role_name != 'auxiliar_paqueteias-todos' && $roles[0]->role_name != 'titular-innovacion'){
            $data = $data->WHERE('cursos.estado', '=', true);
        }
        $data = $data->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->PAGINATE(25, ['cursos.id', 'cursos.nombre_curso', 'cursos.modalidad', 'cursos.horas', 'cursos.clasificacion',
                       'cursos.costo', 'cursos.objetivo', 'cursos.perfil', 'cursos.solicitud_autorizacion',
                       'cursos.fecha_validacion', 'cursos.memo_validacion', 'cursos.memo_actualizacion',
                       'cursos.fecha_actualizacion', 'cursos.unidad_amovil', 'especialidades.nombre',
                       'cursos.tipo_curso', 'cursos.rango_criterio_pago_minimo', 'cursos.rango_criterio_pago_maximo',
                       DB::raw("CASE WHEN cursos.estado ='true' THEN 'ACTIVO' ELSE (CASE WHEN cursos.estado='false' THEN  'INACTIVO' ELSE 'BAJA' END) END as estado"),
                       'cursos.servicio','cursos.proyecto','cursos.file_carta_descriptiva']);
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
        $cp = $criterioPago->all();
        $area = new Area();
        $areas = $area->all();
        $gruposvulnerables = DB::table('grupos_vulnerables')->SELECT('id','grupo')->ORDERBY('grupo','ASC')->GET();
        $dependencias = DB::table('organismos_publicos')->SELECT('id','organismo')->ORDERBY('organismo','ASC')->GET();
        // mostramos el formulario de cursos
        return view('layouts.pages.frmcursos', compact('especialidades', 'areas', 'unidadesMoviles', 'cp', 'gruposvulnerables','dependencias'));
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
                
                if($request->estado==1) $estado = true;
                elseif($request->estado==2) $estado = false;
                else $estado = null;

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
                $cursos->nivel_estudio = trim($request->nivel_estudio);
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
                $cursos->estado = $estado;
                $cursos->servicio = json_encode($request->servicio);
                $cursos->motivo = trim($request->motivo);                
                $cursos->iduser_created = Auth::user()->id;

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
            $areas = $area->all();

            $Especialidad = new especialidad();
            $especialidades = $Especialidad->all();
            $unidades = new tbl_unidades();
            $unidadesMoviles = $unidades->SELECT('ubicacion')->GROUPBY('ubicacion')->ORDERBY('ubicacion','ASC')->GET();
            $criterioPago = new criterio_pago;
            $criterio_pago = $criterioPago->all();
            $servicios = ['CURSO'=>'CURSO','CERTIFACION'=>'CERTIFACION'];
            $idCurso = base64_decode($id);
            $curso = new curso();
            $cursos = $curso::SELECT('cursos.id','cursos.estado','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
                    'cursos.costo','cursos.duracion','cursos.tipo_curso','cursos.documento_memo_validacion','cursos.documento_memo_actualizacion','cursos.documento_solicitud_autorizacion',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad', 'cursos.id_especialidad',
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria', 'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
                    'cursos.rango_criterio_pago_minimo', 'rango_criterio_pago_maximo','cursos.observacion',
                    'cursos.grupo_vulnerable', 'cursos.dependencia','cursos.proyecto','cursos.motivo',
                    'cursos.servicio','cursos.file_carta_descriptiva')
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')->ORDERBY ('cursos.updated_at','DESC')
                    ->GET();

                   //dd($cursos[0]);

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
            return view('layouts.pages.frmedit_curso', compact('cursos', 'areas', 'especialidades', 'fechaVal', 'fechaAct', 'unidadesMoviles', 'criterio_pago','gruposvulnerables','otrauni','gv','dependencias','dp','servicios'));

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
                    'cursos.costo','cursos.duracion',
                    'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
                    'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
                    'especialidades.nombre AS especialidad','cursos.tipo_curso' ,
                    'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria',
                    'cursos.documento_memo_validacion',
                    'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
                    'cursos.rango_criterio_pago_minimo', 'cursos.rango_criterio_pago_maximo',
                    'cursos.grupo_vulnerable','cursos.dependencia',DB::raw("CASE WHEN cursos.proyecto ='1' THEN 'SI' ELSE 'NO' END as proyecto"))
                    ->WHERE('cursos.id', '=', $idCurso)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=' , 'cursos.id_especialidad')
                    ->GET();

            $cadwell = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')
                ->WHERE('id', '=', $curso[0]->rango_criterio_pago_minimo)
                ->FIRST();
            $curso[0]->rango_criterio_pago_minimo = $cadwell->perfil_profesional;
            $cadwell = DB::TABLE('criterio_pago')->SELECT('perfil_profesional')
                ->WHERE('id', '=', $curso[0]->rango_criterio_pago_maximo)
                ->FIRST();
            $curso[0]->rango_criterio_pago_maximo = $cadwell->perfil_profesional;

            if($curso[0]->grupo_vulnerable != NULL)
            {
                $gv = $curso[0]->grupo_vulnerable;
                $curso[0]->grupo_vulnerable = $gv;
            }

            if($curso[0]->dependencia != NULL)
            {
                $dp = $curso[0]->dependencia;
                $curso[0]->dependencia = $dp;
            }

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
                'unidad_amovil' => $uniamov,
                'area' => $request->areaCursos,
                'solicitud_autorizacion' => (isset($request->solicitud_autorizacion)) ? $request->solicitud_autorizacion : false,
                'memo_actualizacion' => trim($request->memo_actualizacion),
                'memo_validacion' => trim($request->memo_validacion),
                'cambios_especialidad' => trim($request->cambios_especialidad),
                'nivel_estudio' => trim($request->nivel_estudio),
                'categoria' => trim($request->categoria),
                'tipo_curso' => trim($request->tipo_curso),                
                'rango_criterio_pago_minimo' => trim($request->criterio_pago_minimo_edit),
                'rango_criterio_pago_maximo' => trim($request->criterio_pago_maximo_edit),
                'grupo_vulnerable' => $gv,
                'dependencia' => $dp,
                'proyecto' => $proyecto,
                'estado' => $estado,
                'servicio' => json_encode($request->servicio),
                'motivo' => trim($request->motivo),
                'updated_at' =>date('Y-m-d h:m:s'),
                'iduser_updated' => Auth::user()->id

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
        $data = curso::SELECT('area.formacion_profesional','cursos.categoria','dependencia',
                        'grupo_vulnerable', 'especialidades.nombre as especialidad','cursos.nombre_curso',
                        'cursos.horas','cursos.objetivo','cursos.perfil',
                        DB::raw("(case when cursos.solicitud_autorizacion = 'true' then 'SI' else 'NO' end) as etnia"),
                        'cursos.fecha_validacion','cursos.memo_validacion','cursos.unidad_amovil',
                        'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.tipo_curso',
                        'cursos.modalidad','cursos.clasificacion','observacion','cursos.costo',
                        'cursos.rango_criterio_pago_minimo',
                        DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_minimo) as mini"),
                        'cursos.rango_criterio_pago_maximo',
                        DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_maximo) as maxi"),
                        'cursos.unidades_disponible','cursos.servicio',
                        DB::raw("(case when cursos.proyecto = 'true' then 'SI' else 'NO' end) as proyecto"),
                        )
                        ->WHERE('cursos.estado', '=', true)
                        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
                        ->LEFTJOIN('area', 'area.id', '=', 'especialidades.id_areas')
                        ->ORDERBY('especialidades.nombre', 'ASC')
                        ->ORDERBY('cursos.nombre_curso', 'ASC')
                        ->GET();
                        //dd($data[0]);

        $cabecera = [
            'CAMPO','CATEGORIA','DEPENDENCIA','GRUPO VULNERABLE','ESPECIALIDAD','NOMBRE','HORAS','OBJETIVO',
            'PERFIL INGRESO DEL ALUMNO','SOLICITUD AUTORIZACION DE RIESGO','FECHA DE VALIDACION','MEMO DE VALIDACION',
            'UNIDAD MOVIL','MEMO DE ACTUALIZACION','FECHA DE ACTUALIZACION','TIPO CAPACITACION','MODALIDAD','CLASIFICACION',
            'OBSERVACION','COSTO','CRITERIO DE PAGO MINIMO','NOMBRE CRITERIO MINIMO','CRITERIO DE PAGO MAXIMO',
            'NOMBRE DE CRITERIO MAXIMO','UNIDADES DISPONIBLES','SERVICIO','PROYECTO'
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
                        DB::raw("(case when cursos.solicitud_autorizacion = 'true' then 'SI' else 'NO' end) as etnia"),
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
            'PERFIL','SOLICITUD DE AUTORIZACION','FECHA DE VALIDACION','MEMO DE VALIDACION',
            'UNIDAD MOVIL','MEMO DE ACTUALIZACION','FECHA DE ACTUALIZACION','TIPO CURSO','MODALIDAD','CLASIFICACION',
            'OBSERVACION','COSTO','CRITERIO DE PAGO MINIMO','NOMBRE CRITERIO MINIMO','CRITERIO DE PAGO MAXIMO',
            'NOMBRE DE CRITERIO MAXIMO','SERVICIO', 'PROYECTO','ESTATUS'
        ];
        $nombreLayout = "Catalogo de cursos completo.xlsx";
        $titulo = "Catalogo de cursos completo";
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }
}
