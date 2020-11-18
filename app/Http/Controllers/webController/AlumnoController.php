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
use App\Models\cursoAvailable;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDF;
use Carbon\Carbon;
use App\Models\Unidad;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;
use App\Models\AlumnosSice;

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
        ->PAGINATE(25, ['id', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp', 'es_cereso']);
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

    public function createcerss()
    {
        // nueva modificacion
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

        return view('layouts.pages.sid_cerss', compact('estados', 'grado_estudio'));
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
                $AlumnoPreseleccion->es_cereso = false;
                $AlumnoPreseleccion->save();

                // redireccionamos con un mensaje de éxito
                return redirect('alumnos/indice')->with('success', 'Nuevo Aspirante Agregado Exitosamente!');
            }

        } else {
            # por el contrario si no está vacía mandamos un mensaje al usuario
            #Mensaje
            $mensaje = "Lo sentimos, la curp ".$curp." asociada a este registro ya se encuentra en la base de datos.";
            return redirect('/alumnos/sid')->withErrors($mensaje);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storecerss(Request $request)
    {

        $rules = [
            'nombre_cerss' => ['required', 'min:4'],
            'nombre_aspirante_cerss' => ['required', 'min:2', ],
            'genero_cerss' => 'required',
            'anio_cerss' => 'max:4',
            'file_upload' => 'required|mimes:pdf|file:1',
            'numero_expediente_cerss' => 'required',
        ];

        $messages = [
            'nombre_cerss.required' => 'EL NOMBRE DEL CERSS ES REQUERIDO',
            'nombre_cerss.min' => 'LA LONGITUD DEL NOMBRE DEL CERSS NO PUEDE SER MENOR A 3 CARACTERES',
            'nombre_aspirante_cerss.required' => 'EL NOMBRE DEL ASPIRANTE ES REQUERIDO',
            'nombre_aspirante_cerss.min' => 'LA LONGITUD DEL NOMBRE NO PUEDE SER MENOR A 2 CARACTERES',
            'genero_cerss.required' => 'EL GENERO ES REQUERIDO',
            'anio_cerss.max' => 'EL AÑO NO DEBE DE TENER MAS DE 4 DIGITOS',
            'file_upload.required' => 'EL ARCHIVO DE CARGA ES REQUERIDO',
            'file_upload.mimes' => 'EL ARCHIVO NO ES UNA EXTENSION PDF',
            'file_upload.file' => '',
            'numero_expediente_cerss.required' => 'EL NÚMERO DE EXPEDIENTE ES REQUERIDO',
        ];

        $validator =  Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            # devolvemos un error
            //dd($validator);
            return redirect()->route('preinscripcion.cerss')
                    ->withErrors($validator)
                    ->withInput();
        } else {
            // obtener el usuario que agrega
            $usuario_agrega = Auth::user()->name;
            /**
             * empezamos a insertar el registro
             */
            $dia = trim($request->input('dia_cerss'));
            $mes = trim($request->input('mes_cerss'));
            $anio = trim($request->input('anio_cerss'));

            if (empty($dia) && empty($mes) && empty($anio))
            {
                # condición para saber si se puede armar una fecha
                $fecha_nacimiento = NULL;
            } else {
                $fecha_nacimiento = $anio."-".$mes."-".$dia;
            }

            $id_alumnos_pre = DB::table('alumnos_pre')->insertGetId([
                'nombre' => $request->input('nombre_aspirante_cerss'),
                'apellido_paterno' => (is_null($request->input('apellidoPaterno_aspirante_cerss')) ? '' : $request->input('apellidoPaterno_aspirante_cerss')),
                'apellido_materno' => (is_null($request->input('apellidoMaterno_aspirante_cerss')) ? '' : $request->input('apellidoMaterno_aspirante_cerss')),
                'fecha_nacimiento' => $fecha_nacimiento,
                'nacionalidad' => $request->input('nacionalidad_cerss'),
                'sexo' => $request->input('genero_cerss'),
                'curp' => (is_null($request->input('curp_cerss')) ? '' : $request->input('curp_cerss')),
                'rfc_cerss' => $request->input('rfc_cerss'),
                'ultimo_grado_estudios' => $request->input('ultimo_grado_estudios_cerss'),
                'tiene_documentacion' => false,
                'realizo' => $usuario_agrega,
                'nombre_cerss' => $request->input('nombre_cerss'),
                'numero_expediente' => $request->input('numero_expediente_cerss'),
                'direccion_cerss' => $request->input('direcciones_cerss'),
                'nombre_cerss' => $request->input('nombre_cerss'),
                'titular_cerss' => $request->input('titular_cerss'),
                'telefono' => '',
                'domicilio' => '',
                'colonia' => '',
                'estado' => '',
                'municipio' => '',
                'estado_civil' => '',
                'discapacidad' => '',
                'ultimo_grado_estudios' => '',
                'medio_entero' => '',
                'puesto_empresa' => '',
                'sistema_capacitacion_especificar' => '',
                'empresa_trabaja' => '',
                'antiguedad' => '',
                'es_cereso' => true,
            ]);

            # trabajamos cargando el acta de nacimiento al servidor
            if ($request->hasFile('file_upload')) {

                    // obtenemos el valor de acta_nacimiento
                    $ficha_cerss = DB::table('alumnos_pre')->WHERE('id', $id_alumnos_pre)->VALUE('ficha_cerss');
                    // checamos que no sea nulo
                    if (!is_null($ficha_cerss)) {
                        # si no está nulo
                        if(!empty($ficha_cerss)){
                            $docFichaCerss = explode("/",$ficha_cerss, 5);
                            if (Storage::exists($docFichaCerss[4])) {
                                # checamos si hay un documento de ser así procedemos a eliminarlo
                                Storage::delete($docFichaCerss[4]);
                            }
                        }
                    }

                    $ficha_cerss = $request->file('file_upload'); # obtenemos el archivo
                    $url_ficha_cerss = $this->uploaded_file($ficha_cerss, $id_alumnos_pre, 'ficha_cerss'); #invocamos el método
                    $chk_ficha_cerss = true;
                    // creamos un arreglo
                    $arregloDocs = [
                        'ficha_cerss' => $url_ficha_cerss,
                        'chk_ficha_cerss' => $chk_ficha_cerss
                    ];
            } else {
                $url_ficha_cerss = '';
                $chk_ficha_cerss = false;
            }

            // vamos a actualizar el registro con el arreglo que trae diferentes variables y carga de archivos
            DB::table('alumnos_pre')->WHERE('id', $id_alumnos_pre)->update($arregloDocs);

            // limpiamos el arreglo
            unset($arregloDocs);

            // redireccionamos con un mensaje de éxito
            return redirect()->route('alumnos.index')->with('success', 'Nuevo Aspirante Agregado Exitosamente!');
        }
    }

    public function showCerss($id)
    {
        $id_prealumno = base64_decode($id);
        $alumnoPre_show = DB::table('alumnos_pre')->WHERE('id', $id_prealumno)->FIRST([
            'nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento' , 'nacionalidad' ,
            'sexo' , 'curp', 'rfc_cerss', 'ultimo_grado_estudios', 'es_cereso', 'chk_ficha_cerss', 'ficha_cerss',
            'nombre_cerss', 'numero_expediente', 'direccion_cerss', 'nombre_cerss', 'titular_cerss',
        ]);
        return view('layouts.pages.sid_cerss_show', compact('id_prealumno', 'alumnoPre_show'));
    }
    /**
     * modificaciones formulario updateCerss
     */
    public function updateCerss($id)
    {
        $idPrealumnoUpdate = base64_decode($id);
        $grado_estudio_update = [
            'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO' => 'POSTGRADO',
            'NO ESPECIFICADO' => 'NO ESPECIFICADO'
        ];
        $alumnoPre_update = DB::table('alumnos_pre')->WHERE('id', $idPrealumnoUpdate)->FIRST([
            'nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento' , 'nacionalidad' ,
            'sexo' , 'curp', 'rfc_cerss', 'ultimo_grado_estudios', 'es_cereso', 'chk_ficha_cerss', 'ficha_cerss',
            'nombre_cerss', 'numero_expediente', 'direccion_cerss', 'titular_cerss',
        ]);

        if (is_null($alumnoPre_update->fecha_nacimiento)) {
            # es nulo como verdadero
            $anio_nac_cerss = '';
            $mes_nac_cerss = '';
            $dia_nac_cerss= '';
        } else {
            $fecha_nac = explode("-", $alumnoPre_update->fecha_nacimiento);
            $anio_nac_cerss = $fecha_nac[0];
            $mes_nac_cerss = $fecha_nac[1];
            $dia_nac_cerss = $fecha_nac[2];
        }

        return view('layouts.pages.sid_cerss_update', compact('idPrealumnoUpdate', 'alumnoPre_update', 'anio_nac_cerss', 'mes_nac_cerss', 'dia_nac_cerss', 'grado_estudio_update'));
    }
    /**
     * formulario número 2
     */
    protected function steptwo($id)
    {
        $id_prealumno = base64_decode($id);
        $alumnoPre = DB::table('alumnos_pre')->WHERE('id', '=', $id_prealumno)->FIRST(['chk_acta_nacimiento', 'acta_nacimiento', 'chk_curp', 'documento_curp',
        'chk_comprobante_domicilio', 'comprobante_domicilio', 'chk_ine', 'ine', 'chk_pasaporte_licencia', 'pasaporte_licencia_manejo', 'chk_comprobante_ultimo_grado', 'comprobante_ultimo_grado',
        'chk_fotografia', 'fotografia', 'comprobante_calidad_migratoria', 'chk_comprobante_calidad_migratoria', 'nombre', 'apellido_paterno', 'apellido_materno',
        'curp']);
        return view('layouts.pages.frmfiles_alumno_inscripcion2', compact('id_prealumno', 'alumnoPre'));
    }

    protected function update_pregistro(Request $request)
    {
        if (isset($request->tipoDocumento)) {
            $idPre = $request->alumno_id;
            # validamos que haya un tipo de documento iniciado, es decir la variable
            switch ($request->tipoDocumento) {
                case 'acta_nacimiento':
                    # trabajamos cargando el acta de nacimiento al servidor
                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de acta_nacimiento
                            $alumnoPre = Alumnopre::WHERE('id', $idPre)->FIRST();
                            // checamos que no sea nulo
                            if (!is_null($alumnoPre->acta_nacimiento)) {
                                # si no está nulo
                                if(!empty($alumnoPre->acta_nacimiento)){
                                    $docActanacimiento = explode("/",$alumnoPre->acta_nacimiento, 5);
                                    if (Storage::exists($docActanacimiento[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($docActanacimiento[4]);
                                    }
                                }
                            }

                            $acta_nacimiento = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($acta_nacimiento, $idPre, 'acta_nacimiento'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'acta_nacimiento' => $url_documento,
                                'chk_acta_nacimiento' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }
                    break;
                case 'copia_curp':
                    # modificacion de documento curp
                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->documento_curp)) {
                                # si no está nulo
                                if(!empty($alumnoPre->documento_curp)){
                                    $docCurp = explode("/",$alumnoPre->documento_curp, 5);
                                    if (Storage::exists($docCurp[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($docCurp[4]);
                                    }
                                }
                            }

                            $documento_curp = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($documento_curp, $idPre, 'documento_curp'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'documento_curp' => $url_documento,
                                'chk_curp' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }
                    break;
                case 'comprobante_domicilio':
                    # comprobante de domicilio

                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->comprobante_domicilio)) {
                                # si no está nulo
                                if (!empty($alumnoPre->comprobante_domicilio)) {
                                    # code...
                                    $comprobanteDomicilio = explode("/",$alumnoPre->comprobante_domicilio, 5);
                                    if (Storage::exists($comprobanteDomicilio[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($comprobanteDomicilio[4]);
                                    }
                                }
                            }

                            $comprobante_domicilio = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($comprobante_domicilio, $idPre, 'comprobante_domicilio'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'comprobante_domicilio' => $url_documento,
                                'chk_comprobante_domicilio' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }

                    break;
                case 'fotografia':
                    # fotografia
                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:jpeg,jpg,png|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->fotografia)) {
                                # si no está nulo y el campo fotografia no presenta ''
                                if(!empty($alumnoPre->fotografia)){
                                    $fotografia = explode("/",$alumnoPre->fotografia, 5);
                                    if (Storage::exists($fotografia[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($fotografia[4]);
                                    }
                                }
                            }

                            $fotografia = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($fotografia, $idPre, 'fotografia'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'fotografia' => $url_documento,
                                'chk_fotografia' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }

                    break;
                case 'credencial_electoral':
                    # credencial electoral o credencial de elector
                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->ine)) {
                                # si no está nulo
                                if (!empty($alumnoPre->ine)) {
                                    # code...
                                    $documentoIne = explode("/",$alumnoPre->ine, 5);
                                    if (Storage::exists($documentoIne[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($documentoIne[4]);
                                    }
                                }

                            }

                            $ine = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($ine, $idPre, 'ine'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'ine' => $url_documento,
                                'chk_ine' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }

                    break;
                case 'pasaporte_licencia_manejo':

                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->pasaporte_licencia_manejo)) {
                                # si no está nulo
                                if (!empty($alumnoPre->pasaporte_licencia_manejo)) {
                                    # code...
                                    $documentoLicenciaManejo = explode("/",$alumnoPre->pasaporte_licencia_manejo, 5);
                                    if (Storage::exists($documentoLicenciaManejo[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($documentoLicenciaManejo[4]);
                                    }
                                }
                            }

                            $pasaporte_licencia_manejo = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($pasaporte_licencia_manejo, $idPre, 'pasaporte_licencia_manejo'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'pasaporte_licencia_manejo' => $url_documento,
                                'chk_pasaporte_licencia' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }
                    break;
                case 'ultimo_grado_estudios':

                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->comprobante_ultimo_grado)) {
                                # si no está nulo
                                if (!empty($alumnoPre->comprobante_ultimo_grado)) {
                                    # code...
                                    $ultimoGradoEstudios = explode("/",$alumnoPre->comprobante_ultimo_grado, 5);
                                    if (Storage::exists($ultimoGradoEstudios[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($ultimoGradoEstudios[4]);
                                    }
                                }
                            }

                            $comprobante_ultimo_grado = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($comprobante_ultimo_grado, $idPre, 'comprobante_ultimo_grado'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'comprobante_ultimo_grado' => $url_documento,
                                'chk_comprobante_ultimo_grado' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }
                    break;
                case 'comprobante_migratorio':

                    if ($request->hasFile('customFile')) {
                        # llamamos al método
                        $validator = Validator::make($request->all(), [
                            'customFile' => 'mimes:pdf|max:2048'
                        ]);
                        if ($validator->fails()) {
                            # code...
                            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))
                                    ->withErrors($validator);
                        } else {
                            // obtenemos el valor de documento_curp
                            $alumnoPre = Alumnopre::WHERE('id', '=', $idPre)->FIRST();
                            // checamos que no sea nulo

                            if (!is_null($alumnoPre->comprobante_calidad_migratoria)) {
                                # si no está nulo
                                if (!empty($alumnoPre->comrprobante_calidad_migratoria )) {
                                    # code...
                                    $comprobanteCalidadMigratoria = explode("/",$alumnoPre->comprobante_calidad_migratoria, 5);
                                    if (Storage::exists($comprobanteCalidadMigratoria[4])) {
                                        # checamos si hay un documento de ser así procedemos a eliminarlo
                                        Storage::delete($comprobanteCalidadMigratoria[4]);
                                    }
                                }
                            }

                            $comprobante_calidad_migratoria = $request->file('customFile'); # obtenemos el archivo
                            $url_documento = $this->uploaded_file($comprobante_calidad_migratoria, $idPre, 'comprobante_calidad_migratoria'); #invocamos el método
                            $chk_documento = true;
                            // creamos un arreglo
                            $arregloDocs = [
                                'comprobante_calidad_migratoria' => $url_documento,
                                'chk_comprobante_calidad_migratoria' => $chk_documento
                            ];
                        }
                    } else {
                        $url_documento = '';
                        $chk_documento = false;
                    }
                    break;
                default:
                    # code...
                    break;
            }
            // vamos a actualizar el registro con el arreglo que trae diferentes variables y carga de archivos
            Alumnopre::findOrfail($idPre)->update($arregloDocs);
            $aspirante = Alumnopre::WHERE('id', '=', $idPre)->FIRST();

            $nombre_aspirante = $aspirante->apellido_paterno.' '.$aspirante->apellido_materno.' '.$aspirante->nombre;
            // limpiamos el arreglo
            unset($arregloDocs);

            return redirect('alumnos/preinscripcion/paso2/'.base64_encode($idPre))->with('success', sprintf('DOCUMENTO DEL ALUMNO  %s  CARGADO EXTIOSAMENTE!', $nombre_aspirante));

        }
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

        // checamos si el usuario ya existe
        if(!$AlumnosPre) {
            // no se puede encontrar el alumno con el id_alumno
        } else {
            // si existe, se tiene que utilizar el mismo número de control
            // obtenemos su número de control
            // primeramente habrá que buscarlo en la tabla AlumnoSice
            $alumnos_sice = AlumnosSice::WHERE('curp', $AlumnosPre->curp)->GET();
            // comprobamos si existe algo en la busqueda de la tabla
            if(count($alumnos_sice) > 0){
                // registro encontrado
                $no_control_sice = $alumnos_sice[0]->no_control;

                // hacemos el guardado del alumno con el curso que desea tomar
                $usuario = Auth::user()->name;

                /**
                 * funcion alumnos
                 */
                $alumno = new Alumno([
                    'no_control' => $no_control_sice,
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

                return redirect('alumnos/registrados')->with('success', sprintf('ASPIRANTE VINCULADO EXITOSAMENTE A CURSO CON N° CONTROL %s', $no_control_sice));

            } else {
                // no encontrado

                $unidadesTbl_ = $request->input('tblunidades');

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

                return redirect('alumnos/registrados')->with('success', sprintf('ASPIRANTE VINCULADO EXITOSAMENTE A CURSO CON N° CONTROL %s', $no_control));

            }

        }

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

    protected function checkcursos(Request $request)
    {
        if (isset($request->unidad)){
            $idcurso = $request->idcur;
            $unidad = 'CHK_' . str_replace(' ', '_', $request->unidad) . ' AS chk';
            $check = cursoAvailable::SELECT($unidad, 'curso_id')->WHERE('curso_id', '=', $idcurso)->FIRST();
            $json=json_encode($check);
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
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
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
            'POSTGRADO' => 'POSTGRADO',
            'NO ESPECIFICADO' => 'NO ESPECIFICADO'
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

    protected function modifyUpdateChief($id){
        $grado_estudio = [
            'PRIMARIA INCONCLUSA' => 'PRIMARIA INCONCLUSA',
            'PRIMARIA TERMINADA' => 'PRIMARIA TERMINADA',
            'SECUNDARIA INCONCLUSA' => 'SECUNDARIA INCONCLUSA',
            'SECUNDARIA TERMINADA' => 'SECUNDARIA TERMINADA',
            'NIVEL MEDIO SUPERIOR INCONCLUSO' => 'NIVEL MEDIO SUPERIOR INCONCLUSO',
            'NIVEL MEDIO SUPERIOR TERMINADO' => 'NIVEL MEDIO SUPERIOR TERMINADO',
            'NIVEL SUPERIOR INCONCLUSO' => 'NIVEL SUPERIOR INCONCLUSO',
            'NIVEL SUPERIOR TERMINADO' => 'NIVEL SUPERIOR TERMINADO',
            'POSTGRADO' => 'POSTGRADO',
            'NO ESPECIFICADO' => 'NO ESPECIFICADO'
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
        return view('layouts.pages.sid-modificacion-jefe', compact('alumno', 'municipios', 'estados', 'anio_nac', 'mes_nac', 'dia_nac', 'grado_estudio'));
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

    /**
     * Actualización preinscripcion cerss.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updatedCerssNew(Request $request, $idPreinscripcion) {
        if (isset($idPreinscripcion)) {
            //dd($request);

            // obtener el usuario que agrega
            $usuario_agrega = Auth::user()->name;

            /**
             * empezamos a insertar el registro
             */
            $dia = trim($request->input('dia_cerss'));
            $mes = trim($request->input('mes_cerss'));
            $anio = trim($request->input('anio_cerss'));

            if (empty($dia) && empty($mes) && empty($anio))
            {
                # condición para saber si se puede armar una fecha
                $fecha_nacimiento = NULL;
            } else {
                $fecha_nacimiento = $anio."-".$mes."-".$dia;
            }

        # arreglo de datos
            $array_update_cerss = [

                'nombre' => $request->get('nombre_aspirante_cerss'),
                'apellido_paterno' => (is_null($request->get('apellidoPaterno_aspirante_cerss')) ? '' : $request->get('apellidoPaterno_aspirante_cerss')),
                'apellido_materno' => (is_null($request->get('apellidoMaterno_aspirante_cerss')) ? '' : $request->get('apellidoMaterno_aspirante_cerss')),
                'fecha_nacimiento' => $fecha_nacimiento,
                'nacionalidad' => $request->get('nacionalidad_cerss'),
                'sexo' => $request->get('genero_cerss'),
                'curp' => (is_null($request->get('curp_cerss')) ? '' : $request->get('curp_cerss')),
                'rfc_cerss' => $request->get('rfc_cerss'),
                'ultimo_grado_estudios' => $request->get('ultimo_grado_estudios_cerss'),
                'tiene_documentacion' => false,
                'realizo' => $usuario_agrega,
                'nombre_cerss' => $request->get('nombre_cerss'),
                'numero_expediente' => $request->get('numero_expediente_cerss'),
                'direccion_cerss' => $request->get('direcciones_cerss_update_'),
                'nombre_cerss' => $request->get('nombre_cerss'),
                'titular_cerss' => $request->get('titular_cerss_update_'),
                'telefono' => '',
                'domicilio' => '',
                'colonia' => '',
                'estado' => '',
                'municipio' => '',
                'estado_civil' => '',
                'discapacidad' => '',
                'medio_entero' => '',
                'puesto_empresa' => '',
                'sistema_capacitacion_especificar' => '',
                'empresa_trabaja' => '',
                'antiguedad' => '',
                'es_cereso' => $request->get('is_cerrs'),
            ];
            //dd($array_update_cerss);

            $idPreInscripcion = base64_decode($idPreinscripcion);

            DB::table('alumnos_pre')->WHERE('id', $idPreInscripcion)->UPDATE($array_update_cerss);

            # trabajamos cargando el acta de nacimiento al servidor
            if ($request->hasFile('file_upload')) {

                // obtenemos el valor de acta_nacimiento
                $ficha_cerss = DB::table('alumnos_pre')->WHERE('id', $idPreInscripcion)->VALUE('ficha_cerss');
                // checamos que no sea nulo
                if (!is_null($ficha_cerss)) {
                    # si no está nulo
                    if(!empty($ficha_cerss)){
                        $docFichaCerss = explode("/",$ficha_cerss, 5);
                        if (Storage::exists($docFichaCerss[4])) {
                            # checamos si hay un documento de ser así procedemos a eliminarlo
                            Storage::delete($docFichaCerss[4]);
                        }
                    }
                }

                $ficha_cerss = $request->file('file_upload'); # obtenemos el archivo
                $url_ficha_cerss = $this->uploaded_file($ficha_cerss, $idPreInscripcion, 'ficha_cerss'); #invocamos el método
                $chk_ficha_cerss = true;
                // creamos un arreglo
                $arregloDocs = [
                    'ficha_cerss' => $url_ficha_cerss,
                    'chk_ficha_cerss' => $chk_ficha_cerss
                ];
        } else {
            $url_ficha_cerss = '';
            $chk_ficha_cerss = false;
        }

        // vamos a actualizar el registro con el arreglo que trae diferentes variables y carga de archivos
        DB::table('alumnos_pre')->WHERE('id', $idPreInscripcion)->update($arregloDocs);

        // limpiamos el arreglo
        unset($arregloDocs);

            $numeroExpediente = $request->input('numero_expediente_cerss');
            return redirect()->route('alumnos.index')
                ->with('success', sprintf('ASPIRANTE CON EXPEDIENTE %s  MODIFICADO EXTIOSAMENTE!', $numeroExpediente));
        }
    }

    public function updateSidJefeUnidad(Request $request, $idAspirante){
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
                'direccion_empresa' => trim($request->direccion_empresa_mod),
                'curp' => trim($request->curp_mod)
            ];

            $AspiranteId = base64_decode($idAspirante);

            $AlumnoPre->WHERE('id', '=', $AspiranteId)->UPDATE($array);

            $curpAlumno = $request->curp_mod;
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
