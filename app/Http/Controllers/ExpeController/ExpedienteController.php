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
        // $this->path_pdf = "/DTA/solicitud_folios/";
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
        else if($slug == 'direccion_vinculacion' || $slug == 'unidad_vinculacion' || $slug == 'vinculadores_administrativo') {$val_rol = 1;}
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
                        //$val_rol = 3; //Ejemplo supongamos que andamos en el rol del viculador

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
        for ($i = 1; $i <= 7; $i++) {
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
        // $ruta_serv = 'https://sivyc.icatech.gob.mx/storage/uploadFiles';
        // $ruta_serv = $this->path_files;  //Esto vamos a pasarlo a la vista
        $bddoc2 = DB::table('exoneraciones')->where('folio_grupo',$folio)->value('memo_soporte_dependencia');

        #Para obtener documentos de alumnos enviamos el array para procesar
        $bddoc56 = Inscripcion::select('alumno','doc_soporte')->where('folio_grupo', '=', $folio)->get();
        $resdoc56 = $this->curp_alumnos_proces($folio, $bddoc56);

        // dd($bddoc56[0]->doc_soporte);
        $bddoc789 = DB::table('tbl_cursos')->select('comprobante_pago', 'file_arc01', 'pdf_curso', 'file_arc02')->where('folio_grupo', '=', $folio)->first();
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

        $bddoc22 = DB::table('contratos as con')->join('tbl_cursos as c', 'c.id', '=', 'con.id_curso')
        ->where('c.folio_grupo', $folio)->value('con.arch_contrato');

        $bddoc23 = DB::table('pagos as pa')->select('pa.arch_solicitud_pago')->join('tbl_cursos as c', 'c.id', '=', 'pa.id_curso')
        ->where('c.folio_grupo', '2B-231061')->first();
        $bddoc24 = $resultado = DB::table('instructores as i')->select('i.archivo_rfc')->join('tbl_cursos as c', 'c.id_instructor', '=', 'i.id')
        ->where('c.folio_grupo', $folio)->first();

        //Firma lista de asistencia tradicional y electronica
        $bdEAsis = DB::table('tbl_cursos as tc')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Lista de asistencia')
        ->where('ef.status', 'VALIDADO')->value('tc.id');

        $bdEFoto = DB::table('tbl_cursos as tc')
        ->join('documentos_firmar as ef', 'ef.numero_o_clave', '=', 'tc.clave')
        ->where('tc.folio_grupo', $folio)->where('ef.tipo_archivo', 'Reporte fotografico')
        ->where('ef.status', 'VALIDADO')->value('tc.id');

        $bdAsisEvid = DB::table('pagos as pag')->select('pag.arch_asistencia', 'pag.arch_evidencia')->join('tbl_cursos as c', 'c.id', '=', 'pag.id_curso')
        ->where('c.folio_grupo', $folio)->where('pag.status_recepcion', 'VALIDADO')->first();

        //Obtener comprobante de pago ya que se actualizaron rutas
        $bdReciboP = DB::table('tbl_recibos')->where('folio_grupo', $folio)->value('folio_recibo');


        $doc2 = $bddoc2;
        $doc5 = $resdoc56; ##Consulta de curp de alumnos
        //Validar recibo de pago en dos tablas
        if(!empty($bdReciboP)){$doc7 = $bdReciboP; $validRec = 'folio';}
        else if(!empty($bddoc789->comprobante_pago)){$doc7 = $bddoc789->comprobante_pago; $validRec = 'link';}
        else{$doc7 = ''; $validRec = '';}

        $doc8 = $bddoc789->file_arc01;
        $doc9 = $bddoc789->pdf_curso;
        $doc10 = $bddoc789->file_arc02;
        $doc11 = $bddoc789->pdf_curso;
        $doc20 = $bddoc2021->doc_supre;
        $doc21 = $bddoc2021->doc_validado;
        //Validamos contrato si no esta entonces enviamos el id del contraro para visualizarlo electronicamente
        if(!empty($bdECont)){$doc22 = $bdECont;}
        else if(!empty($bddoc22)){$doc22 = $bddoc22;}
        else{$doc22 = '';}
        // Asistencia
        if(!empty($bdEAsis)){$docAsis = $bdEAsis;}
        else if(!empty($bdAsisEvid)){$docAsis = $bdAsisEvid->arch_asistencia;}
        else{$docAsis = '';}
        //Fotografico
        if(!empty($bdEFoto)){$docFoto = $bdEFoto;}
        else if(!empty($bdAsisEvid)){$docFoto = $bdAsisEvid->arch_evidencia;}
        else{$docFoto = '';}

        $doc23 = $bddoc23->arch_solicitud_pago;
        $doc24 = $bddoc24->archivo_rfc;

        $url_docs = array(
            "urldoc2" => $doc2,"urldoc5" => $doc5,"urldoc7" => $doc7,"urldoc8" => $doc8,"urldoc9" => $doc9,"urldoc10" => $doc10,"urldoc11" => $doc11,
            "urldoc20" => $doc20,"urldoc21" => $doc21,"urldoc22" => $doc22,"urldoc23" =>$doc23,"urldoc24"=>$doc24,
            "urldoc15" =>$docAsis,"urldoc19" => $docFoto, "validRecibo"=>$validRec
        );
        $this->guardarLinks($folio, $url_docs);  #Agregar las url externas a la tabla de expedientes
        $this->proces_documentos($folio);

        return $url_docs;
    }

    #Guardar links externos de pdf cuando se realiza la busqueda externa
    public function guardarLinks($folio, $array_links){
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo', 'id')->where('folio_grupo', '=', $folio)->first();

        #Comparamos si existen en la tabla de lo contrario de guadaran en la nueva tabla
        $doc_insert_vinc = $doc_insert_aca = $doc_insert_adm = array();
        #Documentos externos
        $n_vinc = [2,7];
        $n_acad = [8,9,10,11,15,19];
        $n_adm = [20,21,22,23,24];

        #Vinculacion
        for ($i=0; $i < count($n_vinc) ; $i++) {
            $nuevoArray = [];
            if ($bd_json->vinculacion['doc_'. $n_vinc[$i]]['url_documento'] != $array_links['urldoc' . $n_vinc[$i]]) {
                $nuevoArray['doc'] = $n_vinc[$i];
                $nuevoArray['url'] = $array_links['urldoc' . $n_vinc[$i]];
                $doc_insert_vinc[] = $nuevoArray;
            }
        }
        #Academico
        for ($i=0; $i < count($n_acad) ; $i++) {
            $nuevoArray = [];
            if ($bd_json->academico['doc_'. $n_acad[$i]]['url_documento'] != $array_links['urldoc' . $n_acad[$i]]) {
                $nuevoArray['doc'] = $n_acad[$i];
                $nuevoArray['url'] = $array_links['urldoc' . $n_acad[$i]];
                $doc_insert_aca[] = $nuevoArray;
            }
        }
        #Administratiivo
        for ($i=0; $i < count($n_adm) ; $i++) {
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

    #Procesar requisitos de alumnos desde alumnos_pre
    public function curp_alumnos_proces($folio, $dtalumnos){
        $idcurso = ExpeUnico::select('id')->where('folio_grupo', $folio)->first();
        $conta_nulos = 0;

        // dd($dtalumnos[0]->doc_soporte);
        for ($i=0; $i < count($dtalumnos); $i++) {
            if($dtalumnos[$i]->doc_soporte == null){
                $conta_nulos ++;
            }
        }

        #Si contiene la misma cantidad de nulos entonces hacemos un update a tbl_incripcion y alumnos_registro
        if(count($dtalumnos) == $conta_nulos){
            try {
                #Realizamos la consulta en alumnos_pre de acuerdo al grupo
                $alumnos_pre = Alumnopre::select('alumnos_pre.id', DB::raw("alumnos_pre.requisitos->>'documento' as url_curp"))
                ->join('tbl_inscripcion', 'tbl_inscripcion.id_pre', '=', 'alumnos_pre.id')
                ->where('tbl_inscripcion.folio_grupo', $folio)
                ->get();

                #Actualizamos la tabla de expedientes unicos en doc_ur
                $expeUnicoDoc = ExpeUnico::find($idcurso->id);
                $json = $expeUnicoDoc->vinculacion;
                $json['doc_5']['url_documento'] = 'SI EXISTEN DOCUMENTOS';
                $json['doc_6']['url_documento'] = 'SI EXISTEN DOCUMENTOS';
                $expeUnicoDoc->vinculacion = $json;
                $expeUnicoDoc->save();

            } catch (\Throwable $th) {
                // dd("Error al buscar y actualizar en campo viculacion ". $th->getMessage());
                return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡ERROR AL BUSCAR Y ACTUALIZAR DOC 5 Y 6!')->with('status', 'danger');
            }

            #Actualizamos las tablas con las curp de los alumnos
            foreach ($alumnos_pre as $value) {
                try {
                    $objeto_curp = array('url' => $value['url_curp']);
                    Inscripcion::where('folio_grupo', $folio)->where('id_pre', $value['id'])->update(['doc_soporte' => $objeto_curp]);
                    Alumno::where('folio_grupo', $folio)->where('id_pre', $value['id'])->update(['doc_soporte' => $objeto_curp]);

                } catch (\Throwable $th) {
                    return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡ERROR AL MOMENTO DE ACTUALIZAR LAS CURP DE LOS ALUMNOS!')->with('status', 'danger');
                }
            }

            return Inscripcion::select('alumno','doc_soporte')->where('folio_grupo', '=', $folio)->get();
        }else{
            #Tiene documentos, por lo tanto actualizamos los campos de los docs 5 y 6
            #Actualizamos la tabla de expedientes unicos en doc_ur
            try {
                $expeUnicoDoc = ExpeUnico::find($idcurso->id);
                $json = $expeUnicoDoc->vinculacion;
                $json['doc_5']['url_documento'] = 'SI EXISTEN DOCUMENTOS';
                $json['doc_6']['url_documento'] = 'SI EXISTEN DOCUMENTOS';
                $expeUnicoDoc->vinculacion = $json;
            $expeUnicoDoc->save();
            } catch (\Throwable $th) {
                return redirect()->route('expunico.principal.mostrar.get')->with('message', '¡ERROR AL ACTUALIZAR DOC 5 Y 6!')->with('status', 'danger');
            }


        }
        return $dtalumnos;
    }

    #Agregar si de manera automatica en caso de que los documentos existan.
    public function proces_documentos($folio){
        $bd_json = ExpeUnico::select('vinculacion', 'academico', 'administrativo', 'id')->where('folio_grupo', '=', $folio)->first();
        $docs = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,25,20,21,22,23,24];

        $exUnico = ExpeUnico::find($bd_json->id);
        $json1 = $exUnico->vinculacion;
        $json2 = $exUnico->academico;
        $json3 = $exUnico->administrativo;
        for ($i=0; $i < count($docs) ; $i++){
            if($i<=6){
                if($bd_json->vinculacion['doc_'.$docs[$i]]['url_documento'] != ''){ #Validamos si hay pdf
                    $json1['doc_'.$docs[$i]]['existe_evidencia'] = 'si';
                }
            }else if($i >= 7 && $i <= 19){
                if($bd_json->academico['doc_'.$docs[$i]]['url_documento'] != ''){
                    $json2['doc_'.$docs[$i]]['existe_evidencia'] = 'si';
                }

            }else if($i >= 20){
                if($bd_json->administrativo['doc_'.$docs[$i]]['url_documento'] != ''){
                    $json3['doc_'.$docs[$i]]['existe_evidencia'] = 'si';
                }

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
        $doc_numeros= ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14'];

        #VINCULACION
        if($rol_user == 1){
            $conta_abced = 0;
            $docs_true = [];
            for ($i=1; $i <= 7; $i++) {
                if (isset($valores_form['radio'.$i])) {
                    $radio_v = $valores_form['radio'.$i];
                    $doc_v = $bd_json->vinculacion['doc_'.$i]['url_documento'];

                    if($radio_v === 'si'){
                        if(empty($doc_v)){
                            return response()->json(['mensaje' => "¡EN EL DOCUMENTO $doc_numeros[$conta_abced] NO PUEDE SELECCIONAR ( SI ) YA QUE EL DOCUMENTO NO EXISTE!"]);
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
                for ($i=1; $i <= 7; $i++) {
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
            $conta_abced = 0;
            $docs_true = [];
            $indice_docs = [8,9,10,11,12,13,14,15,16,17,18,19,25];
            for ($i=0; $i < count($indice_docs); $i++) {
                if(isset($valores_form['radio'.$indice_docs[$i]])){ #Validamos si el radio contiene un valor
                    $radio_a = $valores_form['radio'.$indice_docs[$i]];
                    $doc_a = $bd_json->academico['doc_'.$indice_docs[$i]]['url_documento'];

                    if($radio_a === 'si'){
                        if(empty($doc_a)){
                            return response()->json(['mensaje' => "¡EN EL DOCUMENTO $doc_numeros[$conta_abced] NO PUEDE SELECCIONAR ( SI ) YA QUE EL DOCUMENTO NO EXISTE!"]);
                        }
                    }else if($radio_a === 'no' || $radio_a === 'no_aplica') {
                        if(!empty($doc_a)){
                            $docs_true[] = $indice_docs[$i];
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
            $conta_abced = 0;
            for ($i=20; $i <= 24; $i++) {
                if(isset($valores_form['radio'.$i])){
                    $radio_d = $valores_form['radio'.$i];
                    $doc_d = $bd_json->administrativo['doc_'.$i]['url_documento'];

                    if($radio_d === 'si'){
                        if(empty($doc_d)){
                            return response()->json(['mensaje' => "¡EN EL DOCUMENTO $doc_numeros[$conta_abced] NO PUEDE SELECCIONAR ( SI ) YA QUE EL DOCUMENTO NO EXISTE!"]);
                        }
                    }else if($radio_d === 'no' || $radio_d === 'no_aplica') {
                        if(!empty($doc_d)){
                            return response()->json(['mensaje' => "EN EL DOCUMENTO $doc_numeros[$conta_abced] NO PUEDE SELECCIONAR ( NO / NO APLICA ) YA QUE EL DOCUMENTO EXISTE"]);
                        }
                    }
                }else{
                    return response()->json([
                        'status' => 'VALOR DE RADIOBUTTON INDEFINIDO',
                        'mensaje' => 'FALTAN DATOS POR REGISTRAR'
                    ]);
                }
                $conta_abced ++;
            }
            //Guardamos valores de administrativo
            try {
                $expeUnico = ExpeUnico::find($idcurso);
                $json = $expeUnico->administrativo;
                for ($i=20; $i <= 24; $i++) {
                    $json['doc_'.$i]['existe_evidencia'] = $valores_form['radio'.$i];
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
            $num_docs = [1,3,4]; #Documentos que se requieren obtener
            for ($i=0; $i < count($num_docs) ; $i++) {
                ${"file" . $num_docs[$i]} = $request->hasFile('doc_'.$num_docs[$i]);
                ${"img" . $num_docs[$i]} = basename($bd_json->vinculacion['doc_'.$num_docs[$i]]['url_documento']);
            }
            $nombres_doc = ['acta_acuerdo', 'soli_apertura', 'sid01'];

            try {
                $conta = 0;
                for ($i=0; $i < count($num_docs) ; $i++) {
                    #Validamos si existe el archivo para empezar con el proceso
                    if (${"file" . $num_docs[$i]} == true){
                        if(${"img" . $num_docs[$i]} != ''){
                            #Reemplazar
                            $filePath = 'uploadFiles/'.$anio.'/expedientes/'.$idcurso.'/'.${"img" . $num_docs[$i]};
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            } else { return response()->json(['mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO ->".$filePath]); }
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
                            } else { return response()->json(['mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO ->".$filePath]); }
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


        if(($rol == '1' || $rol == '2') && $val_doc == "existe"){
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
            } else { return response()->json(['mensaje' => "¡ERROR!, DOCUMENTO NO ENCONTRADO"]); }
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
                    'mensaje' => "¡ERROR AL INTENTAR GUARDAR REGISTROS DEL ARCHIVO ELIMINADO!"]);
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
                for ($i=1; $i <= 7; $i++) {$json1['doc_'.$i]['mensaje_dta'] = "";}

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
                for ($i=1; $i <= 7; $i++) {
                    $json1['doc_'.$i]['mensaje_dta'] = (!empty($mensajes_dta['txtarea'.$i])) ? $mensajes_dta['txtarea'.$i] : "";
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

        $curso = DB::table('tbl_cursos')->select('tipo_curso', 'curso', 'tcapacitacion', 'clave', 'folio_grupo',
        'nombre', 'espe', 'unidad', 'costo', 'inicio', 'termino', 'hini', 'hfin',
        DB::raw("CASE
            WHEN tipo = 'EXO' THEN 'EXONERACIÓN DE CUOTA'
            WHEN tipo = 'PINS' THEN 'CUOTA ORDINARIA'
            WHEN tipo = 'EPAR' THEN 'REDUCCIÓN DE CUOTA'
        END as tpago"))->where('id', $idcurso)->first();
        $abecedario = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        $pdf = PDF::loadView('vistas_expe.genpdfexpedientes',compact('direccion', 'distintivo', 'json_dptos', 'abecedario', 'curso','marca'));
        return $pdf->stream('Expediente_Unico');


    }

}
