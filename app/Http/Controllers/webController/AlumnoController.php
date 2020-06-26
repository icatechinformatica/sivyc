<?php

namespace App\Http\Controllers\webController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Alumnopre;
use App\Models\Municipio;
use App\Models\Estado;
use App\Models\especialidad;
use App\Models\curso;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDF;
use Carbon\Carbon;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $buscar_aspirante = $request->get('busqueda_aspirantepor');

        $tipoaspirante = $request->get('busqueda_aspirante');
        $retrieveAlumnos = Alumnopre::busquedapor($tipoaspirante, $buscar_aspirante)
        ->PAGINATE(30, ['id', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp']);
        $contador = $retrieveAlumnos->count();
        return view('layouts.pages.vstaalumnos', compact('retrieveAlumnos', 'contador'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $grado_estudio = [
            'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO' => 'POSTGRADO'
        ];
        $estado = new Estado();
        $estados = $estado->all(['id', 'nombre']);
        return view('layouts.pages.sid', compact('estados', 'grado_estudio'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $curp = strtoupper($request->input('curp'));
        $alumnoPre = Alumnopre::WHERE('curp', '=', $curp)->GET(['curp']);
        // obtener el usuario que agrega
        $usuario = Auth::user()->name;
        if ($alumnoPre->isEmpty()) {
            # si la consulta no está vacía hacemos la inserción
            $validator =  Validator::make($request->all(), [
                'nombre' => 'required',
                'apellidoPaterno' => 'required',
                'apellidoMaterno' => 'required',
                'sexo' => 'required',
                'curp' => 'required',
                'domicilio' => 'required',
                'colonia' => 'required',
                'cp' => 'required',
                'estado' => 'required',
                'municipio' => 'required',
                'estado_civil' => 'required',
                'discapacidad' => 'required',
            ]);
            if ($validator->fails()) {
                # devolvemos un error
                //dd($validator);
                return redirect('/alumnos/sid')
                        ->withErrors($validator)
                        ->withInput();
            } else {

                /**
                 * formar el formato fecha para fecha de nacimiento
                 */
                $dia = trim($request->dia);
                $mes = trim($request->mes);
                $anio = trim($request->anio);
                $fecha_nacimiento = $anio."-".$mes."-".$dia;

                //estados
                $nombre_estado = Estado::WHERE('id', '=', $request->estado)->FIRST(['nombre']);

                $AlumnoPreseleccion = new Alumnopre;
                $AlumnoPreseleccion->nombre = $request->nombre;
                $AlumnoPreseleccion->apellido_paterno = $request->apellidoPaterno;
                $AlumnoPreseleccion->apellido_materno = $request->apellidoMaterno;
                $AlumnoPreseleccion->sexo = $request->sexo;
                $AlumnoPreseleccion->curp = $request->curp;
                $AlumnoPreseleccion->fecha_nacimiento = $fecha_nacimiento;
                $AlumnoPreseleccion->telefono = $request->telefonosid;
                $AlumnoPreseleccion->domicilio = $request->domicilio;
                $AlumnoPreseleccion->colonia = $request->colonia;
                $AlumnoPreseleccion->cp = $request->cp;
                $AlumnoPreseleccion->estado = $nombre_estado->nombre;
                $AlumnoPreseleccion->municipio = $request->municipio;
                $AlumnoPreseleccion->estado_civil = $request->estado_civil;
                $AlumnoPreseleccion->discapacidad = $request->discapacidad;
                $AlumnoPreseleccion->ultimo_grado_estudios = $request->ultimo_grado_estudios;
                $AlumnoPreseleccion->medio_entero = ($request->input('medio_entero') === "0") ? $request->input('medio_entero_especificar') : $request->input('medio_entero');
                $AlumnoPreseleccion->puesto_empresa = $request->puesto_empresa;
                $AlumnoPreseleccion->sistema_capacitacion_especificar = ($request->input('motivos_eleccion_sistema_capacitacion') === "0") ? $request->input('sistema_capacitacion_especificar') : $request->input('motivos_eleccion_sistema_capacitacion');
                $AlumnoPreseleccion->empresa_trabaja = $request->empresa;
                $AlumnoPreseleccion->antiguedad = $request->antiguedad;
                $AlumnoPreseleccion->realizo = $usuario;
                $AlumnoPreseleccion->tiene_documentacion = false;
                $AlumnoPreseleccion->save();

                // redireccionamos con un mensaje de éxito
                return redirect('alumnos/indice')->with('success', 'Nuevo Alumno Agregado Exitosamente!');
            }

        } else {
            # por el contrario si no está vacía mandamos un mensaje al usuario
            #Mensaje
            $mensaje = "Lo sentimos, la curp ".$curp." asociada a este registro ya se encuentra en la base de datos.";
            return redirect('/alumnos/sid')->withErrors($mensaje);
        }
    }
    /**
     * formulario número 2
     */
    protected function steptwo($id)
    {
        $id_prealumno = base64_decode($id);
        $alumnoPre = Alumnopre::WHERE('id', '=', $id_prealumno)->FIRST(['chk_acta_nacimiento', 'acta_nacimiento', 'chk_curp', 'documento_curp',
        'chk_comprobante_domicilio', 'comprobante_domicilio', 'chk_ine', 'ine', 'chk_pasaporte_licencia', 'pasaporte_licencia_manejo', 'chk_comprobante_ultimo_grado', 'comprobante_ultimo_grado',
        'chk_fotografia', 'fotografia', 'comprobante_calidad_migratoria', 'chk_comprobante_calidad_migratoria']);
        return view('layouts.pages.frminscripcion2', compact('id_prealumno', 'alumnoPre'));
    }

    protected function update_pregistro(Request $request)
    {
        # ==================================
        # Aquí tenemos el id recién guardado
        # ==================================
        $AlumnosId = $request->alumno_id;

        /***
         * MÉTODOS PARA GUARDAR ARCHIVOS
        */

        /**
        * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('acta_nacimiento')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'acta_nacimiento' => 'mimes:pdf|max:2048'
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $acta_nacimiento = $request->file('acta_nacimiento'); # obtenemos el archivo
                $url_acta_nacimiento = $this->uploaded_file($acta_nacimiento, $AlumnosId, 'acta_nacimiento'); #invocamos el método
                $chk_acta_nacimiento = true;
            }
        } else {
            $chk_acta_nacimiento = false;
            $url_acta_nacimiento = '';
        }

        /**
            * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('copia_curp')) {
            # llamamos el método
            $validator = Validator::make($request->all(), [
                'copia_curp' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $curp = $request->file('copia_curp'); # obtenemos el archivo
                $url_curp = $this->uploaded_file($curp, $AlumnosId, 'curp'); #invocamos el método
                $chk_curp = true;
            }
        } else {
            $chk_curp = false;
            $url_curp = '';
        }

        /**
         * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('comprobante_domicilio')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'comprobante_domicilio' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $comprobante_domicilio = $request->file('comprobante_domicilio'); # obtenemos el archivo
                $url_comprobante_domicilio = $this->uploaded_file($comprobante_domicilio, $AlumnosId, 'comprobante_domicilio'); #invocamos el método
                $chk_comprobante_domicilio = true;
            }
        } else {
            $url_comprobante_domicilio = '';
            $chk_comprobante_domicilio = false;
        }

        /**
         * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('fotografias')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'fotografias' => 'mimes:jpg,jpeg,png|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $fotografia = $request->file('fotografias'); # obtenemos el archivo
                $url_fotografia = $this->uploaded_file($fotografia, $AlumnosId, 'fotografia'); #invocamos el método
                $chk_fotografia = true;
            }
        } else {
            $url_fotografia = '';
            $chk_fotografia = false;
        }

        /**
         * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('ine')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'ine' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $ine = $request->file('ine'); # obtenemos el archivo
                $url_ine = $this->uploaded_file($ine, $AlumnosId, 'ine'); #invocamos el método
                $chk_ine = true;
            }
        } else {
            $chk_ine = false;
            $url_ine = '';
        }

        /**
         * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('licencia_manejo')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'licencia_manejo' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $licencia_manejo = $request->file('licencia_manejo'); # obtenemos el archivo
                $url_licencia_manejo = $this->uploaded_file($licencia_manejo, $AlumnosId, 'licencia_manejo'); #invocamos el método
                $chk_licencia_manejo = true;
            }
        } else {
            $chk_licencia_manejo = false;
            $url_licencia_manejo = '';
        }

        /**
         *
        */
        if ($request->hasFile('comprobante_ultimo_grado_estudios')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'comprobante_ultimo_grado_estudios' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                        ->withErrors($validator);
            } else {
                $grado_estudios = $request->file('comprobante_ultimo_grado_estudios'); # obtenemos el archivo
                $url_grado_estudios = $this->uploaded_file($grado_estudios, $AlumnosId, 'comprobante_ultimo_grado_estudios'); #invocamos el método
                $chk_ultimo_grado_estudios = true;
            }
        } else {
            $chk_ultimo_grado_estudios = false;
            $url_grado_estudios = '';
        }

        /**
         * cargar comprobante_migratorio
        */
        if (is_null($request->input('comprobante_migratorio'))) {
            # verdadero si es null...
            $url_documento_comprobante_migratorio = '';
            $chk_comprobante_calidad_migratoria = false;
        } else {
            $chk_comprobante_calidad_migratoria = true;
            # falso si no es null
            if ($request->hasFile('documento_comprobante_migratorio')) {
                # llamamos al método
                $validator = Validator::make($request->all(), [
                    'documento_comprobante_migratorio' => 'mimes:pdf|max:2048',
                ]);
                if ($validator->fails()) {
                    # code...
                    return redirect('alumnos/sid-paso2/'.base64_encode($AlumnosId))
                            ->withErrors($validator);
                } else {
                    $documento_comprobante_migratorio = $request->file('documento_comprobante_migratorio'); # obtenemos el archivo
                    $url_documento_comprobante_migratorio = $this->uploaded_file($documento_comprobante_migratorio, $AlumnosId, 'documento_comprobante_migratorio'); #invocamos el método
                }
            } else {
                $url_documento_comprobante_migratorio = '';
            }
        }

        # =================================================================================================================================
        #
        #                                               ACTUALIZAR REGISTROS
        #
        # =================================================================================================================================
        $AlumnosUpdate = Alumnopre::findOrfail($AlumnosId);

        $arrayUpdate = [
            'chk_acta_nacimiento' => $chk_acta_nacimiento,
            'chk_curp' => $chk_curp,
            'chk_comprobante_domicilio' => $chk_comprobante_domicilio,
            'chk_fotografia' => $chk_fotografia,
            'chk_ine' => $chk_ine,
            'chk_pasaporte_licencia' => $chk_licencia_manejo,
            'chk_comprobante_ultimo_grado' => $chk_ultimo_grado_estudios,
            'chk_comprobante_calidad_migratoria' => $chk_comprobante_calidad_migratoria,
            'acta_nacimiento' => $url_acta_nacimiento,
            'documento_curp' => $url_curp,
            'comprobante_domicilio' => $url_comprobante_domicilio,
            'fotografia' => $url_fotografia,
            'ine' => $url_ine,
            'pasaporte_licencia_manejo' => $url_licencia_manejo,
            'comprobante_ultimo_grado' => $url_grado_estudios,
            'comprobante_calidad_migratoria' => $url_documento_comprobante_migratorio,
            'tiene_documentacion' => true
        ];

        $AlumnosUpdate->update($arrayUpdate);
        // destruye el arreglo
        unset($arrayUpdate);
        // redireccionamos con un mensaje de éxito
        return redirect('alumnos/indice')->with('success', sprintf('REGISTRO %d  ACTUALIZADO EXTIOSAMENTE!', $AlumnosId));
    }

    public function pdf_registro()
    {
        $pdf = PDF::loadView('layouts.pdfpages.registroalumno');

        return view('layouts\pdfpages\registroalumno');
        return $pdf->stream('medium.pdf');
    }

    protected function show($id)
    {
        $idpre = base64_decode($id);
        $AlumnoMatricula = new  Alumnopre;
        $Especialidad = new especialidad;
        $unidadestbl = new tbl_unidades();
        $tblUnidades = $unidadestbl->SELECT('ubicacion')->GROUPBY('ubicacion')->GET(['ubicacion']);
        $especialidades = $Especialidad->all(['id', 'nombre']);
        $Alumno = $AlumnoMatricula->findOrfail($idpre, ['id', 'nombre', 'apellido_paterno', 'apellido_materno', 'sexo', 'curp', 'fecha_nacimiento',
        'telefono', 'cp', 'estado', 'municipio', 'estado_civil', 'discapacidad', 'domicilio', 'colonia']);

        return view('layouts.pages.sid_general', compact('Alumno', 'especialidades', 'tblUnidades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->alumno_id;
        $AlumnosPre = Alumnopre::findOrfail($id); // encontrar el registro

        $unidadesTbl_ = $request->input('tblunidades');

        // checamos si el usuario ya existe
        if(!$AlumnosPre) {
            // no se puede encontrar el alumno con el id_alumno

        } else {
            // si existe, se tiene que utilizar el mismo número de control
            // obtenemos su número de control
            $Alumno_ = new Alumno();
            $Alumnos_ = $Alumno_->WHERE([
                ['id_pre', '=', $id]
            ])
            ->SKIP(0)->TAKE(1)->GET();
            // aquí veré que obtenemos
            if(count($Alumnos_)) {
                // si hay datos obtenemos el número de control
                $no_control = $Alumnos_[0]->no_control;
            } else {
                // no cuenta con registros, por lo tanto se tendrá que generar un nuevo numero de control
                 /**
                 * obtener el año correcto
                 */
                $date = Carbon::now();
                $anio = $date->format('Y');

                /**
                 * obtenemos los dos ultimos digitos de la fecha
                 */
                $anio_division = substr($anio,2,2);

                /**
                 * obtenemos el valor de un campo de trabajo
                 */
                $unidades = new Unidad();
                $cct_unidades = $unidades->SELECT('cct')
                                ->WHERE('unidad', '=', $unidadesTbl_)
                                ->GET();

                /***
                 * obtener los numeros de las unidades
                 */
                $cla = substr($cct_unidades[0]->cct,0,2); // dos primeros

                $cli = $cla . substr($cct_unidades[0]->cct,5,5); //ultimos 5 caracteres

                $cv = substr($cct_unidades[0]->cct,8,2); // ultimos dos caracteres

                $no_control = $this->setNumeroControl($cli, $anio_division, $cv);
            }
        }

        $usuario = Auth::user()->name;

        /**
         * funcion alumnos
         */
        $alumno = new Alumno([
            'no_control' => $no_control,
            'id_especialidad' => $request->input('especialidad_sid'),
            'id_curso' => $request->input('cursos_sid'),
            'horario' => $request->input('horario'),
            'grupo' => $request->input('grupo'),
            'unidad' => $request->input('tblunidades'),
            'tipo_curso' => $request->input('tipo_curso'),
            'realizo' => $usuario,
            'cerrs' => $request->input('cerrs')
        ]);

        $AlumnosPre->alumnos()->save($alumno);

        return redirect('alumnos/registrados')->with('success', 'Nuevo Alumno Matriculado Exitosamente!');

    }

    protected function getcursos(Request $request)
    {
        if (isset($request->idEsp)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEspecialidad = $request->idEsp;
            $tipo_curso = $request->tipo;
            $Curso = new curso();
            $Cursos = $Curso->WHERE('id_especialidad', '=', $idEspecialidad)
                            ->WHERE('tipo_curso', '=', $tipo_curso)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($Cursos);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function getcursos_update(Request $request)
    {
        if (isset($request->idEsp_mod)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEspecialidad_mod = $request->idEsp_mod;
            $tipo_curso_mod = $request->tipo_mod;
            $Curso = new curso();
            $Cursos_mod = $Curso->WHERE('id_especialidad', '=', $idEspecialidad_mod)
                            ->WHERE('tipo_curso', '=', $tipo_curso_mod)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($Cursos_mod);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function getmunicipios(Request $request)
    {
        if (isset($request->idEst)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEstado=$request->idEst;
            $municipio = new municipio();
            $municipios = $municipio->WHERE('id_estado', '=', $idEstado)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($municipios);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getClientSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/alumnos/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/alumnos/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    protected function showUpdate($id)
    {
        $grado_estudio = [
            'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO' => 'POSTGRADO'
        ];
        $idpre = base64_decode($id);
        $alumnos = new Alumnopre();
        $municipio = new Municipio();
        $estado = new Estado();
        $municipios = $municipio->all();
        $estados = $estado->all();
        $alumno = $alumnos->findOrfail($idpre);
        $fecha_nac = explode("-", $alumno->fecha_nacimiento);
        $anio_nac = $fecha_nac[0];
        $mes_nac = $fecha_nac[1];
        $dia_nac = $fecha_nac[2];
        return view('layouts.pages.sid-modificacion', compact('alumno', 'municipios', 'estados', 'anio_nac', 'mes_nac', 'dia_nac', 'grado_estudio'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updateSid(Request $request, $idAspirante) {
            if (isset($idAspirante)) {
                # code...
                $AlumnoPre = new Alumnopre();

                $dia = trim($request->dia_mod);
                $mes = trim($request->mes_mod);
                $anio = trim($request->anio_mod);
                $fecha_nacimiento = $anio."-".$mes."-".$dia;

                //obtener el estado
                $nombre_estado_mod = Estado::WHERE('id', '=', $request->estado_mod)->GET();

            # code...
                $array = [
                    'nombre' => trim($request->nombre_alum_mod),
                    'apellido_paterno' => trim($request->apellido_pat_mod),
                    'apellido_materno' => trim($request->apellido_mat_mod),
                    'sexo' => trim($request->sexo_mod),
                    'fecha_nacimiento' => trim($fecha_nacimiento),
                    'telefono' => trim($request->telefono_mod),
                    'domicilio' => trim($request->domicilio_mod),
                    'colonia' => trim($request->colonia_mod),
                    'cp' => trim($request->codigo_postal_mod),
                    'estado' => trim($nombre_estado_mod[0]->nombre),
                    'municipio' => trim($request->municipio_mod),
                    'estado_civil' => trim($request->estado_civil_mod),
                    'discapacidad' => trim($request->discapacidad_mod),
                    'ultimo_grado_estudios' => $request->ultimo_grado_estudios_mod,
                    'medio_entero' => ($request->input('medio_entero_mod') === "0") ? $request->input('medio_entero_especificar_mod') : $request->input('medio_entero_mod'),
                    'sistema_capacitacion_especificar' => ($request->input('motivos_eleccion_sistema_capacitacion_mod') === "0") ? $request->input('sistema_capacitacion_especificar_mod') : $request->input('motivos_eleccion_sistema_capacitacion_mod'),
                    'empresa_trabaja' => trim($request->empresa_mod),
                    'antiguedad' => trim($request->antiguedad_mod),
                    'puesto_empresa' => trim($request->puesto_empresa_mod),
                    'direccion_empresa' => trim($request->direccion_empresa_mod)
                ];

                $AspiranteId = base64_decode($idAspirante);

                $AlumnoPre->WHERE('id', '=', $AspiranteId)->UPDATE($array);

                $curpAlumno = $request->curp_alumno;
                return redirect()->route('alumnos.index')
                    ->with('success', sprintf('ASPIRANTE %s  MODIFICADO EXTIOSAMENTE!', $curpAlumno));
            }
    }
    /***
     * METODO
     */
    protected function setNumeroControl($cli_value, $anioDivision, $cv){
        // si arroja algo la consulta se procede
        $alsumnados = new Alumno();
        // pasamos la variable a entero
        $ultima_fecha_division = $alsumnados->SELECT(
            DB::raw('(SUBSTRING(no_control FROM 1 FOR 2)) control')
        )->WHERE(DB::raw('SUBSTRING(no_control FROM 8 FOR 2)'),'=',$cv)
        ->orderBy('control', 'DESC')->limit(1)->FIRST();

        if(is_null($ultima_fecha_division)) {
            // verdadero
            $fechaControl = null;
        } else {
            // falso
            $fechaControl = $ultima_fecha_division->control;
        }

        // comparamos fechas
        if ($fechaControl <> $anioDivision) {
            # nuevo código
            $control = 0;
            $contador = $control + 1;
            $str_length = 4;
            $value_control = substr("0000{$contador}", -$str_length);

            return $control_number = $anioDivision . $cli_value . $value_control;

        } else {
            $als = $alsumnados->SELECT(
                DB::raw('(SUBSTRING(no_control FROM 10 FOR 13)) control ')
            )
            ->WHERE([[DB::raw('SUBSTRING(no_control FROM 8 FOR 2)'),'=',$cv],[DB::raw('SUBSTRING(no_control FROM 1 FOR 2)'),'=', $anioDivision]])
            ->orderBy('control', 'DESC')
            ->limit(1)
            ->GET();

            $control_ = $als[0]->control;

            $count = (int)$control_ + 1;
            $str_length = 4;

            $value_control = substr("0000{$count}", -$str_length);

            return $control_number = $anioDivision . $cli_value . $value_control;
        }
    }
}
