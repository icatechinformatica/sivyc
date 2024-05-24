<?php

namespace App\Http\Controllers\Grupos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use App\Models\Unidad;
use App\Models\FirmaElectronica\EfoliosAlumnos;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Http;
use App\Models\Tokens_icti;
use DateTime;
use Carbon\Carbon;

class asignarfoliosController extends Controller
{
    function __construct() {
        session_start();
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->ubicacion = Unidad::where('id',$this->user->unidad)->value('ubicacion'); //si fallas camabiar ubicacion=> unidad
           if($this->user->roles[0]->slug =="admin")
                $this->unidades = Unidad::orderby('unidad')->pluck('unidad','unidad');
            else
                $this->unidades = Unidad::where('ubicacion',$this->ubicacion)->orderby('unidad')->pluck('unidad','unidad');
            return $next($request);
        });
    }

    public function index(Request $request){
        $curso = $alumnos = $message = $acta = $matricula = $efirma = $clave =  null;
        $btn_genxml = false;

        if(session('clave')) $clave = session('clave');
        else $clave = $request->clave;

        if(session('matricula')) $matricula = session('matricula');
        else $matricula = $request->matricula;

        if(session('efirma')) $efirma = session('efirma');
        else $efirma = $request->efirma;

        // dd($efirma);
        if(!is_null($efirma) && !is_null($clave)){
            // dd("boton");
            $consulta = DB::table('tbl_folios as tf')
            ->select('tf.id', 'tf.motivo', 'bf.mod', 'ef.status_doc')
            ->join('tbl_cursos as tc', 'tc.id', '=', 'tf.id_curso')
            ->leftJoin('efolios_alumnos as ef', 'ef.efolio', '=', 'tf.folio')
            ->join('tbl_banco_folios as bf', 'bf.id', 'tf.id_banco_folios')
            ->where('bf.mod', 'EFIRMA')
            ->where('tc.clave', '=', $clave)
            ->where('tf.motivo', '=', 'ACREDITADO');
            if (!is_null($matricula)) {
                $consulta->where('tf.matricula', '=', $matricula);
            }
            $verif_efirma = $consulta->get();

            if(!is_null($verif_efirma)) {
                foreach ($verif_efirma as $val) {
                    if(is_null($val->status_doc) || $val->status_doc  == 'cancelado'){
                        $btn_genxml = true;
                        break;
                    }
                }
            }
        }

        if($clave){
            $data = $this->validaCurso($clave, $matricula, NULL, $efirma);
            list($curso, $acta, $alumnos, $message) = $data;
        }
        return view('grupos.asignarfolios.index', compact('curso','alumnos','message','acta', 'matricula','efirma','clave', 'btn_genxml'));
    }

    public function store(Request $request) {
        $id_afolio = $request->id_afolio*1;
        $clave =  $request->clave;
        $matricula = $request->matricula;
        $efirma = $request->efirma;
        $data = $this->validaCurso($clave, $matricula, $id_afolio, $efirma);
        list($curso, $acta, $alumnos_out, $message) = $data; //var_dump($acta);exit;

        if($acta AND !$message){
            $id_curso = $curso->id;
            $num_folio = $acta->num_inicio+$acta->contador; //echo $num_folio;exit;
            $fecha_expedicion = $curso->termino;

            $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','f.folio','f.fecha_expedicion','f.movimiento','f.motivo',
            DB::raw('(select count(id) from tbl_folios where i.id_curso = tbl_folios.id_curso and i.matricula = tbl_folios.matricula) as total_expedidos'))
                    ->where('i.status','INSCRITO')->leftjoin('tbl_folios as f','f.id','i.id_folio');
                    if($matricula)$alumnos = $alumnos->where('i.matricula',$matricula);
                    $alumnos = $alumnos->where('i.id_curso',$id_curso)->orderby('i.alumno')->get();

                   // var_dump($alumnos);exit;
            foreach($alumnos as $a){  //var_dump($a);exit;
                if($num_folio<=$acta->num_fin){
                    if((!$a->folio AND $a->calificacion !="NP") OR ($a->movimiento=="CANCELADO" AND $a->reexpedicion==false))  {

                        $motivo= "ACREDITADO";
                        if($a->total_expedidos>=1){
                            $reexpedicion=true;
                            if($a->motivo=='ROBO O EXTRAVIO' OR $a->motivo=='NO SOLICITADO')$movimiento='DUPLICADO';
                            else $movimiento='REEXPEDIDO';
                        }else{
                            $reexpedicion=false;
                             $movimiento = "EXPEDIDO";
                        }

                        if($acta->mod=="EXT") $prefijo = "D";
                        elseif($acta->mod=="CAE") $prefijo = "C";
                        elseif($acta->mod=="EFIRMA"){
                             $prefijo = substr($this->ubicacion, 0, 3);
                        }else $prefijo = "A";

                        $folio = $prefijo.str_pad($num_folio, 6, "0", STR_PAD_LEFT);

                        $id_folio = DB::table('tbl_folios')->insertGetId(
                            ['unidad' => $curso->unidad, 'id_curso'=>$curso->id,'matricula'=>$a->matricula, 'nombre'=>$a->alumno,
                                'folio' => $folio, 'movimiento'=> $movimiento, 'motivo' => $motivo, 'mod'=> $curso->mod, 'fini' => $acta->finicial, 'ffin' => $acta->ffinal, 'focan' => 0,
                                'fecha_acta' => $acta->facta, 'fecha_expedicion' => $fecha_expedicion, 'id_unidad' => $acta->id_unidad, 'id_banco_folios' => $acta->id,
                                'iduser_created' => Auth::user()->id, 'realizo'=>Auth::user()->name,'created_at'=>date('Y-m-d H:i:s'), 'updated_at'=>date('Y-m-d H:i:s')
                                ]
                         );

                         $data = ['reexpedicion' => $reexpedicion, 'iduser_updated' => Auth::user()->id];
                         if($movimiento!='DUPLICADO') $data['id_folio']= $id_folio;
                         $resultAlumno = DB::table('tbl_inscripcion')->where('id',$a->id)->update($data);


                        if($id_folio){
                                DB::table('tbl_banco_folios')->where('id',$acta->id)->increment('contador');
                                $message = "Operacion exitosa!!";
                        }
                        $num_folio++;
                    }

                }else $message = "El folio final ha sido asignado!!";
            }
        }
        $efirma = $request->efirma;
        return redirect('grupos/asignarfolios')->with(['msn'=>$message, 'clave'=>$clave, 'matricula'=>$matricula, 'efirma' => $efirma]);
    }

    private function validaCurso($clave, $matricula, $id_afolio, $efirma){
        $curso = $alumnos = $message = $acta = NULL;
        if($clave){
            //EXISTE EL CURSO
            $curso = DB::table('tbl_cursos')->where('clave',$clave);
                $curso = $curso->whereIn('unidad',$this->unidades);
                $curso = $curso->first();


            if($curso){

                ///ACTA CON FOLIOS DISPONIBLES
                if( $efirma){
                    $unidad = $this->ubicacion;
                    $mod[] = "EFIRMA";
                }else{
                    if($curso->mod=="EXT" OR $curso->mod=="CAE" ) $mod[] = $curso->mod;
                    $mod[] = "GRAL";
                    $unidad = $curso->unidad;
                }

                //dd($unidad);
                $acta =  DB::table('tbl_banco_folios')
                    ->select('*',DB::RAW("CONCAT(substr(finicial,1,1),lpad((num_inicio+contador)::text, 6, '0')) as folio_disponible"))
                    ->where('unidad',$unidad)->wherein('mod',$mod)
                    ->where('activo',true)->whereColumn('contador','<','total');
                    if($id_afolio){
                        $acta =  $acta->where('id',$id_afolio)->first(); //solo un folio
                        if(!$acta) $message = "No hay Acta con Folios disponibles, realice su solicitud a la DTA. ";
                    }else{
                        $acta =  $acta->orderby('id')->get(); //todos los folios
                        if(count($acta)==0) $message = "No hay Acta con Folios disponibles, realice su solicitud a la DTA. ";
                    }

                ///ALUMNOS REGISTRADOS
                $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','i.id_folio as id_folioi','f.folio','f.fecha_expedicion','f.movimiento','f.motivo','f.id as id_foliof',
                    DB::raw('(select count(id) from tbl_folios where i.id_curso = tbl_folios.id_curso and i.matricula = tbl_folios.matricula) as total_expedidos'))
                    ->where('i.status','INSCRITO');

                    if($matricula)$alumnos = $alumnos->where('i.matricula',$matricula);
                    $alumnos = $alumnos->leftJoin('tbl_folios as f', function($join){
                        $join->on('f.id_curso', '=', 'i.id_curso');
                        $join->on('f.matricula', '=', 'i.matricula');
                    });
                    $alumnos = $alumnos->where('i.id_curso',$curso->id)->orderby('i.alumno')->orderby('f.folio','DESC')->get();

               //var_dump($alumnos);exit;
                if(count($alumnos)==0) $message = "El curso no tiene alumnos registrados. ";
                elseif(count($alumnos)>0) if(!$alumnos[0]->calificacion)$message = "No hay registro de calificaciones, no podrá asignar folios. ";
                //elseif(count($alumnos)>0) if($alumnos[0]->folio)$message = "Curso con folios expedidos. ";
                /*
                if(!$message){
                    $_SESSION['clave'] = $curso->clave;
                    $_SESSION['matricula'] = $matricula;
                }
                */

            }else $message = "Clave inválida.";
        }
        return $data = [$curso, $acta, $alumnos, $message];

    }

    /** By Jose Luis Moreno Arcos Firma efolios*/
    ## Función para la generación de xml
    public function efolios_insert(Request $request){
        $mensaje = '';
        $no_creados = $correctos = 0;
        $clave =  $request->clave;
        $matricula = $request->matricula;
        $efirma = $request->efirma;

        $curso = DB::table('tbl_cursos as tc')->select('tc.id', 'tc.id_unidad', 'tc.cct', 'tc.dura', 'tc.termino', 'tc.curso', 'tc.id_curso',
        'tc.mod', 'tu.unidad', 'tu.ubicacion', 'tu.plantel', DB::raw('UPPER(tu.municipio_acm) as municipio_acm'))
        ->join('tbl_unidades as tu', 'tu.unidad', '=', 'tc.unidad')
        ->where('clave',$clave)->first();

        ##Validar campo
        if (is_null($curso)) {return back()->with('msn', 'Error en la clave de curso');}

        ## Buscamos el contenido tematico del curso
        $cadena_tematico = $submodulo = '';
        $array_tematico = [];
        $carta_descriptiva = DB::table('tbl_carta_descriptiva')->where('id_curso', $curso->id_curso)->value('cont_tematico');
        // $carta_descriptiva = DB::table('tbl_carta_descriptiva')->where('id_curso', 1404)->value('cont_tematico');

        if(!empty($carta_descriptiva)){
            $cont_tema = json_decode($carta_descriptiva, true);
            foreach ($cont_tema as $item) {
                // $submodulo = '';
                $tempo = ['nombre_modulo' => $item['name_modulo'], 'submodulos' => $item['val_inputs'], 'hora' => $item['curso_hora'].' '.$item['sel_horario']];
                // foreach ($item['val_inputs'] as $subitem) {
                //     if($subitem != '.') $submodulo .= $subitem.', ';
                // }
                $cadena_tematico .= $item['name_modulo'].'( '. $item['curso_hora'].' '.$item['sel_horario'].' )'."\n";
                $array_tematico [] = $tempo;
            }
        }else{
            return back()->with(['msn' => "No es posible generar el xml debido a que no se encuentra el contenido tematico del curso\n comuníquese con la DTA para la captura de la carta descriptiva",
            'clave' => $clave, 'matricula' => $matricula, 'efirma' => $efirma]);
        }

        try {
            ##Obtenemos fecha del termino de curso para la constancia
            $fecha_termino = Carbon::createFromFormat('Y-m-d', $curso->termino);
            $dia = $fecha_termino->day;
            $mes = strtoupper($fecha_termino->translatedFormat('F'));
            $anio = $this->convertirAniooALetras($fecha_termino->year);
            $resul_fecha = [$dia, $mes, $anio];

            ##OBTENEMOS DATOS DE LOS FIRMANTES YA VALIDADOS CON INCAPACIDAD
            $data = $this->datos_firmantes($curso->id_unidad);
            list($firm_academico, $firm_director) = $data;

            ##Consulta de alumnos foliados con efirma
            $alumnos = DB::table('tbl_inscripcion as i')->select('i.id','i.matricula','i.alumno','i.calificacion','i.reexpedicion','i.curp','f.folio','f.fecha_expedicion','f.movimiento','f.motivo', 'bf.mod')
                        ->where('i.status','INSCRITO')->where('f.motivo', 'ACREDITADO')->leftjoin('tbl_folios as f','f.id','i.id_folio')
                        ->join('tbl_banco_folios as bf', 'bf.id', 'f.id_banco_folios')->where('bf.mod', 'EFIRMA');
                        if($matricula)$alumnos = $alumnos->where('i.matricula',$matricula);
                        $alumnos = $alumnos->where('i.id_curso', $curso->id)->orderby('i.alumno')->get(); //limit de 5 pesonas por el momento ->limit(5)

            if($firm_academico && $firm_director && $alumnos){
                $status_efirma = '';
                foreach($alumnos as $alumno){
                    $valid_alumno = DB::table('efolios_alumnos')->select('id','status_doc')->where('id_curso', $curso->id)
                    ->where('matricula', $alumno->matricula)->where('efolio', $alumno->folio)->first();

                    if($valid_alumno != null) {$status_efirma = $valid_alumno->status_doc;}
                    if ($valid_alumno == null || $status_efirma == 'cancelado') {  //no esta el xml
                        ##Datos del alumno
                        $folio = $alumno->folio; $nombre = $alumno->alumno; $curp = $alumno->curp; $matricula_al = $alumno->matricula;
                        if($folio != '' && $nombre != '' && $curp != ''){
                            $correctos ++;
                            $this->generar_xml($folio, $nombre, $curp, $firm_academico, $firm_director, $curso, $resul_fecha,
                            $matricula_al, $cadena_tematico, $array_tematico);
                        }
                    }else{
                        $no_creados ++;
                    }

                }
                if($correctos > $no_creados) $mensaje = 'Se crearon los documentos de manera exitosa '.'Creados: '.$correctos.'    Detalles: '.$no_creados;
                // else $mensaje = 'Los documentos se encuentran en firma, firmados o sellados, por lo tanto no es posible crearlos de nuevo, para ello tiene que cancelarlos primero'.'Creados -> '.$correctos.' No Creados -> '.$no_creados;
                else $mensaje = "Los documentos se encuentran con estatus (en firma, firmados o sellados), por lo tanto no es posible crearlos de nuevo. \nPara realizar esta acción, primero debe cancelar los documentos.   \n[Documentos creados:  $correctos\n / No creados:  $no_creados]";

                return redirect('grupos/asignarfolios')->with(['msn'=>$mensaje, 'clave' => $clave, 'matricula' => $matricula, 'efirma' => $efirma]);

            }else{return redirect('grupos/asignarfolios')->with(['msn'=>'No se econtraron datos de firmantes o alumnos', 'clave' => $clave, 'matricula' => $matricula, 'efirma' => $efirma]);}

        } catch (\Throwable $th) {
            return redirect('grupos/asignarfolios')->with(['msn'=>'Error: '.$th->getMessage(), 'clave' => $clave, 'matricula' => $matricula, 'efirma' => $efirma]);
        }

    }

    #Generar xml
    private function generar_xml($folio, $nombre, $curp, $academico, $director, $curso, $resul_fecha, $matricula, $cadena_tematico, $array_tematico) {

        $datos = $this->create_body($folio, $nombre, $curp, $academico, $director, $curso, $resul_fecha, $cadena_tematico, $array_tematico); //creacion de body
        list($body, $objAlum) = $datos;

        // $data = $this->datos_firmantes($curso->id_unidad);

        $nameFileOriginal = 'Constancia Alumno '.$folio.'.pdf';
        $numOficio = 'CONSTANCIA_ALUMNO-'.$folio;
        $numFirmantes = '2';

        $arrayFirmantes = [];

        //Llenado de funcionarios firmantes
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $academico->curp,
                'nombre_firmante' => $academico->funcionario,
                'email_firmante' => $academico->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);

        $temp = ['_attributes' =>
            [
                'curp_firmante' => $director->curp,
                'nombre_firmante' => $director->funcionario,
                'email_firmante' => $director->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);


        ### XML CON FOTOS
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $academico->name,
                    'cargo_emisor' => $academico->puesto,
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [$body]
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' => [
                    $arrayFirmantes
                ]
            ],
        ];

        //Creacion de estampa de hora exacta de creacion
        $date = Carbon::now();
        $month = $date->month < 10 ? '0'.$date->month : $date->month;
        $day = $date->day < 10 ? '0'.$date->day : $date->day;
        $hour = $date->hour < 10 ? '0'.$date->hour : $date->hour;
        $minute = $date->minute < 10 ? '0'.$date->minute : $date->minute;
        $second = $date->second < 10 ? '0'.$date->second : $date->second;
        $dateFormat = $date->year.'-'.$month.'-'.$day.'T'.$hour.':'.$minute.':'.$second;

        $result = ArrayToXml::convert($ArrayXml, [
            'rootElementName' => 'DocumentoChis',
            '_attributes' => [
                'version' => '2.0',
                'fecha_creacion' => $dateFormat,
                'no_oficio' => $numOficio,
                'dependencia_origen' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                'asunto_docto' => 'Constancia alumno',
                'tipo_docto' => 'OFC',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);
        //Generacion de cadena unica mediante el ICTI
        $xmlBase64 = base64_encode($result);
        $getToken = Tokens_icti::all()->last();
        if ($getToken) {
            $response = $this->getCadenaOriginal($xmlBase64, $getToken->token);
            if ($response->json() == null) {
                $token = $this->generarToken();
                $response = $this->getCadenaOriginal($xmlBase64, $token);
            }
        } else {// no hay registros
            $token = $this->generarToken();
            $response = $this->getCadenaOriginal($xmlBase64, $token);
        }
        //Guardado de cadena unica
        if ($response->json()['cadenaOriginal'] != null) {

            try {
                // $dataInsert = EfoliosAlumnos::Where('id_curso',$curso->id)->Where('nombre_archivo','Constancia Alumno')->First();
                $dataInsert = EfoliosAlumnos::Where('id_curso',$curso->id)->Where('efolio', $folio)->First();
                if(is_null($dataInsert)) {
                    $dataInsert = new EfoliosAlumnos();
                }
                // $dataInsert->obj_documento_interno = json_encode($ArrayXml);
                $dataInsert->id_curso = $curso->id;
                $dataInsert->matricula = $matricula;
                $dataInsert->efolio = $folio;
                $dataInsert->status_doc = 'EnFirma';
                $dataInsert->fecha_creacion = date('Y-m-d H:i');
                $dataInsert->nombre_archivo = $nameFileOriginal;
                $dataInsert->no_oficio = $numOficio;
                $dataInsert->datos_alumno = $objAlum;
                $dataInsert->obj_documento = $ArrayXml;
                $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
                $dataInsert->documento_xml = $result;
                $dataInsert->iduser_created = Auth::user()->id;
                // $dataInsert->documento_interno = $result;
                $dataInsert->save();
                // return "EXITOSO";
            } catch (\Throwable $th) {
                return "ERROR AL GUARDAR LOS DATOS EN LA TABLA: ".$th->getMessage();
            }

        } else {
            return "ERROR AL ENVIAR Y VALIDAR. INTENTE NUEVAMENTE EN UNOS MINUTOS";
        }
    }


    #Crear Cuerpo
    private function create_body($folio, $nombre, $curp, $academico, $director, $curso, $resul_fecha, $cadena_tematico, $array_tematico) {

        try {
            $stps = '';
            $modalidad = 'EXTENSIÓN.';
            if($curso->mod == 'CAE'){$stps = 'ICV-00-07-27-K41-0013'; $modalidad = 'CAPACITACIÓN ACELERADA ESPECIFICA.';}
            $valid_accionmovil = ($curso->unidad != $curso->ubicacion) ? 'Centro de trabajo acción móvil '.$curso->plantel.' '.$curso->unidad : 'Unidad de capacitación '.$curso->plantel.' '.$curso->unidad;

            $objAlum =[
                'unidad' => $curso->unidad,
                'ubicacion' => $curso->ubicacion,
                'plantel' => $curso->plantel,
                'municipio' => $curso->municipio_acm,
                'cct' => $curso->cct,
                'stps' => $stps,
                'nombre' => $nombre,
                'curp' => $curp,
                'curso' => $curso->curso,
                'dura' => $curso->dura,
                'folio' => $folio,
                'diaexp' => $resul_fecha[0],
                'mesexp' => $resul_fecha[1],
                'anioexp' => $resul_fecha[2],
                'academico' => $academico->funcionario,
                'director' => $director->funcionario,
                'puesto_acad' => $academico->cargo,
                'puesto_direc' => $director->cargo,
                'modalidad' => $curso->mod,
                'cont_tematico' => $array_tematico
            ];

            $body = "SECRETARÍA DE EDUCACIÓN PÚBLICA\n".
            "\n SISTEMA EDUCATIVO NACIONAL".
            "\n DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO\n".

            "\n INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN".
            "\n TECNOLÓGICA DEL ESTADO DE CHIAPAS \n".

            "\n".$valid_accionmovil."       "." Con CCT: ".$curso->cct.
            "\n".$stps.
            "\n OTORGA LA PRESENTE \n".
            "\n\n CONSTANCIA \n\n".
            "\n A:       ".$nombre."\n".
            "\n con Clave Única de Registro de Poblacion  ".$curp.
            "\n En virtud de haber acreditado los conocimientos, habilidades, destrezas y aptitudes del".
            "\n curso, conforme al programa de capacitación de acuerdo a los documentos que obran en".
            "\n los archivos del Instituto".

            "\n\n".$curso->curso.
            "\n\n Con una duración de ".$curso->dura." horas, en la modalidad de ".$modalidad." \n\n".

            "\n folio ".$folio."\n\n".

            "\n La presente se expide en ".$curso->municipio_acm.", CHIAPAS.".
            "\n A los ".$resul_fecha[0]." días del mes de ".$resul_fecha[1]." del ".$resul_fecha[2].
            "\n CONTENIDO TEMATICO ".$cadena_tematico;

        } catch (\Throwable $th) {
            return "ERROR AL CREAR EL CUERPO DEL DOCUMENTO ".$th->getMessage();
        }

        return $data = [$body, $objAlum];
    }

    ## DATOS DE FIRMANTES
    private function datos_firmantes($id_unidad){
        $firm_academico = $firm_director = null;

        try {
            $firm_academico = DB::Table('tbl_organismos AS org')
            ->Select('fun.id as id_fun','org.id', 'fun.nombre AS funcionario','fun.curp', 'us.name',
            'fun.cargo','fun.correo', 'us.puesto', 'fun.incapacidad')
                ->join('tbl_funcionarios AS fun', 'fun.id_org','org.id')
                ->join('tbl_cursos as tc', 'tc.id_unidad','org.id_unidad')
                ->join('users as us', 'us.email','fun.correo')
                ->where('org.nombre', 'LIKE', '%ACADEMICO%')
                ->where('tc.id_unidad', '=', $id_unidad)
                ->where('fun.activo', '=', 'true')
                ->first();
            if($firm_academico == null){return "NO SE ENCONTRON DATOS DEL ACADEMICO!";}
        } catch (\Throwable $th) {
            return back()->with('message', 'Error firmante academico '.$th->getMessage());
        }

        try {
            $firm_director = DB::Table('tbl_organismos AS org')
            ->Select('fun.id as id_fun','org.id', 'fun.nombre AS funcionario','fun.curp', 'us.name',
            'fun.cargo','fun.correo', 'us.puesto', 'fun.incapacidad')
                ->join('tbl_funcionarios AS fun', 'fun.id_org','org.id')
                ->join('tbl_cursos as tc', 'tc.id_unidad','org.id_unidad')
                ->join('users as us', 'us.email','fun.correo')
                ->where('org.nombre', 'LIKE', '%UNIDAD DE CAPACITACIÓN%')
                ->where('tc.id_unidad', '=', $id_unidad)
                ->where('fun.activo', '=', 'true')
                ->first();
            if($firm_director == null){return "NO SE ENCONTRON DATOS DEL DIRECTOR!";}
        } catch (\Throwable $th) {
            return back()->with('message', 'Error firmante director de unidad '.$th->getMessage());
        }


        $val_incap = $this->valid_incapacidad($firm_academico);
        if ($val_incap != null) {
            $firm_academico = $val_incap;
        }

        $val_incap = $this->valid_incapacidad($firm_director);
        if ($val_incap != null) {
            $firm_director = $val_incap;
        }

        return $data = [$firm_academico, $firm_director];
    }

    ##CONVERTIR AÑO EN LETRAS
    // Función para convertir el año a letras en español
    private function convertirAniooALetras($anio)
    {
        $unidades = array_map('strtoupper', ['', 'uno', 'dos', 'trés', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve']);
        $decenas = array_map('strtoupper', ['diez', 'veinti', 'treintai', 'cuarentai', 'cincuentai', 'sesentai', 'setentai', 'ochentai', 'noventai']);
        $especiales = array_map('strtoupper', ['once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve']);

        $añoEnLetras = '';

        // Separar el año en sus dígitos
        $anios = str_pad($anio, 4, '0', STR_PAD_LEFT);
        $miles = intval(substr($anios, 0, 1));
        $centenas = intval(substr($anios, 1, 1));
        $decenasNum = intval(substr($anios, 2, 1));
        $unidadesNum = intval(substr($anios, 3, 1));

        // Convertir los miles
        if ($miles > 0) {
            $añoEnLetras .= $unidades[$miles] . ' MIL ';
        }

        // Convertir las centenas
        if ($centenas > 0) {
            $añoEnLetras .= $unidades[$centenas] . ' CIENTOS ';
        }

        // Convertir las decenas y unidades
        if ($decenasNum > 0 || $unidadesNum > 0) {
            if ($decenasNum == 1) {
                $añoEnLetras .= $especiales[$unidadesNum - 1];
            } else {
                if ($decenasNum > 1) {
                    $añoEnLetras .= $decenas[$decenasNum - 1];
                }
                if ($unidadesNum > 0) {
                    $añoEnLetras .= '' . $unidades[$unidadesNum];
                }
            }
        }

        return $añoEnLetras;
    }

    ## VALIDACIÓN DE LA INCAPACIDAD DEL FIRMANTE
    private function valid_incapacidad($dataFirmante){
        $result = null;
        $status_campos = false;
        if($dataFirmante->incapacidad != null){
            $dataArray = json_decode($dataFirmante->incapacidad, true);

            ##Validamos los campos json
            if(isset($dataArray['fecha_inicio']) && isset($dataArray['fecha_termino'])
            && isset($dataArray['id_firmante']) && isset($dataArray['historial'])){

                if($dataArray['fecha_inicio'] != '' && $dataArray['fecha_termino'] != '' && $dataArray['id_firmante'] != ''){
                    $fecha_ini = $dataArray['fecha_inicio'];
                    $fecha_fin = $dataArray['fecha_termino'];
                    $id_firmante = $dataArray['id_firmante'];
                    $historial = $dataArray['historial'];
                    $status_campos = true;
                }
            }else{
                return "LA ESTRUCTURA DEL JSON DE LA INCAPACIDAD NO ES VALIDA!";
            }

            ##Validar si esta vacio
            if($status_campos == true){
                ##Validar las fechas
                $fechaActual = date("Y-m-d");
                $fecha_nowObj = new DateTime($fechaActual);
                $fecha_iniObj = new DateTime($fecha_ini);
                $fecha_finObj = new DateTime($fecha_fin);

                if($fecha_nowObj >= $fecha_iniObj && $fecha_nowObj <= $fecha_finObj){
                    ###Realizamos la consulta del nuevo firmante
                    $dataIncapacidad = DB::Table('tbl_organismos AS org')
                    ->Select('org.id', 'fun.nombre AS funcionario','fun.curp', 'us.name',
                    'fun.cargo','fun.correo', 'us.puesto', 'fun.incapacidad')
                    ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                    ->join('users as us', 'us.email','fun.correo')
                    ->where('fun.id', $id_firmante)
                    ->first();

                    if ($dataIncapacidad != null) {$result = $dataIncapacidad;}
                    else{return "NO SE ENCONTRON DATOS DE LA PERSONA QUE TOMARÁ EL LUGAR DEL ACADEMICO!";}
                }else{
                    ##Historial
                    $fecha_busqueda = 'Ini:'. $fecha_ini .'/Fin:'. $fecha_fin .'/IdFun:'. $id_firmante;
                    $clave_ar = array_search($fecha_busqueda, $historial);

                    if($clave_ar === false){ ##No esta en el historial entonces guardamos
                        $historial[] = $fecha_busqueda;
                        ##guardar en la bd el nuevo array en el campo historial del json
                        try {
                            $jsonHistorial = json_encode($historial);
                            DB::update('UPDATE tbl_funcionarios SET incapacidad = jsonb_set(incapacidad, \'{historial}\', ?) WHERE id = ?', [$jsonHistorial, $dataFirmante->id_fun]);
                        } catch (\Throwable $th) {
                            return "Error: " . $th->getMessage();
                        }

                    }
                }
            }

        }
        return $result;
    }

    public function generarToken() {
        ##Producción
        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'SISTEM_IVINCAP',
            'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        ]);

        ##Prueba
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'FirmaElectronica',
        //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        // ]);

        $token = $resToken->json();
        Tokens_icti::create(['token' => $token]);
        return $token;
    }

    // obtener la cadena original
    public function getCadenaOriginal($xmlBase64, $token) {
        ##Produccion
        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        ##Prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response1;
    }

}
