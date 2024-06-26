<?php

namespace App\Http\Controllers\ExpeController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelExpe\ExpeUnico;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Models\Alumnopre;
use PDF;

class ExpedienteController extends Controller
{
    function __construct() {
        $this->path_files = env("APP_URL").'/storage/uploadFiles';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $folio = null)
    {
        #Roles de los 4 dptos
        $val_rol = null;
        $user = Auth::user();$roles = $user->roles();$resul = $roles->first();
        $slug = $resul->slug;
        if ($slug == 'admin') {$val_rol = 0;}
        else if($slug == 'direccion_vinculacion' || $slug == 'unidad_vinculacion' || $slug == 'vinculadores_administrativo' || $slug == 'director_unidad') {$val_rol = 1;}
        else if($slug == 'unidad' || $slug == 'titular_unidad' || $slug == 'auxiliar_unidad') {$val_rol = 2;}
        else if($slug == 'administrativo') {$val_rol = 3;}
        else if($slug == 'titular-innovacion') {$val_rol = 4;}

        #REALIZAMOS LA BUSQUEDA
        $valor_select_true = $array_rol = [];

        if($folio == null){
            $req_foliogrupo = $request->input('txtbuscar');
        }else{
            $req_foliogrupo = $folio;
        }
        $data_cursos = null;
        $v_radios = null; //Variable que obtiene los valores de cada json
        $json_dptos = null;
        $search_docs = null;
        $st_general = null;
        $path_files = $this->path_files;
        if($req_foliogrupo != ''){
            // $data_cursos = DB::table('tbl_cursos')->select('id', 'folio_grupo')->where('folio_grupo', $req_foliogrupo)->first();
            $data_cursos = DB::table('tbl_cursos as tc')
            ->join('alumnos_registro as ar', 'tc.folio_grupo', '=', 'ar.folio_grupo')
            ->select('tc.id','tc.folio_grupo','tc.curso','tc.area','tc.tcapacitacion','tc.clave','tc.nombre', 'tc.tipo_curso',
                'tc.espe','tc.mexoneracion','tc.inicio','tc.termino','tc.hini','tc.hfin','tc.costo','ar.costo as costo_alumnos',
                DB::raw("CASE
                            WHEN tc.tipo = 'EXO' THEN 'EXONERACIÓN DE CUOTA'
                            WHEN tc.tipo = 'PINS' THEN 'CUOTA ORDINARIA'
                            WHEN tc.tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA'
                        END as tpago"))
            ->where('tc.folio_grupo', '=', $req_foliogrupo)
            ->first();

            if ($data_cursos != null) {
                #Hacemos la consulta en expedientes unicos
                $existsExpediente = DB::table('tbl_cursos_expedientes')->where('folio_grupo', $req_foliogrupo)->exists();
                if (!$existsExpediente){
                    #FALSE crear todo desde cero
                    $json_vacios = $this->llenar_json_exp(); #llamamos los arrays para mandarlos como json
                    try {
                        $reg_expedientes = new ExpeUnico;
                        $reg_expedientes['id'] = $data_cursos->id;
                        $reg_expedientes['id_curso'] = $data_cursos->id;
                        $reg_expedientes['folio_grupo'] = $data_cursos->folio_grupo;
                        $reg_expedientes['vinculacion'] = $json_vacios[0];
                        $reg_expedientes['academico'] = $json_vacios[1];
                        $reg_expedientes['administrativo'] = $json_vacios[2];
                        $reg_expedientes['created_at'] = date('Y-m-d');
                        $reg_expedientes['updated_at'] = date('Y-m-d');
                        $reg_expedientes['iduser_created'] = Auth::user()->id;
                        $reg_expedientes->save();
                    } catch (\Throwable $th) {
                        //throw $th;
                        return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡ERROR AL CREAR EL REGISTRO!')->with('status', 'danger');
                    }
                    //Retornamos al inicio para que cargue de nuevo debido a ser nuevo registro
                    return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡FOLIO REGISTRADO CORRECTAMENTE, INTENTE DE NUEVO!')->with('status', 'success');
                }else{
                    #TRUE buscar si los json estan llenos si no deberiamos agregar
                    $foundJson = ExpeUnico::where('folio_grupo', $req_foliogrupo)->whereNotNull('vinculacion')
                    ->whereNotNull('academico')->whereNotNull('administrativo')->first();

                    if ($foundJson == null) {
                        #Actualizamos los campos JSON por que null significa que no estan llenos #Mandamos a llamar los arrays asociativos para los JSON
                        $json_vacios = $this->llenar_json_exp(); #llamamos los arrays para mandarlos como json
                        DB::table('tbl_cursos_expedientes')->where('folio_grupo', $req_foliogrupo)
                        ->update(['id_curso' => $data_cursos->id, 'folio_grupo' => $data_cursos->folio_grupo,
                        'vinculacion' => $json_vacios[0], 'academico' => $json_vacios[1], 'administrativo' => $json_vacios[2],
                        'created_at' => date('Y-m-d'), 'updated_at' => date('Y-m-d'), 'iduser_updated' => Auth::user()->id]);

                        //Recargamos porque por recien se agrego el json en los campos correspondientes.
                        return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡FOLIO REGISTRADO CORRECTAMENTE, INTENTE DE NUEVO!')->with('status', 'success');

                    }else{
                        #No hacer nada todo esta correcto.
                        #Damos acceso validamos ROL y status de json por dpto

                        $search_docs = $this->search_docs($data_cursos->folio_grupo); // Buscar enlaces de los documentos fuera de los json;

                        $json_dptos = ExpeUnico::where('folio_grupo', $req_foliogrupo)->whereNotNull('vinculacion')
                        ->whereNotNull('academico')->whereNotNull('administrativo')->first();
                        $v_radios = $this->get_val_json_valid($json_dptos); //Validacion para check en vista

                        // $this->proces_documentos($data_cursos->folio_grupo); ##Actualizar registros de existe evidencias
                        $array_rol = ['rol' => null, 'status_json' => null, 'btn_envio_dta' => false, 'idcurso' => null, 'message_return' => null]; #declaramos la variable comenzando con esos valores
                        $st_general = [$foundJson->vinculacion['status_dpto'], $foundJson->academico['status_dpto'], $foundJson->administrativo['status_dpto']]; #enviamos status para validar select de dptos

                        #Validamos roles y enviamos por array el rol y status del json
                        if($val_rol == 1) {
                            $array_rol = ['rol' => $val_rol, 'status_json' => $foundJson->vinculacion['status_dpto'], 'btn_envio_dta' => false, 'idcurso' => $data_cursos->id, 'message_return' => $foundJson->vinculacion['descrip_return']];
                        }
                        else if($val_rol == 2) {
                            $array_rol = ['rol' => $val_rol, 'status_json' => $foundJson->academico['status_dpto'], 'btn_envio_dta' => false, 'idcurso' => $data_cursos->id, 'message_return' => $foundJson->academico['descrip_return']];
                        }
                        else if($val_rol == 3){
                            $array_rol = ['rol' => $val_rol, 'status_json' => $foundJson->administrativo['status_dpto'], 'btn_envio_dta' => false, 'idcurso' => $data_cursos->id, 'message_return' => $foundJson->administrativo['descrip_return']];
                            #validamos si los json estan como guardados para mostrar el envia a DTA
                            if($foundJson->vinculacion['status_save'] == true
                            && $foundJson->academico['status_save'] == true
                            && $foundJson->administrativo['status_save'] == true){
                                $array_rol = ['rol' => $val_rol, 'status_json' => $foundJson->administrativo['status_dpto'], 'btn_envio_dta' => true, 'idcurso' => $data_cursos->id, 'message_return' => $foundJson->administrativo['descrip_return']];
                            }
                        }else if($val_rol == 4){
                            $array_rol = ['rol' => $val_rol, 'status_json' => $foundJson->administrativo['status_dpto'], 'btn_envio_dta' => false, 'idcurso' => $data_cursos->id, 'message_return' => $foundJson->administrativo['descrip_return']];
                        }

                    }

                }
            }else{
                #Mensaje de no encontrado
                return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡FOLIO NO ENCONTRADO!')->with('status', 'danger');
            }
        }

        return view('vistas_expe.expediente_unico', compact('val_rol', 'data_cursos', 'array_rol', 'st_general', 'v_radios', 'json_dptos', 'search_docs', 'path_files'));
    }

    #Llenar array para anexar al json de expedientes
    public function llenar_json_exp(){
        $vinculacion = [
            "doc_1" => [
                "nom_doc" => "Convenio Específico / Acta de acuerdo.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => "",
                "iduser" => "",
                "convenio_firma" => "",
                "convenio_cerss_one" => "",
                "convenio_cerss_two" => "",
                "url_pdf_acta" => "",
                "url_pdf_convenio" => ""
            ],
            "doc_2" => [
                "nom_doc" => "Copia de autorización de Exoneración o Reducción de cuota de recuperación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_3" => [
                "nom_doc" => "Original  de la  Solicitud de Apertura del curso o certificacion al Depto. Académico.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_4" => [
                "nom_doc" => "SID-01 solicitud de inscripción del interesado.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_5" => [
                "nom_doc" => "CURP actualizada o Copia de Acta de Nacimiento.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_6" => [
                "nom_doc" => "Copia de comprobante de último grado de estudios (en caso de contar con el).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_7" => [
                "nom_doc" => "Copia del recibo oficial de la cuota de recuperación expedido por la Delegación Administrativa y comprobante de depósito o transferencia Bancaria.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_8" => [
                "nom_doc" => "Soporte de manifiesto de inscripción",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];
        $academico = [
            "doc_8" => [
                "nom_doc" => "Original de memorándum ARC-01, solicitud de Apertura de cursos de Capacitación y/o certificación a la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_9" => [
                "nom_doc" => "Copia de memorándum de autorización de ARC-01, emitido por la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_10" => [
                "nom_doc" => "Original de memorándum ARC-02, solicitud de modificación, reprogramación y/o cancelación de curso a la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_11" => [
                "nom_doc" => "Copia de Memorándum de autorización de ARC-02 emitido por la Dirección Técnica Académica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_12" => [
                "nom_doc" => "Copia de RIACD-02 Inscripción.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_13" => [
                "nom_doc" => "Copia de RIACD-02 Acreditación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_14" => [
                "nom_doc" => "Copia de RIACD-02 Certificación.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_15" => [
                "nom_doc" => "Copia de LAD-04 (Lista de Asistencia).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_16" => [
                "nom_doc" => "Copia de RESD-05 (Registro de Evaluación por Subobjetivos).",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_17" => [
                "nom_doc" => "Originales o Copia de las Evaluaciones y/o Reactivos de aprendizaje del alumno y/o resumen de actividades. en caso de ICATECH virtual.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_18" => [
                "nom_doc" => "Original o Copia de las Evaluaciones al Docente y Evaluación del Curso y/o resumen de actividades en caso de ICATECH virtual.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_19" => [
                "nom_doc" => "Reporte fotográfico, como mínimo 2 dos fotografías.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_25" => [
                "nom_doc" => "Oficio de entrega de constancias",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];
        $administrativa = [
            "doc_20" => [
                "nom_doc" => "Memorandum de solicitud de suficiencia presupuestal.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_21" => [
                "nom_doc" => "Copia memorandum de autorización de Suficiencia Presupuestal.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_22" => [
                "nom_doc" => "Original de Contrato de prestación de servicios profesionales del Instructor externo, con firma autógrafa o Firma Electrónica.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_23" => [
                "nom_doc" => "Copia de solicitud de pago al Instructor.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "doc_24" => [
                "nom_doc" => "Comprobante Fiscal Digital por Internet o comprobante de transferencia bancaria de pagp al instructor externo.",
                "existe_evidencia" => 'VACIO',
                "observaciones" => "",
                "url_documento" => "",
                "fecha_subida" => ""
            ],
            "status_dpto" => "CAPTURA",
            "status_save" => false,
            "fecha_guardado" => "",
            "fecha_envio_dta" => "",
            "fecha_validado" => "",
            "fecha_retornado" => "",
            "id_user_save" => null,
            "id_user_valid" => null,
            "id_user_return" => null,
            "descrip_return" => ""
        ];

        $json_vacios = [$vinculacion, $academico, $administrativa];
        return $json_vacios;
    }

    #Obtener valores de JSON dpto para validar el formulario
    public function get_val_json_valid($json_dptos) {
        $v_vincu = [];
        $v_academic = [];
        $v_adminis = [];
        for ($i = 1; $i <= 8; $i++) {
            $clave = 'doc_' . $i;
            $valorfor = $json_dptos->vinculacion[$clave]['existe_evidencia'];
            $valtxt = $json_dptos->vinculacion[$clave]['observaciones'];
            $v_vincu[$clave] = $valorfor;
            $v_vincu['doc_txt'. $i] = $valtxt;
        }
        for ($i=8; $i <= 19 ; $i++) {
            $clave = 'doc_' . $i;
            $valorfor = $json_dptos->academico[$clave]['existe_evidencia'];
            $valtxt = $json_dptos->academico[$clave]['observaciones'];
            $v_academic[$clave] = $valorfor;
            $v_academic['doc_txt'. $i] = $valtxt;
        }
        //Esto se debe a que en academico esta el 25 de manera no seriada
        $v_academic['doc_25'] = $json_dptos->academico['doc_25']['existe_evidencia'];
        $v_academic['doc_txt25'] = $json_dptos->academico['doc_25']['observaciones'];

        for ($i=20; $i <= 24 ; $i++) {
            $clave = 'doc_' . $i;
            $valorfor = $json_dptos->administrativo[$clave]['existe_evidencia'];
            $valtxt = $json_dptos->administrativo[$clave]['observaciones'];
            $v_adminis[$clave] = $valorfor;
            $v_adminis['doc_txt'. $i] = $valtxt;
        }
        $resul = [$v_vincu, $v_academic, $v_adminis];
        return $resul;
    }

    #Realizar consultas de diferentes documento pdf en la BD
    public function search_docs($folio){

        $bddoc2 = DB::table('exoneraciones')->where('folio_grupo',$folio)->value('memo_soporte_dependencia');
        $mod_insctructor = DB::table('tbl_cursos')->where('folio_grupo',$folio)->value('modinstructor');

        //Obtenemos la lista de los alumnos con sus campos correspondientes
        $bddocAlumnos = Inscripcion::select(
            'id',
            'alumno',
            'id_pre',
            DB::raw("requisitos->>'documento' as documento"),
            DB::raw("requisitos->>'chk_curp' as curp"),
            DB::raw("requisitos->>'chk_escolaridad' as estudio"),
            DB::raw("requisitos->>'chk_acta_nacimiento' as acta_nacimiento"),
            DB::raw("CASE
                        WHEN (requisitos->>'documento' IS NULL OR requisitos->>'documento' = '')
                            AND (requisitos->>'chk_curp' IS NULL OR requisitos->>'chk_curp' = '')
                            AND (requisitos->>'chk_escolaridad' IS NULL OR requisitos->>'chk_escolaridad' = '')
                            AND (requisitos->>'chk_acta_nacimiento' IS NULL OR requisitos->>'chk_acta_nacimiento' = '')
                        THEN 'VACIO'
                        ELSE NULL
                     END as estado_requisitos")
        )
        ->where('folio_grupo', '=', $folio)
        ->orderBy('id','ASC')
        ->get();

        // Contamos los registros para validar si existen o no documentos
        $contCurp = Inscripcion::where('folio_grupo', '=', $folio)
        ->whereRaw("requisitos->>'chk_curp' = 'true'")
        ->whereRaw("requisitos->>'documento' IS NOT NULL AND requisitos->>'documento' != ''")
        ->count();

        $contEsco = Inscripcion::where('folio_grupo', '=', $folio)
        ->whereRaw("requisitos->>'chk_escolaridad' = 'true'")
        ->whereRaw("requisitos->>'documento' IS NOT NULL AND requisitos->>'documento' != ''")
        ->count();

        $bddoc789 = DB::table('tbl_cursos')->select('comprobante_pago', 'file_arc01', 'pdf_curso', 'file_arc02', 'arc', 'tipo_curso')->where('folio_grupo', '=', $folio)->first();
        $bddoc2021 = DB::table('tabla_supre as sup')->select('sup.doc_validado', 'sup.doc_supre')
        ->join('folios as f', 'f.id_supre', '=', 'sup.id')
        ->join('tbl_cursos as c', 'c.id', '=', 'f.id_cursos')
        ->where('c.folio_grupo', $folio)->first();

        //Contrato firma electronica y tradicional
        $bdECont = DB::table('tbl_cursos as tc')
        ->join('contratos as con', 'con.id_curso', '=', 'tc.id')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Contrato')
        ->where('ef.status', 'VALIDADO')->value('con.id_contrato');

        // $bddoc22 = DB::table('contratos as con')->join('tbl_cursos as c', 'c.id', '=', 'con.id_curso')
        // ->where('c.folio_grupo', $folio)->value('con.arch_contrato');
        $bddoc22 = DB::table('contratos as con')->select('con.arch_contrato', 'con.arch_factura', 'con.arch_factura_xml')->join('tbl_cursos as c', 'c.id', '=', 'con.id_curso')
        ->where('c.folio_grupo', $folio)->first();

        $bddoc23 = DB::table('pagos as pa')->select('pa.arch_solicitud_pago', 'pa.arch_pago')->join('tbl_cursos as c', 'c.id', '=', 'pa.id_curso')
        ->where('c.folio_grupo', $folio)->first();

        // $bddoc24 = DB::table('instructores as i')->select('i.archivo_rfc')->join('tbl_cursos as c', 'c.id_instructor', '=', 'i.id')
        // ->where('c.folio_grupo', $folio)->first();

        //Firma lista de asistencia tradicional y electronica
        $bdEAsis = DB::table('tbl_cursos as tc')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Lista de asistencia')
        ->where('ef.status', 'VALIDADO')->value('tc.id');

        $bdEFoto = DB::table('tbl_cursos as tc')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Reporte fotografico')
        ->where('ef.status', 'VALIDADO')->value('tc.id');

        $bdECalif = DB::table('tbl_cursos as tc')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Lista de calificaciones')
        ->where('ef.status', 'VALIDADO')->value('tc.id');

        $bdAsisEvid = DB::table('pagos as pag')->select('pag.arch_asistencia', 'pag.arch_evidencia','arch_calificaciones')->join('tbl_cursos as c', 'c.id', '=', 'pag.id_curso')
        ->where('c.folio_grupo', $folio)->where('pag.status_recepcion', 'VALIDADO')->first();

        //Obtener comprobante de pago ya que se actualizaron rutas
        $bdReciboP = DB::table('tbl_recibos')->where('folio_grupo', $folio)->where('status_folio', '!=', 'CANCELADO')->whereNotNull('status_folio')->value('file_pdf');

        $bdReciboT = DB::table('tbl_cursos')
        ->select('comprobante_pago', DB::raw("EXTRACT(YEAR FROM termino) as anio_curso"), DB::raw("
            CASE
                WHEN comprobante_pago IS NOT NULL
                    AND (folio_pago ILIKE '%PROV%' OR folio_pago ~ '^[0-9]+$')
                THEN 'Provisional'
                ELSE 'NoProvisional'
            END as es_valido
        "))
        ->where('folio_grupo', '=', $folio)
        ->first();

        //Variables
        $doc2 = $doc5 = $doc6 = $doc7 = $validRec = $doc8 = $doc9 = $doc10 = $doc11 = $doc20 = $doc21 =
        $doc22 = $docAsis = $docFoto = $docCalif = $doc23 = $doc24 = $tipoCurso = $docXml = '';
        $docAlumnos = []; $reciboProvi =  true;
        //Soporte de constancias
        if(!empty($bddoc2)){$doc2 = $bddoc2;}

        //Alumnos
        ##Consulta de curp y comprobante alumnos
        if(!empty($contCurp)){$doc5 = $contCurp;}
        if(!empty($contEsco)){$doc6 = $contEsco;}
        if(!empty($bddocAlumnos)){$docAlumnos = $bddocAlumnos;}

        //Validar recibo de pago en dos tablas
        if(!empty($bdReciboP)){$doc7 = env("APP_URL").'/storage/'.$bdReciboP; $validRec = 'digital';}
        else if(!empty($bdReciboT->comprobante_pago)){
            $doc7 = $bdReciboT->comprobante_pago;
            $validRec = $bdReciboT->es_valido;
            $anioCurso = $bdReciboT->anio_curso;
        }

        //Arc01
        if(!empty($bddoc789->file_arc01) && ($bddoc789->arc == '01')){$doc8 = $bddoc789->file_arc01;}
        if(!empty($bddoc789->pdf_curso) && ($bddoc789->arc == '01')){$doc9 = $bddoc789->pdf_curso;}
        if(!empty($bddoc789->file_arc02) && ($bddoc789->arc == '02')){$doc10 = $bddoc789->file_arc02;}
        if(!empty($bddoc789->pdf_curso) && ($bddoc789->arc == '02')){$doc11 = $bddoc789->pdf_curso;}
        if(!empty($bddoc2021->doc_supre)){$doc20 = $bddoc2021->doc_supre;}
        if(!empty($bddoc2021->doc_validado)){$doc21 = $bddoc2021->doc_validado;}

        //Validamos contrato si no esta entonces enviamos el id del contraro para visualizarlo electronicamente
        if(!empty($bdECont)){$doc22 = $bdECont;}
        else if(!empty($bddoc22->arch_contrato)){$doc22 = $bddoc22->arch_contrato;}
        // Asistencia
        if(!empty($bdEAsis)){$docAsis = $bdEAsis;}
        else if(!empty($bdAsisEvid->arch_asistencia)){$docAsis = $bdAsisEvid->arch_asistencia;}
        // else if($bddoc789->tipo_curso == 'CERTIFICACION'){ $tipoCurso = "CERTIFICACION";}

        //Fotografico
        if(!empty($bdEFoto)){$docFoto = $bdEFoto;}
        else if(!empty($bdAsisEvid->arch_evidencia)){$docFoto = $bdAsisEvid->arch_evidencia;}
        //E Calificaciones
        if(!empty($bdECalif)){$docCalif = $bdECalif;}
        else if(!empty($bdAsisEvid->arch_calificaciones)){$docCalif = $bdAsisEvid->arch_calificaciones;}


        // if(!empty($bddoc23->arch_pago)){$doc24 = $bddoc23->arch_pago;}

        //Validacion d (delegacion)
        if($mod_insctructor == 'ASIMILADOS A SALARIOS'){
            if(!empty($bddoc23->arch_solicitud_pago)){$doc23 = $bddoc23->arch_solicitud_pago;}

        }else{
            if(!empty($bddoc23->arch_pago)){$doc23 = $bddoc23->arch_pago;}
            else if(!empty($bddoc23->arch_solicitud_pago)){$doc23 = $bddoc23->arch_solicitud_pago;}
        }

        //Validacion e (delegacion)
        if($mod_insctructor == 'ASIMILADOS A SALARIOS'){
            if(!empty($bddoc23->arch_pago)){$doc24 = $bddoc23->arch_pago;}
        }else{
            if(!empty($bddoc22->arch_factura)){$doc24 = $bddoc22->arch_factura; $docXml = $bddoc22->arch_factura_xml;}
        }

        $url_docs = array(
            "urldoc2" => $doc2,"urldoc5" => $doc5,"urldoc6" => $doc6,"urldoc7" => $doc7,"urldoc8" => $doc8,"urldoc9" => $doc9,"urldoc10" => $doc10,"urldoc11" => $doc11,
            "urldoc20" => $doc20,"urldoc21" => $doc21,"urldoc22" => $doc22,"urldoc23" =>$doc23,"urldoc24"=>$doc24,
            "urldoc15" =>$docAsis,"urldoc19" => $docFoto, "validRecibo"=>$validRec, "urldoc16"=>$docCalif, "alumnos_req" => $docAlumnos, 'doc_xml' => $docXml, 'anio_curso' => $anioCurso
        );
        //$this->guardarLinks($folio, $url_docs);  #Agregar las url externas a la tabla de expedientes
        $this->proces_documentos($folio, $url_docs);
        return $url_docs;
    }

    #Guardar links externos de pdf cuando se realiza la busqueda externa
    public function guardarLinks($folio, $array_links){
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo', 'id')->where('folio_grupo', '=', $folio)->first();

        #Comparamos si existen en la tabla de lo contrario de guadaran en la nueva tabla
        $doc_insert_vinc = $doc_insert_aca = $doc_insert_adm = array();
        #Documentos externos
        $n_vinc = [2,5,6,7];
        $n_acad = [8,9,10,11];
        $n_adm = [20,21,24];

        #Vinculacion
        for ($i=0; $i < count($n_vinc); $i++) {
            $nuevoArray = [];
            if ($bd_json->vinculacion['doc_'. $n_vinc[$i]]['url_documento'] != $array_links['urldoc' . $n_vinc[$i]]) {
                $nuevoArray['doc'] = $n_vinc[$i];
                $nuevoArray['url'] = $array_links['urldoc' . $n_vinc[$i]];
                $doc_insert_vinc[] = $nuevoArray;
            }
        }
        #Academico
        for ($i=0; $i < count($n_acad); $i++) {
            $nuevoArray = [];
            if ($bd_json->academico['doc_'. $n_acad[$i]]['url_documento'] != $array_links['urldoc' . $n_acad[$i]]) {
                $nuevoArray['doc'] = $n_acad[$i];
                $nuevoArray['url'] = $array_links['urldoc' . $n_acad[$i]];
                $doc_insert_aca[] = $nuevoArray;
            }
        }
        #Administratiivo
        for ($i=0; $i < count($n_adm); $i++) {
            $nuevoArray = [];
            if ($bd_json->administrativo['doc_'. $n_adm[$i]]['url_documento'] != $array_links['urldoc' . $n_adm[$i]]) {
                $nuevoArray['doc'] = $n_adm[$i];
                $nuevoArray['url'] = $array_links['urldoc' . $n_adm[$i]];
                $doc_insert_adm[] = $nuevoArray;
            }
        }

        #Se actualizan los campos de url en caso de que no haya registros en los campos
        if (count($doc_insert_vinc) > 0 || count($doc_insert_aca) > 0 || count($doc_insert_adm) > 0) {
            try {
                $expeUnico = ExpeUnico::find($bd_json->id);
                if(count($doc_insert_vinc) > 0){
                    $json1 = $expeUnico->vinculacion;
                    foreach ($doc_insert_vinc as $val) {
                        $json1['doc_'.$val['doc']]['url_documento'] = $val['url'];
                    }
                    $expeUnico->vinculacion = $json1;
                }

                if(count($doc_insert_aca) > 0){
                    $json2 = $expeUnico->academico;
                    foreach ($doc_insert_aca as $val) {
                        $json2['doc_'.$val['doc']]['url_documento'] = $val['url'];
                    }
                    $expeUnico->academico = $json2;
                }

                if(count($doc_insert_adm) > 0){
                    $json3 = $expeUnico->administrativo;
                    foreach ($doc_insert_adm as $val) {
                        $json3['doc_'.$val['doc']]['url_documento'] = $val['url'];
                    }
                    $expeUnico->administrativo = $json3;
                }
                $expeUnico->updated_at = date('Y-m-d');
                $expeUnico->save();
            } catch (\Throwable $th) {
                return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡ERROR AL ACTUALIZAR LINKS EXTERNOS!')->with('status', 'danger');
            }
            return 'Links actualizados';
        }
        return 'No es necesario actualizar';

    }

    #Agregar si de manera automatica en caso de que los documentos existan.
    public function proces_documentos($folio, $array_doc){
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo', 'id')->where('folio_grupo', '=', $folio)->first();
        $docs = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,25,20,21,22,23,24];
        $docsVincu = [2,5,6,7];
        $docsAcad = [8,9,10,11,15,16,19];
        $docsDeleg = [20,21,22,23,24];

        $exUnico = ExpeUnico::find($bd_json->id);
        $json1 = $exUnico->vinculacion;
        $json2 = $exUnico->academico;
        $json3 = $exUnico->administrativo;
        for ($i=0; $i < count($docs) ; $i++){
            if($i<=7){

                if(!empty($bd_json->vinculacion['doc_'.$docs[$i]]['url_documento'])){ #Validamos si hay pdf
                    $json1['doc_'.$docs[$i]]['existe_evidencia'] = 'si';

                }else if($bd_json->vinculacion['doc_'.$docs[$i]]['existe_evidencia'] == 'si' ||
                $bd_json->vinculacion['doc_'.$docs[$i]]['existe_evidencia'] == '' ||
                $bd_json->vinculacion['doc_'.$docs[$i]]['existe_evidencia'] == 'VACIO'){
                    $json1['doc_'.$docs[$i]]['existe_evidencia'] = 'no_aplica';
                }



            }else if($i >= 7 && $i <= 19){
                if(!empty($bd_json->academico['doc_'.$docs[$i]]['url_documento'])){
                    $json2['doc_'.$docs[$i]]['existe_evidencia'] = 'si';

                }else if($bd_json->academico['doc_'.$docs[$i]]['existe_evidencia'] == 'si' ||
                $bd_json->academico['doc_'.$docs[$i]]['existe_evidencia'] == '' ||
                $bd_json->academico['doc_'.$docs[$i]]['existe_evidencia'] == 'VACIO'){
                    if($i != 16 && $i != 17){
                        $json2['doc_'.$docs[$i]]['existe_evidencia'] = 'no_aplica';
                    }
                }

            }else if($i >= 20){
                if(!empty($bd_json->administrativo['doc_'.$docs[$i]]['url_documento'])){
                    $json3['doc_'.$docs[$i]]['existe_evidencia'] = 'si';

                }else if($bd_json->administrativo['doc_'.$docs[$i]]['existe_evidencia'] == 'si' ||
                $bd_json->administrativo['doc_'.$docs[$i]]['existe_evidencia'] == '' ||
                $bd_json->administrativo['doc_'.$docs[$i]]['existe_evidencia'] == 'VACIO'){
                    $json3['doc_'.$docs[$i]]['existe_evidencia'] = 'no_aplica';
                }

            }
        }

        //Validamos el convenio especifico debido al campo diferente
        if(!empty($bd_json->vinculacion['doc_1']['url_pdf_convenio'])
        || !empty($bd_json->vinculacion['doc_1']['url_pdf_acta'])
        || !empty($bd_json->vinculacion['doc_1']['url_documento'])){
            $json1['doc_1']['existe_evidencia'] = 'si';
        }else{
            $json1['doc_1']['existe_evidencia'] = 'no_aplica';
        }

        //Recorremos el array de consultas y si tiene archivo colocamos si
        foreach ($docsVincu as $key => $num) {
            if(!empty($array_doc['urldoc'.$num])){
                $json1['doc_'.$num]['existe_evidencia'] = 'si';
            }
        }
        foreach ($docsAcad as $key => $num) {
            if(!empty($array_doc['urldoc'.$num])){
                $json2['doc_'.$num]['existe_evidencia'] = 'si';
            }
        }
        foreach ($docsDeleg as $key => $num) {
            if(!empty($array_doc['urldoc'.$num])){
                $json3['doc_'.$num]['existe_evidencia'] = 'si';
            }
        }

        $exUnico->vinculacion = $json1;
        $exUnico->academico = $json2;
        $exUnico->administrativo = $json3;
        $exUnico->save();

    }

    #Guardar el formulario en la base de datos
    public function guardar(Request $request) {
        $valores_form = $request->valor_form;
        $rol_user = $request->rol_user;
        $idcurso = $request->idcurso;
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo')->where('id', '=', $idcurso)->first();
        $txtarea25 = ""; $radio25 = null;
        // $doc_abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K','L', 'M', 'N', 'O', 'P'];


        #VINCULACION
        if($rol_user == 1){
            $doc_numeros = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'A.1'];
            $conta_abced = 0;
            $docs_true = [];
            for ($i=1; $i <= 8; $i++) {
                if (isset($valores_form['radio'.$i])) {
                    $radio_v = $valores_form['radio'.$i];
                    $doc_v = $bd_json->vinculacion['doc_'.$i]['url_documento'];

                    if($radio_v === 'si'){
                        if(empty($doc_v)){
                            // return response()->json(['mensaje' => "¡En el documento ( $doc_numeros[$conta_abced] ) no puede seleccionar ( SI ) ya que el documento no existe!"]);
                        }
                    }else if($radio_v === 'no' || $radio_v === 'no_aplica') {
                        if(!empty($doc_v)){
                            $docs_true[] = $i;
                        }
                    }
                }else{
                    return response()->json(['mensaje' => 'FALTAN CAMPOS POR SELECCIONAR, ¡VERIFIQUE!']);
                }
                $conta_abced ++; #Consecutivo del abecedario
            }
            //Guardamos valores de vinculacion
            try {
                $expeUnico = ExpeUnico::find($idcurso);
                $json = $expeUnico->vinculacion;
                for ($i=1; $i <= 8; $i++) {
                    if(in_array($i, $docs_true)){$json['doc_'.$i]['existe_evidencia'] = 'si';}
                    else{$json['doc_'.$i]['existe_evidencia'] = $valores_form['radio'.$i];}
                    $json['doc_'.$i]['observaciones'] = $valores_form['txtarea'.$i];
                }
                $json['status_save'] = true;
                $json['id_user_save'] = Auth::user()->id;
                $json['fecha_guardado'] = date('Y-m-d H:i');
                $expeUnico->vinculacion = $json;
                $expeUnico->save();

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Ocurrió un error al realizar la inserción de datos',
                    'error' => $th->getMessage()
                ]);
            }
        #ACADEMICO
        }else if($rol_user == 2){
            $doc_numeros = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'G.1'];
            $conta_abced = 0;
            $docs_true = [];
            $indice_docs = [8,9,10,11,12,13,14,15,16,17,18,19,25];
            for ($i=0; $i < count($indice_docs); $i++) {
                if(isset($valores_form['radio'.$indice_docs[$i]])){ #Validamos si el radio contiene un valor
                    $radio_a = $valores_form['radio'.$indice_docs[$i]];
                    $doc_a = $bd_json->academico['doc_'.$indice_docs[$i]]['url_documento'];
                    $text = $valores_form['txtarea'.$indice_docs[$i]];

                    if($radio_a === 'si'){
                        if(empty($doc_a)){
                            if($indice_docs[$i] == 17 || $indice_docs[$i] == 18){
                                if(empty($text)){
                                    // return response()->json(['mensaje' => "¡En el documento ( $doc_numeros[$conta_abced] ) debes agregar el link del archivo!"]);
                                }
                            }else{
                                // return response()->json(['mensaje' => "¡En el documento ( $doc_numeros[$conta_abced] ) no puede seleccionar ( SI ) ya que el documento no existe!"]);
                            }
                        }
                    }else if($radio_a === 'no' || $radio_a === 'no_aplica') {
                        if($indice_docs[$i] == 17 || $indice_docs[$i] == 18){
                            if(!empty($doc_a) || !empty($text)){
                                $docs_true[] = $indice_docs[$i];
                            }
                        }else{
                            if(!empty($doc_a)){
                                $docs_true[] = $indice_docs[$i];
                            }
                        }
                    }
                }else{
                    return response()->json(['mensaje' => 'FALTAN CAMPOS POR SELECCIONAR, ¡VERIFIQUE!']);
                }
                $conta_abced ++;
            }

            //Guardamos valores de academico
            try {
                $expeUnico = ExpeUnico::find($idcurso);
                $json = $expeUnico->academico;
                for ($i=0; $i < count($indice_docs); $i++) {
                    if(in_array($indice_docs[$i], $docs_true)){$json['doc_'.$indice_docs[$i]]['existe_evidencia'] = 'si';}
                    else{$json['doc_'.$indice_docs[$i]]['existe_evidencia'] = $valores_form['radio'.$indice_docs[$i]];}
                    $json['doc_'.$indice_docs[$i]]['observaciones'] = $valores_form['txtarea'.$indice_docs[$i]];
                }
                $json['status_save'] = true;
                $json['id_user_save'] = Auth::user()->id;
                $json['fecha_guardado'] = date('Y-m-d H:i');
                $expeUnico->academico = $json;
                $expeUnico->save();
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Ocurrió un error al realizar la inserción de datos',
                    'error' => $th->getMessage()
                ]);
            }

        #ADMINISTRATIVO
        }else if($rol_user == 3){
            $doc_numeros = ['A', 'B', 'C', 'D', 'E'];
            $conta_abced = 0;
            $docs_true = [];
            for ($i=20; $i <= 24; $i++) {
                if(isset($valores_form['radio'.$i])){
                    $radio_d = $valores_form['radio'.$i];
                    $doc_d = $bd_json->administrativo['doc_'.$i]['url_documento'];

                    if($radio_d === 'si'){
                        if(empty($doc_d)){
                            // return response()->json(['mensaje' => "¡En el documento ( $doc_numeros[$conta_abced] ) no puede seleccionar ( SI ) ya que el documento no existe!"]);
                        }
                    }else if($radio_d === 'no' || $radio_d === 'no_aplica') {
                        if(!empty($doc_d)){
                            $docs_true[] = $i;
                        }
                    }

                }else{
                    return response()->json([
                        'status' => 'VALOR DE RADIOBUTTON INDEFINIDO',
                        'mensaje' => 'FALTAN DATOS POR SELECCIONAR'
                    ]);
                }
                $conta_abced ++;
            }
            //Guardamos valores de administrativo
            try {
                $expeUnico = ExpeUnico::find($idcurso);
                $json = $expeUnico->administrativo;
                for ($i=20; $i <= 24; $i++) {
                    if(in_array($i, $docs_true)){$json['doc_'.$i]['existe_evidencia'] = 'si';}
                    else{$json['doc_'.$i]['existe_evidencia'] = $valores_form['radio'.$i];}
                    $json['doc_'.$i]['observaciones'] = $valores_form['txtarea'.$i];
                }

                $json['status_save'] = true;
                $json['id_user_save'] = Auth::user()->id;
                $json['fecha_guardado'] = date('Y-m-d H:i');
                $expeUnico->administrativo = $json;
                $expeUnico->save();
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Ocurrió un error al realizar la inserción de datos',
                    'error' => $th->getMessage()
                ]);
            }
        }
        #GUARDAMOS LOS VALORES
        return response()->json([
            'status' => 200,
            'mensaje' => '¡DATOS GUARDADOS CON ÉXITO!'
        ]);
    }

    /** Funcion para subir pdf al servidor
     * @param string $pdf, $id, $nom
     */
    protected function pdf_upload($pdf, $id, $nom, $anio)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom . "_" . date('YmdHis') . "_" . $id . ".pdf");
        $directorio = '/' . $anio . '/expedientes/' . $id . '/'.$pdfFile;
        $pdf->storeAs('/uploadFiles/'.$anio.'/expedientes/'.$id, $pdfFile);
        $pdfUrl = Storage::url('/uploadFiles' . $directorio);
        return [$pdfUrl, $directorio];
    }

    /**
     * Realizamos la subida de pdf
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadpdfs(Request $request)
    {
        #Consulta de imagenes a los json
        $idcurso = $request->id_curso;
        $rol = $request->rol;
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo')->where('id', '=', $idcurso)->first();
        $db_anio = DB::table('tbl_cursos')->selectRaw('EXTRACT(YEAR FROM inicio) as anio')->where('id', $idcurso)->first();
        $anio = $db_anio->anio;

        #VINCULACION
        if($rol == '1'){
            $num_docs = [1,3,4,8]; #Documentos que se requieren obtener
            for ($i=0; $i < count($num_docs) ; $i++) {
                ${"file" . $num_docs[$i]} = $request->hasFile('doc_'.$num_docs[$i]);
                ${"img" . $num_docs[$i]} = basename($bd_json->vinculacion['doc_'.$num_docs[$i]]['url_documento']);
            }
            $nombres_doc = ['acta_acuerdo', 'soli_apertura', 'sid01', 'sop_manifiesto'];

            try {
                for ($i=0; $i < count($num_docs) ; $i++) {
                    #Validamos si existe el archivo para empezar con el proceso
                    if (${"file" . $num_docs[$i]} == true){
                        if(${"img" . $num_docs[$i]} != ''){
                            #Reemplazar
                            $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.${"img" . $num_docs[$i]};
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            } else { return response()->json(['status' => 500, 'mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO ->".$filePath]); }
                        }
                        #Agregar Registros el doc20 es el doc25  del json
                        $vinc = ExpeUnico::find($idcurso);
                        $doc = $request->file('doc_'.$num_docs[$i]); # obtenemos el archivo
                        $urldoc = $this->pdf_upload($doc, $idcurso, $nombres_doc[$i], $anio); # invocamos el método
                        $url = $vinc->vinculacion;

                        $url['doc_'.$num_docs[$i]]['url_documento'] = $urldoc[1];
                        $url['doc_'.$num_docs[$i]]['fecha_subida'] = date('Y-m-d');

                        $vinc->vinculacion = $url; # guardamos el path
                        $vinc->iduser_updated = Auth::user()->id;
                        $vinc->save();
                    }
                }
                return response()->json([
                    'status' => 200,
                    'mensaje' => 'ARCHIVOS CARGADOS CON EXITO',
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Error al intentar guardar los archivos',
                    'error' => $th->getMessage()
                ]);
            }
        }

        #ACADEMICO
        if($rol == '2'){
            #Creamos variables que contiene los archivos Nota: la ultima es la doc_25 json
            for ($i=12; $i <= 20 ; $i++) {
                ${"file" . $i} = $request->hasFile('doc_'.$i);
                // ${"img" . $i} = basename($bd_json->academico['doc_'.$i]['url_documento']);
                if($i < 20) ${"img" . $i} = basename($bd_json->academico['doc_'.$i]['url_documento']);
                else ${"img" . $i} = basename($bd_json->academico['doc_25']['url_documento']);
            }
            $nombres_doc = ['riacd02ins', 'riacd02acre', 'riacd02cert', 'lad04asis', 'resd05aval',
            'eval_alumn', 'eval_doce', 'repo_foto', 'sop_const'];
            //'sop_const'

            try {
                $conta = 0;
                for ($i=12; $i <= 20 ; $i++) {
                    #Validamos si existe el archivo para empezar con el proceso
                    if (${"file" . $i} == true){
                        if(${"img" . $i} != ''){
                            #Reemplazar
                            $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.${"img" . $i};
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            } else { return response()->json(['status' => 500, 'mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO ->".$filePath]); }
                        }
                        #Agregar Registros el doc20 es el doc25  del json
                        $acad = ExpeUnico::find($idcurso);
                        $doc = $request->file('doc_'.$i); # obtenemos el archivo
                        $urldoc = $this->pdf_upload($doc, $idcurso, $nombres_doc[$conta], $anio); # invocamos el método
                        $url = $acad->academico;

                        if($i == 20){
                            $url['doc_25']['url_documento'] = $urldoc[1];
                            $url['doc_25']['fecha_subida'] = date('Y-m-d');
                        }else{
                            $url['doc_'.$i]['url_documento'] = $urldoc[1];
                            $url['doc_'.$i]['fecha_subida'] = date('Y-m-d');
                        }
                        $acad->academico = $url; # guardamos el path
                        $acad->iduser_updated = Auth::user()->id;
                        $acad->save();
                    }
                    $conta ++;
                }
                return response()->json([
                    'status' => 200,
                    'mensaje' => 'ARCHIVOS CARGADOS CON EXITO',
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Error al intentar guardar los archivos',
                    'error' => $th->getMessage()
                ]);
            }
        }

        #DELEGADO
        if ($rol == '3') {
            $num_docs = [22,23]; #Documentos que se requieren obtener
            for ($i=0; $i < count($num_docs) ; $i++) {
                ${"file" . $num_docs[$i]} = $request->hasFile('doc_'.$num_docs[$i]);
                ${"img" . $num_docs[$i]} = basename($bd_json->administrativo['doc_'.$num_docs[$i]]['url_documento']);
            }
            $nombres_doc = ['contrato', 'solicitud_pago'];

            try {
                for ($i=0; $i < count($num_docs) ; $i++) {
                    #Validamos si existe el archivo para empezar con el proceso
                    if (${"file" . $num_docs[$i]} == true){
                        if(${"img" . $num_docs[$i]} != ''){
                            #Reemplazar
                            $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.${"img" . $num_docs[$i]};
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            } else { return response()->json(['status' => 500, 'mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO ->".$filePath]); }
                        }
                        #Agregar Registros el doc20 es el doc25  del json
                        $deleg = ExpeUnico::find($idcurso);
                        $doc = $request->file('doc_'.$num_docs[$i]); # obtenemos el archivo
                        $urldoc = $this->pdf_upload($doc, $idcurso, $nombres_doc[$i], $anio); # invocamos el método
                        $url = $deleg->administrativo;

                        $url['doc_'.$num_docs[$i]]['url_documento'] = $urldoc[1];
                        $url['doc_'.$num_docs[$i]]['fecha_subida'] = date('Y-m-d');

                        $deleg->administrativo = $url; # guardamos el path
                        $deleg->iduser_updated = Auth::user()->id;
                        $deleg->save();
                    }
                }
                return response()->json([
                    'status' => 200,
                    'mensaje' => 'ARCHIVOS CARGADOS CON EXITO',
                ]);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 500,
                    'mensaje' => 'Error al intentar guardar los archivos',
                    'error' => $th->getMessage()
                ]);
            }
        }

    }

    /**Funcion para eliminar PDF */
    public function deletpdfs(Request $request){
        $partImg = basename($request->urlImg);
        $rol = $request->rol_user;
        $idcurso = $request->idcurso;
        $radio = str_replace('opcion', '', $request->radio);
        $json_dptos = ExpeUnico::select('vinculacion','academico','administrativo')->where('id_curso', $idcurso)->first();
        $st_acad = '';
        $val_doc = "";
        #Validamos los radiobutton para ver si le corresponde eliminar de acuerdo al rol
        if ($rol == '1' && isset($json_dptos->vinculacion['doc_'.$radio]['nom_doc'])) {
            $st_acad = $json_dptos->vinculacion['status_dpto'];
            $val_doc = "existe";
        }
        else if ($rol == '2' && isset($json_dptos->academico['doc_'.$radio]['nom_doc'])) {
            $st_acad = $json_dptos->academico['status_dpto'];
            $val_doc = "existe";
        }
        else if ($rol == '3' && isset($json_dptos->administrativo['doc_'.$radio]['nom_doc'])) {
            $st_acad = $json_dptos->administrativo['status_dpto'];
            $val_doc = "existe";
        }


        if(($rol == '1' || $rol == '2' || $rol == '3') && $val_doc == "existe"){
            if($st_acad == 'CAPTURA' || $st_acad == 'RETORNADO'){
                $this->proced_del_doc($rol, $idcurso, $partImg, $radio);
            }else{
                return response()->json([
                    'status' => 500,
                    'mensaje' => '¡NO ES POSIBLE ELIMINAR EL ARCHIVO DEBIDO AL CAMBIO DE STATUS!'
                ]);
            }
        }else{
            return response()->json([
                'status' => 500,
                'mensaje' => '¡NO CUENTAS CON LOS PERMISOS SUFICIENTES PARA ELIMINAR EL ARCHIVO!'
            ]);
        }
        return response()->json([
            'status' => 200,
            'mensaje' => '¡EL ARCHIVO SE HA ELIMINADO CORRECTAMENTE!'
        ]);
    }
    //Tiene relacion con el proceso de guardado de imagenes
    public function proced_del_doc($rol, $idcurso, $url, $radio){
        $db_anio = DB::table('tbl_cursos')->selectRaw('EXTRACT(YEAR FROM inicio) as anio')->where('id', $idcurso)->first();
        $anio = $db_anio->anio;
        if($url != ''){
            #Reemplazar
            $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.$url;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            } else { return response()->json(['status' => 500, 'mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
            #Guardamos en la bd
            try {
                $json = ExpeUnico::find($idcurso);
                if($rol == '1') $url = $json->vinculacion;
                else if($rol == '2') $url = $json->academico;
                else if($rol == '3') $url = $json->administrativo;
                $url['doc_'.$radio]['url_documento'] = '';
                $url['doc_'.$radio]['existe_evidencia'] = '';
                if($rol == '1') $url = $json->vinculacion = $url;
                else if($rol == '2') $json->academico = $url;
                else if($rol == '3') $json->administrativo = $url;
                // $json->vinculacion = $url; # guardamos el path
                $json->save();
            } catch (\Throwable $th) {
                return response()->json([
                    'mensaje' => "¡ERROR AL INTENTAR ELIMINAR DATOS DEL ARCHIVO!"]);
            }

        }

    }

    //Cambio de estatus a Enviado para DTA
    public function validar_form(Request $request)  {
        // $rol_user = $request->rol_user;
        $idcurso = $request->idcurso;
        $json_dptos = ExpeUnico::select('vinculacion', 'academico', 'administrativo')->where('id_curso', $idcurso)->first();
        $status_vincu = $json_dptos->vinculacion['status_save'];
        $status_acad = $json_dptos->academico['status_save'];
        $status_admin = $json_dptos->administrativo['status_save'];

        //Validamos si el curso esta reportado para proceder con el envio de información de lo contrario se le mandara un mensaje.
        $exists = DB::table('tbl_cursos')->where('id', $idcurso)->where('status', '!=', 'NO REPORTADO')
        ->where('status', '!=', 'CANCELADO')->whereNotNull('status')->exists();

        if($exists == false){
            return response()->json([
                'status' => 500,
                'mensaje' => '¡EL ESTATUS DEL CURSO ESTA COMO NO REPORTADO O CANCELADO, NO ES POSIBLE ENVIAR LA INFORMACIÓN A DTA!',
            ]);
        }

        try {
            $expeUnico = ExpeUnico::find($idcurso);
            if($status_vincu == true && $status_acad == true && $status_admin == true){
                $json1 = $expeUnico->vinculacion;
                $json2 = $expeUnico->academico;
                $json3 = $expeUnico->administrativo;
                $json1['status_dpto'] = 'ENVIADO';
                $json2['status_dpto'] = 'ENVIADO';
                $json3['status_dpto'] = 'ENVIADO';
                $json1['fecha_envio_dta'] = date('Y-m-d H:i');
                $json2['fecha_envio_dta'] = date('Y-m-d H:i');
                $json3['fecha_envio_dta'] = date('Y-m-d H:i');
                $expeUnico->vinculacion = $json1;
                $expeUnico->academico = $json2;
                $expeUnico->administrativo = $json3;
                $expeUnico->save();
            }else{
                return response()->json(['mensaje' => '¡ALGUN DEPARTAMENTO HACE FALTA QUE CARGUE SUS DATOS!']);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'mensaje' => 'Ocurrió un error al realizar la acción',
                'error' => $th->getMessage()
            ]);
        }

        return response()->json([
            'status' => 200,
            'mensaje' => '¡DATOS ENVIADOS A DTA!',
        ]);

    }

    //Cambio de estatus a Validado por DTA
    public function validar_dta (Request $request){
        $rol = $request->rol;
        $idcurso = $request->idcurso;
        // $txtarea = $request->valor_area;
        $accion = $request->accion;
        $mensajes_dta = $request->mensajes_dta;

        try {
            $expeUnico = ExpeUnico::find($idcurso);
            $json1 = $expeUnico->vinculacion;
            $json2 = $expeUnico->academico;
            $json3 = $expeUnico->administrativo;
            if($accion == 'validar'){
                $json1['status_dpto'] = 'VALIDADO';
                $json1['fecha_validado'] = date('Y-m-d H:i');
                $json1['id_user_valid'] = Auth::user()->id;
                for ($i=1; $i <= 8; $i++) {$json1['doc_'.$i]['mensaje_dta'] = "";}

                $json2['status_dpto'] = 'VALIDADO';
                $json2['fecha_validado'] = date('Y-m-d H:i');
                $json2['id_user_valid'] = Auth::user()->id;
                for ($i=8; $i <= 19; $i++) {$json2['doc_'.$i]['mensaje_dta'] = "";}
                $json2['doc_25']['mensaje_dta'] = "";

                $json3['status_dpto'] = 'VALIDADO';
                $json3['fecha_validado'] = date('Y-m-d H:i');
                $json3['id_user_valid'] = Auth::user()->id;
                for ($i=20; $i <= 24; $i++) {$json3['doc_'.$i]['mensaje_dta'] = "";}

            }else if($accion == 'retornar'){
                $json1['status_dpto'] = 'RETORNADO';
                $json1['fecha_retornado'] = date('Y-m-d H:i');
                $json1['id_user_return'] = Auth::user()->id;
                // $json1['descrip_return'] = $txtarea;
                for ($i=1; $i <= 8; $i++) {
                    $index = ($i == 8) ? 26 : $i;
                    $json1['doc_'.$i]['mensaje_dta'] = (!empty($mensajes_dta['txtarea'.$index])) ? $mensajes_dta['txtarea'.$index] : "";
                }

                $json2['status_dpto'] = 'RETORNADO';
                $json2['fecha_retornado'] = date('Y-m-d H:i');
                $json2['id_user_return'] = Auth::user()->id;
                // $json2['descrip_return'] = $txtarea;
                for ($i=8; $i <= 19; $i++) {
                    $json2['doc_'.$i]['mensaje_dta'] = (!empty($mensajes_dta['txtarea'.$i])) ? $mensajes_dta['txtarea'.$i] : "";
                }
                $json2['doc_25']['mensaje_dta'] = (!empty($mensajes_dta['txtarea25'])) ? $mensajes_dta['txtarea25'] : "";


                $json3['status_dpto'] = 'RETORNADO';
                $json3['fecha_retornado'] = date('Y-m-d H:i');
                $json3['id_user_return'] = Auth::user()->id;
                // $json3['descrip_return'] = $txtarea;
                for ($i=20; $i <= 24; $i++) {
                    $json3['doc_'.$i]['mensaje_dta'] = (!empty($mensajes_dta['txtarea'.$i])) ? $mensajes_dta['txtarea'.$i] : "";
                }
            }
            $expeUnico->vinculacion = $json1;
            $expeUnico->academico = $json2;
            $expeUnico->administrativo = $json3;
            $expeUnico->save();

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'mensaje' => 'Ocurrió un error al realizar la acción',
                'error' => $th->getMessage()
            ]);
        }

        return response()->json([
            'status' => 200,
            'arreglo' => $mensajes_dta,
            'mensaje' => '¡INFORMACIÓN '.($accion == 'validar' ? 'VALIDADA' : 'RETORNADA').'!'
        ]);
    }

    //Generar PDF Expedientes Unicos
    public function pdf_expediente($idcurso){
        $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
        // $distintivo = "Lista de verificación de Expediente Único";
        $direccion = DB::table('tbl_instituto')->WHERE('id', 1)->VALUE('direccion');
        $json_dptos = ExpeUnico::select('vinculacion', 'academico', 'administrativo')->where('id_curso', $idcurso)->first();
        $st_vinc = $json_dptos->vinculacion['status_save'];
        $st_acad = $json_dptos->academico['status_save'];
        $st_admin = $json_dptos->administrativo['status_save'];
        if($st_vinc && $st_acad && $st_admin)$marca = false;
        else $marca = true;
        // dd($st_vinc, $st_acad, $st_admin, $marca);

        //Agregamos los textos por departamento para que todo salga correcto
        $evid_vincu = [
            'doc1'=>'Convenio Especifico / Acta de acuerdo.',
            'doc2'=>'Copia de autorización de Exoneración y/o Reducción de Cuota de Recuperación.',
            'doc3'=>'Original de la solicitud de apertura de cursos de capacitación y/o certificación al Departamento Académico.',
            'doc4'=>'SID-01 solicitud de Inscripción del interesado.',
            'doc5'=>'CURP actualizada o Copia de Acta de Nacimiento.',
            'doc6'=>'Copia de comprobante de último grado de estudios (en caso de contar con él).',
            'doc7'=>'Copia del recibo oficial de la cuota de recuperación expedido por la Delegación Administrativa y comprobante de depósito o transferencia Bancaria.'
        ];
        $evid_acad = [
            'doc8'=>'Original de memorándum ARC-01, solicitud de Apertura de cursos de capacitación y/o Certificación a la Dirección Técnica Académica.',
            'doc9'=>'Copia de memorándum de autorización de ARC-01, emitido por la Dirección Técnica Académica.',
            'doc10'=>'Original de memorándum ARC-02, solicitud de modificación, reprogramación y/o cancelación de curso a la Dirección Técnica Académica, en caso aplicable.',
            'doc11'=>'Copia de memorándum de autorización de ARC-02, emitido por la Dirección Técnica Académica, en caso aplicable.',
            'doc12'=>'Copia de RIACD-02 Inscripción.',
            'doc13'=>'Copia de RIACD-02 Acreditación.',
            'doc14'=>'Copia de RIACD-02 Certificación.',
            'doc15'=>'Copia de LAD-04 Lista de Asistencia.',
            'doc16'=>'Copia de RESD-05 Registro de Evaluación por Sub - objetivos.',
            'doc17'=>'Originales o Copia de las Evaluaciones y/o Reactivos de aprendizaje del alumno y/o resumen de actividades.',
            'doc18'=>'Original o Copia de las Evaluaciones al Docente y Evaluación del Curso y/o resumen de actividades.',
            'doc19'=>'Reporte fotográfico, como mínimo dos fotografías.'
        ];

        $evid_admin = [
            'doc20'=>'Memorándum de solicitud de Suficiencia Presupuestal.',
            'doc21'=>'Copia de formato de autorización de suficiencia Presupuestal.',
            'doc22'=>'Original de Contrato de prestación de curso de Capacitación y/o Certificación del Instructor externo, con firma autógrafa o firma electrónica.',
            'doc23'=>'Copia de memorándum de solicitud de pago al Instructor externo.',
            'doc24'=>'Comprobante Fiscal Digital por Internet del Instructor externo.'
        ];

        $curso = DB::table('tbl_cursos')->select('tipo_curso', 'curso', 'tcapacitacion', 'clave', 'folio_grupo',
        'nombre', 'espe', 'unidad', 'costo', 'inicio', 'termino', 'hini', 'hfin',
        DB::raw("CASE
            WHEN tipo = 'EXO' THEN 'EXONERACIÓN DE CUOTA'
            WHEN tipo = 'PINS' THEN 'CUOTA ORDINARIA'
            WHEN tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA'
        END as tpago"))->where('id', $idcurso)->first();
        $abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        $pdf = PDF::loadView('vistas_expe.genpdfexpedientes',compact('direccion','distintivo','json_dptos','abecedario','curso','marca',
        'evid_vincu','evid_acad','evid_admin'));
        return $pdf->stream('Expediente_Unico');


    }

    //Guardar datos de alumnos
    public function requisitos_alumnos(Request $request){
        $folioG = $request->input('folioG');
        $alumnosIds = $request->input('alumnosId', []);
        $docAlumnos = $request->input('docAlumnos', []);
        $idsPre = $request->input('identPre', []);
        $CheckboxCurp = json_decode($request->input('checksCurp'));
        $CheckboxEstudios = json_decode($request->input('checksEstudios'));
        $CheckboxActaNacim = json_decode($request->input('checksActaNacim'));
        $documentos = $request->file('documentos');

        // Insertar documento
        if(!is_null($documentos)){
            foreach ($documentos as $idAlumno => $file) {
                if($file){
                    $link = $docAlumnos[$idAlumno]; //Link del documento
                    $id_pre = $idsPre[$idAlumno];
                    $namePdf = basename($link);
                    if(empty($namePdf)){$namePdf = '';}
                    else{
                        $filePath = 'uploadFiles/alumnos/'.$id_pre.'/'.$namePdf;
                        if (Storage::exists($filePath)) { Storage::delete($filePath);} //Eliminamos el archivo
                    }
                    #Guardamos url del archivo
                    try {
                        $Alumnos = Inscripcion::find($idAlumno);
                        $doc = $file; # obtenemos el archivo
                        $urldoc = $this->pdf_upload_alumnos($doc, $id_pre, $namePdf); # invocamos el método
                        $url = $Alumnos->requisitos;
                        $url['documento'] = $urldoc[0];
                        $Alumnos->requisitos = $url; # guardamos el path
                        $Alumnos->save();
                    } catch (\Throwable $th) {
                        return redirect()->route('expunico.principal.mostrar.get', ['folio' => $folioG])->with(['message' => '¡ERROR EN LA SUBIDA DE ARCHIVOS '.$th->getMessage() , 'status' => 'danger']);
                    }

                }
            }
        }

        //HACER CONSULTA POR ALUMNOS PARA TRAER LA CURP SI CHECK ESTUDIOS ES IGUAL A TRUE ENTONCES HACEMOS LA CONSULTA Y ACTUALIZAMOS POR LA CURP
        //DE LO CONTRARIO HACEMOS LA ACTUALIZACION

        //Actualizar curp o estudios
        foreach ($alumnosIds as $key => $id) {
            try {
                $Alumnos = Inscripcion::find($id);
                $json = $Alumnos->requisitos;
                $json['chk_curp'] = $CheckboxCurp[$key];
                $json['chk_escolaridad'] = $CheckboxEstudios[$key];
                $json['chk_acta_nacimiento'] = $CheckboxActaNacim[$key];
                // $json['documento'] = "";
                $Alumnos->requisitos = $json;
                $Alumnos->save();
            } catch (\Throwable $th) {
                return redirect()->route('expunico.principal.mostrar.get', ['folio' => $folioG])->with(['message' => '¡ERROR AL GUARDAR INFORMACIÓN '.$th->getMessage() , 'status' => 'danger']);
            }
        }
        return redirect()->route('expunico.principal.mostrar.get', ['folio' => $folioG])->with(['message' => '¡INFORMACION ACTUALIZADA!', 'status' => 'success']);

    }

        /** Funcion para subir pdf de alumnos
     * @param string $pdf, $id, $nom
     */
    protected function pdf_upload_alumnos($pdf, $id_pre, $pdfname)
    {
        # nuevo nombre del archivo
        if(empty($pdfname)){ $pdfname = trim("requisitos" . "_" . date('YmdHis') . "_" . $id_pre . ".pdf");}
        // $pdfFile = trim($nom . "_" . date('YmdHis') . "_" . $id . ".pdf");
        $directorio = '/alumnos/' . $id_pre . '/'.$pdfname;
        $pdf->storeAs('/uploadFiles/alumnos/'.$id_pre, $pdfname);
        $pdfUrl = Storage::url('/uploadFiles' . $directorio);
        return [$pdfUrl, $directorio];
    }

    public function upload_recibo(Request $request){

        $folio_recibo = $request->input('folio_recibo');
        $fecha_recibo = $request->input('fecha_recibo');
        $rol = $request->input('rol');
        $id_curso = $request->input('id_curso');

        $consulta = DB::table('tbl_cursos')->select('comprobante_pago', 'folio_grupo')->where('id', '=', $id_curso)->first();

        if(!empty($consulta) && !empty($folio_recibo) && !empty($id_curso) && !empty($fecha_recibo)) {
            $namePdf = basename($consulta->comprobante_pago);
            if(empty($namePdf)){ $namePdf = trim("comprobante_pago" . "_". $consulta->folio_grupo . date('YmdHis'). ".pdf");}

            //Cargar pdf
            if($request->hasFile('file')){
                try {
                    $filePath = 'uploadFiles/UNIDAD/comprobantes_pagos/'.$namePdf;
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    }
                    $pdf = $request->file('file');
                    $directorio = '/UNIDAD/comprobantes_pagos/'.$namePdf;
                    $pdf->storeAs('/uploadFiles/UNIDAD/comprobantes_pagos/', $namePdf);
                    $pdfUrl = Storage::url('/uploadFiles' . $directorio);

                    //Guardamos datos en la bd
                    DB::table('tbl_cursos')
                    ->where('id', $id_curso)
                    ->update([
                        'comprobante_pago' => $directorio,
                        'folio_pago' => $folio_recibo,
                        'fecha_pago' => $fecha_recibo
                    ]);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 500,
                        'mensaje' => 'error '.$th->getMessage()
                    ]);
                }
            }

        }
        return response()->json([
            'status' => 200,
            'mensaje' => 'pdf de recibo cargado con exito'
        ]);
    }

}
