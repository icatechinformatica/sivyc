<?php

namespace App\Http\Controllers\efirma;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;
use App\Models\contratos;
use App\Models\especialidad_instructor;
use App\Models\Tokens_icti;
use App\Models\DocumentosFirmar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Support\Facades\Http;

class EContratoController extends Controller
{
    public function prueba() {
        dd('completo');
    }

    public function xml($id_contrato){
        // dd($id_contrato);
        $info = DB::Table('contratos')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo',
                    'contratos.numero_contrato')
                ->Join('folios','folios.id_folios','contratos.id_folios')
                ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
                ->Join('tbl_unidades','tbl_unidades.unidad','tabla_supre.unidad_capacitacion')
                ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('contratos.id_contrato',$id_contrato)
                ->First();

        $nameFileOriginal = 'contrato '.$info->clave.'.pdf';
        $numDocs = DocumentosFirmar::Where('tipo_archivo', 'Contrato')->Where('numero_o_clave', $info->clave)->WhereIn('status',['CANCELADO','CANCELADO ICTI'])->Get()->Count();
        $numDocs = '0'.($numDocs+1);
        $numOficioBuilder = explode('/',$info->numero_contrato);
        $position = count($numOficioBuilder) - 2;
        array_splice($numOficioBuilder, $position, 0, $numDocs);
        $numOficio = implode('/',$numOficioBuilder);

        $body = $this->create_body($id_contrato,$info, $info->numero_contrato); //creacion de body hemos reemplazado numOficio por $info->no_memo mientras se autoriza el uso del consecutivo electronico
        // dd($body['body_html']);

        $numFirmantes = '4';
        $arrayFirmantes = [];
        $firmante = array();

        $dataFirmantes = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre','fun.curp','fun.cargo','fun.correo','org.nombre AS org_nombre','fun.incapacidad')
                            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
                            ->Where('org.id_unidad', $info->id)
                            ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                            ->Where('fun.activo','true')
                            ->Get();
        // Info de director firmante
        foreach($dataFirmantes as $dataFirmante) {

            if (str_contains($dataFirmante->cargo, 'DIRECTOR') || str_contains($dataFirmante->cargo, 'DIRECTORA') || str_contains($dataFirmante->cargo, 'ENCARGADO DE LA UNIDAD') || str_contains($dataFirmante->cargo, 'ENCARGADA DE LA UNIDAD')) {
                if(isset($dataFirmante->incapacidad)) {
                    $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmante->incapacidad), $dataFirmante->nombre);
                    if($incapacidadFirmante != FALSE) {
                        $dataFirmante = $incapacidadFirmante;
                    }
                }
                $temp = ['_attributes' =>
                    [
                        'curp_firmante' => $dataFirmante->curp,
                        'nombre_firmante' => $dataFirmante->nombre,
                        'email_firmante' => $dataFirmante->correo,
                        'tipo_firmante' => 'FM'
                    ]
                ];
                array_push($arrayFirmantes, $temp);
                $emisor = [ 'nombre' => $dataFirmante->nombre, 'cargo' => $dataFirmante->cargo];
                array_push($firmante, ['nombre' => $dataFirmante->nombre, 'curp' => $dataFirmante->curp, 'cargo' => $dataFirmante->cargo]);
            }
        }

        //Info de instructor firmante
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $info->curp,
                'nombre_firmante' => $info->nombre,
                'email_firmante' => $info->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);

        //Llenado de academico firmante
        foreach($dataFirmantes as $dataFirmante) {
            if (str_contains($dataFirmante->cargo, 'ACADÉMICO')) {
                if(isset($dataFirmante->incapacidad)) {
                    $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmante->incapacidad), $dataFirmante->nombre);
                    if($incapacidadFirmante != FALSE) {
                        $dataFirmante = $incapacidadFirmante;
                    }
                }
                $temp = ['_attributes' =>
                    [
                        'curp_firmante' => $dataFirmante->curp,
                        'nombre_firmante' => $dataFirmante->nombre,
                        'email_firmante' => $dataFirmante->correo,
                        'tipo_firmante' => 'FM'
                    ]
                ];
                array_push($arrayFirmantes, $temp);
                $emisor = [ 'nombre' => $dataFirmante->nombre, 'cargo' => $dataFirmante->cargo];
                array_push($firmante, ['nombre' => $dataFirmante->nombre, 'curp' => $dataFirmante->curp, 'cargo' => $dataFirmante->cargo]);
            }
        }

        //Llenado de vinculacion firmante
        // foreach($dataFirmantes as $dataFirmante) {

        //     if (str_contains($dataFirmante->cargo, 'VINCULACION')) {
        //         if(isset($dataFirmante->incapacidad)) {
        //             $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmante->incapacidad), $dataFirmante->nombre);
        //             if($incapacidadFirmante != FALSE) {
        //                 $dataFirmante = $incapacidadFirmante;
        //             }
        //         }
        //         $temp = ['_attributes' =>
        //             [
        //                 'curp_firmante' => $dataFirmante->curp,
        //                 'nombre_firmante' => $dataFirmante->nombre,
        //                 'email_firmante' => $dataFirmante->correo,
        //                 'tipo_firmante' => 'FM'
        //             ]
        //         ];
        //         array_push($arrayFirmantes, $temp);
        //         array_push($firmante, ['nombre' => $dataFirmante->nombre, 'curp' => $dataFirmante->curp, 'cargo' => $dataFirmante->cargo]);
        //     }
        // }

        //Llenado de delegacion firmante
        foreach($dataFirmantes as $dataFirmante) {
            if (str_contains($dataFirmante->cargo, 'DELEGADO') || str_contains($dataFirmante->cargo, 'DELEGADA') || str_contains($dataFirmante->cargo, 'ENCARGADO DE LA DELEGA') || str_contains($dataFirmante->cargo, 'ENCARGADA DE LA DELEGA')) {
                if(isset($dataFirmante->incapacidad)) {
                    $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmante->incapacidad), $dataFirmante->nombre);
                    if($incapacidadFirmante != FALSE) {
                        $dataFirmante = $incapacidadFirmante;
                    }
                }
                $temp = ['_attributes' =>
                    [
                        'curp_firmante' => $dataFirmante->curp,
                        'nombre_firmante' => $dataFirmante->nombre,
                        'email_firmante' => $dataFirmante->correo,
                        'tipo_firmante' => 'FM'
                    ]
                ];
                array_push($arrayFirmantes, $temp);
                array_push($firmante, ['nombre' => $dataFirmante->nombre, 'curp' => $dataFirmante->curp, 'cargo' => $dataFirmante->cargo]);
            }
        }

        //Llenado de directo de técnica académica
        $dataFirmanteDTA = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre','fun.curp','fun.cargo','fun.correo','org.nombre AS org_nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Where('org.id', '16')
            ->Where('fun.activo','true')
            ->Where('titular', true)
            ->First();
        if(isset($dataFirmanteDTA->incapacidad)) {
            $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmanteDTA->incapacidad), $dataFirmanteDTA->nombre);
            if($incapacidadFirmante != FALSE) {
                $dataFirmanteDTA = $incapacidadFirmante;
            }
        }
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $dataFirmanteDTA->curp,
                'nombre_firmante' => $dataFirmanteDTA->nombre,
                'email_firmante' => $dataFirmanteDTA->correo,
                'tipo_firmante' => 'FM'
            ]
        ];
        array_push($arrayFirmantes, $temp);
        array_push($firmante, ['nombre' => $dataFirmanteDTA->nombre, 'curp' => $dataFirmanteDTA->curp, 'cargo' => $dataFirmanteDTA->cargo]);

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $emisor['nombre'],
                    'cargo_emisor' => $emisor['cargo'],
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'receptores' => [
                'receptor' => [
                    '_attributes' => [
                        'nombre_receptor' => $info->nombre,
                        'cargo_receptor' => 'Instructor Externo',
                        'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                        'tipo_receptor' => 'IEX'
                    ]
                ]
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [strip_tags($body)]
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
                'asunto_docto' => 'Contrato de Instructor',
                'tipo_docto' => 'CNT',
                'xmlns' => 'http://firmaelectronica.chiapas.gob.mx/GCD/DoctoGCD',
            ],
        ]);
        //Generacion de cadena unica mediante el ICTI
        $xmlBase64 = base64_encode($result);
        $getToken = Tokens_icti::Where('sistema', 'sivyc')->First();
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
        if ($response->json()['cadenaOriginal'] != null) { //mejorar por si viene vacio response. con un isset
            $sobrescribir = True;
            // Actualizar  este dataInsert ya que se pondra un consecutivo interno y poder hacer mas documentos por si alguno se cancela
            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','Contrato')->WhereIn('status',['CANCELADO','EnFirma'])->First();
            if(isset($dataInsert->obj_documento)) {
                $firmantes = json_decode($dataInsert->obj_documento, true);
                if($dataInsert->status != 'CANCELADO') {
                    foreach($firmantes['firmantes']['firmante']['0'] as $firma) {
                        if(isset($firma['_attributes']['certificado'])) {
                            $sobrescribir = False;
                        }
                    }
                }
            } else {
                $sobrescribir = False;
            }

            if(!$sobrescribir) {
                $dataInsert = new DocumentosFirmar();
            }

            $body_html = ['body' => $body, 'firmantes' => $firmante];

            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($body_html);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Contrato';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            $dataInsert->num_oficio = $numOficio;
            $dataInsert->save();

            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function create_body($id_contrato,$firmantes, $numOficio = NULL) {
        $body_html = NULL;
        $data_contrato = contratos::WHERE('id_contrato', '=', $id_contrato)->FIRST();
        $data = contratos::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','tbl_cursos.fecha_apertura','tbl_cursos.soportes_instructor',
                                  'tbl_cursos.tipo_curso','tbl_cursos.espe', 'tbl_cursos.clave','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','tbl_cursos.instructor_tipo_identificacion','tbl_cursos.instructor_folio_identificacion','instructores.rfc','tbl_cursos.modinstructor',
                                  'instructores.curp','instructores.domicilio','tabla_supre.fecha_validacion')
                          ->WHERE('folios.id_folios', '=', $data_contrato->id_folios)
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tabla_supre', 'tabla_supre.id', '=', 'folios.id_supre')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->FIRST();
                          //nomes especialidad
        $especialidad = especialidad_instructor::SELECT('especialidades.nombre')
                                                ->WHERE('especialidad_instructores.id', '=', $data_contrato->instructor_perfilid)
                                                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'especialidad_instructores.especialidad_id')
                                                ->FIRST();

        $fecha_act = new Carbon('23-06-2022');
        $fecha_fir = new Carbon($data_contrato->fecha_firma);
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);
        $direccion_instituto = DB::Table('tbl_instituto')->Where('id',1)->Value('direccion');
        $direccion_instituto = str_replace('*',' ',$direccion_instituto);
        $direccion_instituto = mb_strtoupper(str_replace('. Tuxtla',', en la ciudad de Tuxtla',$direccion_instituto), 'UTF-8');

        $cantidad = $this->numberFormat($data_contrato->cantidad_numero);
        $monto = explode(".",strval($data_contrato->cantidad_numero));

        // obtencion de tipo de identificaion y folio dependiendo si esta en el json o en el capo a parte
        $data->soportes_instructor = json_decode($data->soportes_instructor);
        if(isset($data->soportes_instructor->tipo_identificacion)) {
            $tipo_identificacion =$data->soportes_instructor->tipo_identificacion;
            $folio_identificacion = $data->soportes_instructor->folio_identificacion;
        } else {
            $tipo_identificacion = $data->instructor_tipo_identificacion;
            $folio_identificacion = $data->instructor_folio_identificacion;
        }

        if ($data->modinstructor == 'HONORARIOS') {
            //honorarios
            $body = 'Contrato No.'.$data_contrato->numero_contrato.".\n".
            'CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES POR HONORARIOS EN SU MODALIDAD DE HORAS CURSO, QUE CELEBRAN POR UNA PARTE, EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, REPRESENTADO POR ' . $firmantes->dunidad .','.' EN SU CARÁCTER DE '. $firmantes->pdunidad . ' '. $data_contrato->unidad_capacitacion .' Y POR LA OTRA LA O EL C. '.$nomins.", EN SU CARÁCTER DE INSTRUCTOR EXTERNO; A QUIENES EN LO SUCESIVO SE LES DENOMINARÁ “ICATECH” Y “PRESTADOR DE SERVICIOS” RESPECTIVAMENTE; MISMO QUE SE FORMALIZA AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES: \n
            DECLARACIONES \n
                I.  “ICATECH” declara que: \n
                    I.1 Es un Organismo Descentralizado de la Administración Pública Estatal, con personalidad jurídica y patrimonio propios, conforme a lo dispuesto en el artículo 1 del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas. \n
                    I.2 El DR. CÉSAR ARTURO ESPINOSA MORALES, en su carácter de Director General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, cuenta con personalidad juridica que acredita con nombramiento expedido a su favor por el DR. EDUARDO RAMÍREZ AGUILAR, Gobernador Constitucional del Estado de Chiapas, de fecha 08 de diciembre de 2024, por lo que se encuentra plenamente facultado en términos de lo dispuesto en los artículos 28 fracción I de la Ley de Entidades Paraestatales del Estado de Chiapas; 15 fracción I del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, así como el 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, mismas que no le han sido limitadas o revocadas por lo que, delega su representación a los Titulares de las Unidades de Capacitación conforme a lo dispuesto por el artículo" . ($fecha_fir >= $fecha_act ? '42 fracción I' : '29 fracción I')." del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas. \n
                    I.3 " . $firmantes->dunidad .', '.$firmantes->pdunidad. ' '. $data_contrato->unidad_capacitacion.' tiene personalidad jurídica para representar en este acto a “ICATECH”, como lo acredita con el nombramiento expedido por el Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, y cuenta con plena facultad legal para suscribir el presente Instrumento conforme a lo dispuesto por los artículos ' . ($fecha_fir >= $fecha_act ? '42 fracción I' : '29 fracción I') . " del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas y 12 fracción V, de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas. \n
                    I.4 Tiene por objetivo impartir e impulsar la capacitación para la formación en el trabajo, propiciando la mejor calidad y vinculación de este servicio con el aparato productivo y las necesidades de desarrollo regional, estatal y nacional; actuar como organismo promotor en materia de capacitación para el trabajo, conforme a lo establecido por la Secretaría de Educación Pública; promover la capacitación que permita adquirir, reforzar o potencializar los conocimientos, habilidades y destrezas necesarias para elevar el nivel de vida, competencia laboral y productividad en el Estado; promover el surgimiento de nuevos perfiles académicos, que correspondan a las necesidades del mercado laboral. \n
                    I.5 De acuerdo a las necesidades de “ICATECH”, se requiere contar con los servicios de una persona física con conocimientos en ". $data->espe. ', por lo que se ha determinado llevar a cabo la Contratación por HONORARIOS en la modalidad de horas curso como "PRESTADOR DE SERVICIOS".' . "\n
                    I.6 Para los efectos del presente contrato se cuenta con la clave de grupo ". $data->clave." y validación del instructor emitido por la Dirección Técnica Académica de “ICATECH” conforme a lo dispuesto por el artículo 4 fracción III de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, emitido por la Dirección Técnica Académica de “ICATECH”.\n
                    I.7 Para los efectos del presente contrato se cuenta con la suficiencia presupuestal, conforme al presupuesto de egresos autorizado, emitida por la Dirección de Planeación de “ICATECH”.\n
                    I.8 Para los efectos del presente Contrato señala como su domicilio legal, el ubicado en la $direccion_instituto.\n
                II.". '"PRESTADOR DE SERVICIOS"'. "declara que: \n
                    II.1 Es una persona física, de nacionalidad Mexicana, que acredita mediante ". ($data->instructor_tipo_identificacion == 'INE'? ' credencial para votar' : $data->instructor_tipo_identificacion).' con número de folio '. $data->instructor_folio_identificacion.', con plena capacidad jurídica y facultades que le otorga la ley, para contratar y obligarse, así como también con los estudios, conocimientos y la experiencia necesaria en la materia de '.$data->espe." y conoce plenamente las necesidades de los servicios objeto del presente contrato, así como que ha considerado todos los factores que intervienen para desarrollar eficazmente las actividades que desempeñará. \n
                    II.2 Se encuentra al corriente en el pago de sus impuestos y cuenta con el Registro Federal de Contribuyentes número ". $data->rfc.", expedido por el Servicio de Administración Tributaria de la Secretaría de Hacienda y Crédito Público, conforme a lo dispuesto por los artículos 27 del Código Fiscal de la Federación y 110 fracción I de la Ley de Impuesto sobre la Renta.\n
                    II.3 Es conforme de que “ICATECH”, le retenga los impuestos a que haya lugar por concepto de la Prestación de Servicios Profesionales por HONORARIOS.\n
                    II.4 Bajo protesta de decir verdad, no se encuentra inhabilitado por autoridad competente alguna, así como a la suscripción del presente documento no ha sido parte en juicios del orden civil, mercantil, penal, administrativo o laboral en contra de “ICATECH” o de alguna otra institución pública o privada; y que no se encuentra en algún otro supuesto o situación que pudiera generar conflicto de intereses para prestar los servicios profesionales objeto del presente contrato.\n
                    II.5 Para los efectos del presente contrato, señala como su domicilio legal el ubicado en ".$data->domicilio.". \n\n

            Con base en las declaraciones antes expuestas, declaran las partes que es su voluntad celebrar el presente contrato, sujetándose a las siguientes:\n\n

            CLÁUSULAS\n
                PRIMERA.- OBJETO DEL CONTRATO. El presente instrumento tiene por objeto establecer al “PRESTADOR DE SERVICIOS” los términos y condiciones que se obliga con “ICATECH”, a brindar sus servicios profesionales bajo el régimen de HONORARIOS, para otorgar el curso establecido en el ARC01 y/o ARC02.\n
                SEGUNDA.- MONTO DE LOS HONORARIOS. El monto total de los servicios que “ICATECH”, pagará al “PRESTADOR DE SERVICIOS” será por la cantidad de $".$cantidad .'('.$data_contrato->cantidad_letras1.' '. $monto[1].'/100 M.N.), '. ($data->fecha_apertura <  '2023-10-12' ? 'mas el 16% (dieciséis por ciento) del' : 'importe que incluye el') ." Impuesto al Valor Agregado, menos las retenciones que de conformidad con la Ley del Impuesto Sobre la Renta, y demás disposiciones fiscales que procedan para el caso que nos ocupa.\n
                El monto resultante señalado en el párrafo primero de esta cláusula se otorgará al “PRESTADOR DE SERVICIOS” conforme a la disponibilidad financiera de “ICATECH”; que se realizará en una sola exhibición, por medio de transferencia electrónica interbancaria a la cuenta que señala, contra la entrega del Recibo de Honorarios y/o Factura correspondiente, mismo que deberá cubrir los requisitos fiscales estipulados por la Secretaría de Hacienda y Crédito Público; por lo que el “PRESTADOR DE SERVICIOS” no podrá exigir retribución alguna por ningún otro concepto.\n
                TERCERA.- DE LA OBLIGACIÓN DEL “PRESTADOR DE SERVICIOS”. Se obliga a desempeñar las obligaciones que contrae en este acto y con todo el sentido ético y profesional que requiere “ICATECH”, de acuerdo con las políticas y reglamentos del mismo para:\n
                        Diseñar, preparar y dictar los cursos a su cargo con toda la diligencia y esmero que exige la calidad de “ICATECH”.\n
                        Asistir con toda puntualidad a sus cursos y aprovechar íntegramente el tiempo necesario para el mejor desarrollo de los mismos.\n
                        Generar un reporte final del curso impartido o de cualquier incidente o problema que surgió en el desarrollo del mismo.\n
                        Cumplir con los procedimientos del control escolar de alumnos que implemente “ICATECH”.\n
                        Respetar las normas de conducta que establece “ICATECH”.\n
                        Implementar el Lenguaje Incluyente en la impartición de los cursos.\n\n

                CUARTA.- SECRETO PROFESIONAL DEL “PRESTADOR DE SERVICIOS”. En el presente contrato se obliga al “PRESTADOR DE SERVICIOS”, a no divulgar por medio de publicaciones, informes, videos, fotografías, medios electrónicos, conferencias o en cualquier otra forma, los datos y resultados obtenidos de los trabajos de este contrato, sin la autorización expresa de “ICATECH”, pues dichos datos y resultados son considerados confidenciales. Esta obligación subsistirá, aún después de haber terminado la vigencia de este contrato.\n
                QUINTA.- VIGENCIA. La vigencia del presente contrato será conforme a la duración del curso objeto del presente Instrumento, detallados en la CLÁUSULA PRIMERA; el cual será forzoso al “PRESTADOR DE SERVICIOS” y voluntario para “ICATECH” mismo que podrá darlo por terminado anticipadamente en cualquier tiempo, siempre y cuando existan motivos o razones de interés general, incumpla cualquiera de las obligaciones adquiridas con la formalización del presente instrumento o incurra en alguna de las causales previstas en la Cláusula Octava, mediante notificación por escrito a “PRESTADOR DE SERVICIOS”; en todo caso “ICATECH” deberá cubrir el monto únicamente en cuanto a los servicios prestados.\n
                Concluido el término del presente contrato no podrá haber prórroga automática por el simple transcurso del tiempo y terminará sin necesidad de darse aviso entre las partes. Si terminada la vigencia de este contrato, “ICATECH” tuviere necesidad de seguir utilizando los servicios del “PRESTADOR DE SERVICIOS”, se requerirá la celebración de un nuevo contrato, sin que éste pueda ser computado con el anterior.\n
                SEXTA.- SEGUIMIENTO. “ICATECH” a través de los representantes que al efecto designe, tendrá en todo tiempo el derecho de supervisar el estricto cumplimiento de este contrato, por lo que podrá revisar e inspeccionar las actividades que desempeñe “PRESTADOR DE SERVICIOS”.\n
                SÉPTIMA.- PROPIEDAD DE RESULTADOS Y DERECHOS DE AUTOR. Los documentos, estudios y demás materiales que se generen en la ejecución o como consecuencia de este contrato, serán propiedad de “ICATECH”, obligando al “PRESTADOR DE SERVICIOS” a entregarlos al término del presente instrumento.\n
                Se obliga al “PRESTADOR DE SERVICIOS” a responder ilimitadamente de los daños o perjuicios que pudiera causar a “ICATECH” o a terceros, si con motivo de la prestación de los servicios contratados viola derechos de autor, de patentes y/o marcas u otro derecho reservado, por lo que manifiesta en este acto bajo protesta de decir verdad, no encontrarse en ninguno de los supuestos de infracción a la Ley Federal de Derechos de Autor ni a la Ley de Propiedad Industrial.\n
                En caso de que sobreviniera alguna reclamación o controversia legal en contra de “ICATECH” por cualquiera de las causas antes mencionadas, la única obligación de éste será dar aviso al “PRESTADOR DE SERVICIOS” en el domicilio previsto en este instrumento para que ponga a salvo a “ICATECH” de cualquier controversia.\n
                OCTAVA.- RESCISIÓN. “ICATECH” podrá rescindir el presente contrato sin responsabilidad alguna, sin necesidad de declaración judicial, bastando para ello una notificación por escrito cuando concurran causas de interés general, cuando el “PRESTADOR DE SERVICIOS” incumpla algunas de las obligaciones del presente contrato y demás disposiciones contenidas en las leyes que le sean aplicables y cuando a juicio de “ICATECH” incurra en las siguientes causales:\n\n

                Negligencia o impericia.\n
                Falta de probidad u honradez.\n
                Por prestar los servicios de forma ineficiente e inoportuna.\n
                Por no apegarse a lo estipulado en el presente contrato.\n
                Por no observar la discreción debida respecto a la información a la que tenga acceso como consecuencia de la información de los servicios encomendados.\n
                Por suspender injustificadamente la prestación de los servicios o por negarse a corregir lo rechazado por “ICATECH”.\n
                Por negarse a informar a “ICATECH” sobre los resultados de la prestación del servicio encomendado.\n
                Por impedir el desempeño normal de las labores durante la prestación de los servicios.\n
                Si se comprueba que la protesta a que se refiere la Declaración II.2 de “PRESTADOR DE SERVICIOS” se realizó con falsedad.\n
                Por no otorgar los cursos en el tiempo establecido (horas del curso).\n
                Asimismo; en caso de tener evidencias de que el curso no fue impartido, se procederá a dar por rescindido el contrato, y se interpondrá la acción legal que corresponda.\n
                Podrá dar por rescindido al “PRESTADOR DE SERVICIOS” el “ICATECH” de forma anticipada el presente contrato, previo aviso que realice por escrito con un mínimo de 10 días hábiles.\n
                “ICATECH” se reservará el derecho de aceptar la terminación anticipada del contrato, sin que ello implique la renuncia a deducir las acciones legales que en su caso procedan.\n
                NOVENA.- CESIÓN. “PRESTADOR DE SERVICIOS” no podrá en ningún caso ceder total o parcialmente a terceros llámese persona física o persona moral, los derechos y obligaciones derivadas del presente contrato.\n
                DÉCIMA.- RELACIONES PROFESIONALES. “ICATECH” no adquiere ni reconoce obligación alguna de carácter laboral a favor del “PRESTADOR DE SERVICIOS”, en virtud de no ser aplicables a la relación contractual que consta en este instrumento, los artículos 1º y 8º de la Ley Federal del Trabajo y 123 apartado “A” y “B” de la Constitución Política de los Estados Unidos Mexicanos, por lo que no será considerado al “PRESTADOR DE SERVICIOS” como trabajador de “ICATECH” para los efectos legales y en particular para obtener las prestaciones establecidas en su artículo 5 A, fracciones V, VI y VII de la Ley del Instituto Mexicano del Seguro Social.\n
                En el presente instrumento se obliga al “PRESTADOR DE SERVICIOS” a ser el único responsable del cumplimiento con las normas laborales, fiscales, o cualquier otro acto contractual de diversa índole, incluso las de seguridad social e INFONAVIT que pudieran derivarse de la prestación de los servicios aquí contratados, consecuentemente libera de toda responsabilidad a “ICATECH” de las obligaciones que pudieran presentarse por estos conceptos.\n
                DÉCIMA PRIMERA.- RECONOCIMIENTO CONTRACTUAL. El presente contrato se rige por lo dispuesto en el Título Décimo del Contrato de Prestación de Servicios, Capítulo I, del Código Civil del Estado de Chiapas, por lo que no existe relación de dependencia ni de subordinación entre “ICATECH” y “PRESTADOR DE SERVICIOS”, ni podrán tenerse como tales los necesarios nexos de coordinación entre uno y otro.\n
                El presente contrato constituye el acuerdo de voluntades entre las partes, en relación con el objeto del mismo y deja sin efecto cualquier otra negociación o comunicación entre éstas, ya sea oral o escrita con anterioridad a la fecha de su firma.\n
                DÉCIMA SEGUNDA.- Manifiestan ambas partes bajo protesta de decir verdad que en el presente contrato no ha mediado dolo, error, mala fe, engaño, violencia, intimidación, ni cualquiera otra causa que pudiera invalidar el contenido y fuerza legal del mismo.\n
                DÉCIMA TERCERA.- DOMICILIOS. Para los efectos del presente instrumento las partes señalan como sus domicilios legales los estipulados en el Apartado de Declaraciones del presente instrumento legal.\n
                Mientras las partes no notifiquen por escrito el cambio de su domicilio, los emplazamientos y demás diligencias judiciales y extrajudiciales, se practicarán en el domicilio señalado en esta cláusula.\n
                DÉCIMA CUARTA.- RESPONSABILIDAD DEL “PRESTADOR DE SERVICIOS”. Será el responsable de la ejecución de los trabajos y deberá sujetarse en la realización de éstos, a todos aquellos reglamentos administrativos y manuales que las autoridades competentes hayan emitido, así como a las disposiciones establecidas por “ICATECH”.\n
                DÉCIMA QUINTA.- Las partes convienen que los datos personales insertos en el presente instrumento legal son protegidos por la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Chiapas y la Ley de Transparencia y Acceso a la Información Publica del Estado de Chiapas, así como los Lineamientos Generales de la Custodia y Protección de Datos Personales e Información Reservada y Confidencial en Posesión de los Sujetos Obligados del Estado de Chiapas y demás normatividad aplicable.\n
                DÉCIMA SEXTA.- JURISDICCIÓN. Para la interpretación y cumplimiento del presente contrato, así como para todo aquello que no esté expresamente estipulado en el mismo, las partes se someterán a la jurisdicción y competencia de los tribunales del fuero común de la ciudad de Tuxtla Gutiérrez, Chiapas, renunciando al fuero que pudiera corresponderles por razón de su domicilio presente o futuro.\n
                Leído que fue el presente contrato a las partes que en él intervienen y una vez enterados de su contenido y alcance legales, son conformes con los términos del mismo y para constancia lo firman y ratifican ante la presencia de los testigos que al final suscriben; en el municipio de ".$data_contrato->municipio.', Chiapas; '. ", el día de la expedición de la suficiencia presupuestal, que se adjunta al presente como si a la letra se insertase, a efecto de garantizar la disponibilidad de la oblicación contractual.\n
                Las Firmas que anteceden corresponden al Contrato de prestación de servicios profesionales por honorarios en su modalidad de horas curso No. ". $data_contrato->numero_contrato.', que celebran por una parte el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, representado por el (la) C. '.$firmantes->dunidad.', '.$firmantes->pdunidad.' '.$data_contrato->unidad_capacitacion.', y el (la) C. '.$nomins.', en el Municipio de '.$data_contrato->municipio.', a '.$D.' de '.$M.' del año '.$Y.";  el día de la expedición de la suficiencia presupuestal.\n";
        }else {
            $body_html = '<div align=right> <b>Contrato No. ';
            if(is_null($numOficio)) {
                $body_html = $body_html.$data_contrato->numero_contrato . '.</b> </div>';
             } else {
                $body_html = $body_html.$numOficio. '.</b> </div>';
             }
             $body_html = $body_html.'<br><div align="justify"><b>CONTRATO DE PRESTACIÓN DE SERVICIOS PROFESIONALES MODALIDAD DE '.($data->tipo_curso == 'CURSO' ? 'HORAS CURSO' : 'CERTIFICACION EXTRAORDINARIA').', QUE CELEBRAN POR UNA PARTE, EL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, REPRESENTADO POR '. $firmantes->dunidad. ', EN SU CARÁCTER DE '. $firmantes->pdunidad.' '.$data_contrato->unidad_capacitacion.' Y POR LA OTRA LA O EL C. '.$nomins. ', EN SU CARÁCTER DE INSTRUCTOR EXTERNO; A QUIENES EN LO SUCESIVO SE LES DENOMINARÁ “ICATECH” Y “PRESTADOR DE SERVICIOS” RESPECTIVAMENTE; MISMO QUE SE FORMALIZA AL TENOR DE LAS DECLARACIONES Y CLÁUSULAS SIGUIENTES:</b></div>
            <br><div align="center"> DECLARACIONES</div>
            <div align="justify">
                <dl>
                    <dt>I.  <b>“ICATECH”</b> declara que:<br>
                    <br><dd>I.1 Es un Organismo Descentralizado de la Administración Pública Estatal, con personalidad jurídica y patrimonio propio, conforme a lo dispuesto en el artículo 1 del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                    <br><dd>I.2 El DR. CÉSAR ARTURO ESPINOSA MORALES, en su carácter de DIRECTOR GENERAL DEL INSTITUTO DE CAPACITACIÓN y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS, cuenta con personalidad jurídica que acredita con nombramiento expedido a su favor por el DR. EDUARDO RAMÍREZ AGUILAR, Gobernador Constitucional del Estado de Chiapas, de fecha 08 de diciembre de 2024, por lo que se encuentra plenamente facultado en términos de lo dispuesto en los artículos 28 fracción I de la Ley de Entidades Paraestatales del Estado de Chiapas; 15 fracción I del Decreto por el que se crea el Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, así como el 13 fracción IV del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, mismas que no le han sido limitadas o revocadas por lo que, delega su representación a los Titulares de las Unidades de Capacitación conforme a lo dispuesto por el artículo 42 fracción I del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                    <br><dd>I.3 '. $firmantes->dunidad.', '.$firmantes->pdunidad.' '.$data_contrato->unidad_capacitacion.', tiene personalidad jurídica para representar en este acto a <b>“ICATECH”</b>, como lo acredita con el nombramiento expedido por el Titular de la Dirección General del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, y cuenta con plena facultad legal para suscribir el presente Instrumento conforme a lo dispuesto por los artículos 42 fracción I del Reglamento Interior del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas y 12 fracción V, de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas.</dd>
                    <br><dd>I.4 Tiene por objetivo impartir e impulsar la capacitación para la formación en el trabajo, propiciando la mejor calidad y vinculación de este servicio con el aparato productivo y las necesidades de desarrollo Regional, Estatal y Nacional; actuar como Organismo promotor en materia de capacitación para el trabajo, conforme a lo establecido por la Secretaría de Educación Pública; promover la capacitación que permita adquirir, reforzar o potencializar los conocimientos, habilidades y destrezas necesarias para elevar el nivel de vida, competencia laboral y productividad en el Estado; promover el surgimiento de nuevos perfiles académicos, que correspondan a las necesidades del mercado laboral.</dd>
                    <br><dd>I.5  De acuerdo a las necesidades de <b>“ICATECH”</b>, se requiere contar con los servicios de una persona física con conocimientos en '. $data->espe .', por lo que se ha determinado llevar a cabo la Contratación bajo el régimen de <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS,</b> en la modalidad de '.($data->tipo_curso == 'CURSO' ? 'horas curso' : 'certificación extraordinaria').' como <b>"PRESTADOR DE SERVICIOS"</b>.</dd>
                    <br><dd>I.6  Para los efectos del presente contrato se cuenta con la clave de grupo No: '. $data->clave. ', así como la validación del instructor emitida por la Dirección Técnica Académica de <b>“ICATECH”</b> conforme a lo dispuesto por el artículo 4 fracción III de los Lineamientos para los Procesos de Vinculación y Capacitación del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, emitida por la Dirección Técnica Académica de <b>“ICATECH”</b>.</dd>
                    <br><dd>I.7 Para los efectos del presente Contrato se cuenta con la suficiencia presupuestal, conforme al presupuesto de egresos autorizado, emitida por la Dirección de Planeación de <b>“ICATECH”</b>.</dd>
                    <br><dd>I.8 Para los efectos del presente Contrato señala como su domicilio legal, el ubicado en la ' . $direccion_instituto . '</dd>
                </dl>
                <dl><dt>II. <b>"PRESTADOR DE SERVICIOS"</b> declara que:</dt>
                    <br><dd>II.1 Es una persona física, de nacionalidad mexicana, que acredita mediante '. ($tipo_identificacion == 'INE' ? 'credencial para votar' : $tipo_identificacion).' con número de folio '.$folio_identificacion. ', con plena capacidad jurídica y facultades que le otorga la ley, para contratar y obligarse, así como también con los estudios, conocimientos y la experiencia necesaria en la materia de '. $data->espe. ' y conoce plenamente las necesidades de los servicios objeto del presente contrato, así como que ha considerado todos los factores que intervienen para desarrollar eficazmente las actividades que desempeñará.</dd>
                    <br><dd>II.2 Se encuentra al corriente en el pago de sus impuestos y cuenta con el Registro Federal de Contribuyentes número '. $data->rfc. ', expedido por el Servicio de Administración Tributaria de la Secretaría de Hacienda y Crédito Público, conforme a lo dispuesto por los artículos 27 del Código Fiscal de la Federación y 110 fracción I de la Ley de Impuesto sobre la Renta.</dd>
                    <br><dd>II.3 Es conforme de que <b>“ICATECH”</b>, le retenga los impuestos a que haya lugar por concepto de la Prestación de Servicios Profesionales por <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS</b>.</dd>
                    <br><dd>II.4 Bajo protesta de decir verdad, no se encuentra inhabilitado por autoridad competente alguna, así como a la suscripción del presente documento no ha sido parte en juicios del orden civil, mercantil, penal, administrativo o laboral en contra de <b>“ICATECH”</b> o de alguna otra institución pública o privada; y que no se encuentra en algún otro supuesto o situación que pudiera generar conflicto de intereses para prestar los servicios profesionales objeto del presente contrato.</dd>
                    <br><dd>II.5 Para los efectos del presente contrato, señala como su domicilio legal el ubicado en C: '.$data->domicilio. '.</dd>
                </dl>
            </div>
            <div align="justify">Con base en las declaraciones antes expuestas, declaran las partes que es su voluntad celebrar el presente contrato, sujetándose a las siguientes:</div>
            <br>
            <div align="center"><strong> CLÁUSULAS </strong></div>
            <br><div align="justify">
                <dd><b>PRIMERA.- OBJETO DEL CONTRATO</b>. El presente instrumento tiene por objeto establecer al <b>“PRESTADOR DE SERVICIOS”</b> los términos y condiciones que se obliga con <b>“ICATECH”</b>, a brindar sus servicios profesionales bajo el régimen de <b>SUELDOS Y SALARIOS E INGRESOS ASIMILADOS A SALARIOS,</b> para otorgar '. ($data->tipo_curso == 'CURSO' ? 'el curso establecido' : 'la certificación extraordinaria establecida'). '  en el ARC01 y/o ARC02.</dd>
                <br><dd><b>SEGUNDA.- MONTO</b>. El monto que <b>“ICATECH”</b>, pagará al <b>“PRESTADOR DE SERVICIOS”</b> será por la cantidad de <b>$'. $cantidad. ' ('. $data_contrato->cantidad_letras1. ' '. $monto['1']. '/100 M.N.)</b>; por '.($data->tipo_curso == 'CURSO' ? 'curso impartido' : 'certificación extraordinaria').', menos las retenciones que el <b>“ICATECH”</b> le realizará como pago provisional por concepto de Impuesto sobre la Renta, de conformidad con lo establecido al artículo 96 de la Ley del Impuesto sobre la Renta, enterando a las Autoridades Hacendarias las retenciones correspondientes.</dd>
                <br><dd>El monto resultante señalado en el <b>párrafo primero</b> de esta cláusula se otorgará al <b>“PRESTADOR DE SERVICIOS”</b> conforme a la disponibilidad financiera de <b>“ICATECH”</b>; que se realizará en una sola exhibición, por medio de cheque y/o transferencia interbancaria a la cuenta que señala, y se agrega al presente Contrato. Así mismo, se expedirá el comprobante fiscal digital por internet (CFDI), el cual se agrega al presente Instrumento, mismo que deberá cubrir los requisitos fiscales estipulados por la Secretaría de Hacienda y Crédito Público; por lo que el <b>“PRESTADOR DE SERVICIOS”</b> no podrá exigir retribución alguna por ningún otro concepto.</dd>
                <br><dd><b>TERCERA.- DE LA OBLIGACIÓN DEL “PRESTADOR DE SERVICIOS”</b>. Se obliga a desempeñar las obligaciones que contrae en este acto conforme a los procedimientos de control escolar y con todo el sentido ético y profesional que requiere <b>“ICATECH”</b>, de acuerdo con las políticas y reglamentos del mismo para:</dd>
                <Ol type = "I">
                    <li> Diseñar, preparar e impartir '.($data->tipo_curso == 'CURSO' ? 'el curso' : 'la certificación extraordinaria').' a su cargo con toda la diligencia y esmero que exige la calidad de <b>“ICATECH”</b>.</li>
                    <br><li> Vigilar que su '.($data->tipo_curso == 'CURSO' ? 'curso impartido' : 'certificación extraordinaria').' se aproveche íntegramente el tiempo necesario para el mejor desarrollo del mismo.</li>
                    <br><li> Realizar la comprobación final '.($data->tipo_curso == 'CURSO' ? 'del curso impartido' : 'de la certificación extraordinaria').'.</li>
                    <br><li> Respetar las normas de conducta.</li>
                    <br><li> Implementar el Lenguaje Incluyente en la impartición '.($data->tipo_curso == 'CURSO' ? 'del curso' : 'de la certificación extraordinaria').'.</li>
                </Ol>
            </div>
            <div align="justify">
                <dd><b>CUARTA.- SECRETO PROFESIONAL DEL “PRESTADOR DE SERVICIOS”.</b> En el presente contrato se obliga al <b>“PRESTADOR DE SERVICIOS”</b>, a no divulgar por medio de publicaciones, informes, videos, fotografías, medios electrónicos, conferencias o en cualquier otra forma, los datos y resultados obtenidos de los trabajos de este Contrato, sin la autorización expresa de <b>“ICATECH”</b>, pues dichos datos y resultados son considerados confidenciales. Esta obligación subsistirá, aún después de haber terminado la vigencia de este Contrato.</dd>
                <br><dd><b>QUINTA.- VIGENCIA</b>. La vigencia del presente Contrato será conforme a la duración '.($data->tipo_curso == 'CURSO' ? ' del curso' : 'de la certificación extraordinaria').' objeto del presente Instrumento, detallados en la <b>CLÁUSULA PRIMERA</b>; el cual será forzoso al <b>“PRESTADOR DE SERVICIOS”</b> y voluntario para <b>“ICATECH”</b> mismo que podrá darlo por terminado anticipadamente en cualquier tiempo, siempre y cuando existan motivos o razones de interés general, incumpla cualquiera de las obligaciones adquiridas con la formalización del presente instrumento o incurra en alguna de las causales previstas en la Cláusula Octava, mediante notificación por escrito al <b>“PRESTADOR DE SERVICIOS”</b>; en todo caso <b>“ICATECH”</b> deberá cubrir el monto únicamente en cuanto a los servicios prestados.</dd>
                <br><dd>Concluido el término del presente Contrato no podrá haber prórroga automática por el simple transcurso del tiempo y terminará sin necesidad de darse aviso entre las partes. Si terminada la vigencia de este contrato, <b>“ICATECH”</b> tuviere necesidad de seguir utilizando los servicios del <b>“PRESTADOR DE SERVICIOS”</b>, se requerirá la celebración de un nuevo Contrato, sin que éste pueda ser computado con el anterior.</dd>
                <br><dd><b>SEXTA.- SEGUIMIENTO. “ICATECH”</b> a través de los representantes que al efecto designe, tendrá en todo tiempo el derecho de dar seguimiento del estricto cumplimiento de este contrato, en relación a las actividades del <b>“PRESTADOR DE SERVICIOS”</b>.</dd>
                <br><dd><b>SÉPTIMA.- PROPIEDAD DE RESULTADOS Y DERECHOS DE AUTOR</b>. Los documentos, estudios y demás materiales que se generen en la ejecución o como consecuencia de este contrato, serán propiedad de <b>“ICATECH”</b>, obligando al <b>“PRESTADOR DE SERVICIOS”</b> a entregarlos al término del presente instrumento.</dd>
                <br><dd>Se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a responder ilimitadamente de los daños o perjuicios que pudiera causar a <b>“ICATECH”</b> o a terceros, si con motivo de la prestación de los servicios contratados viola derechos de autor, de patentes y/o marcas u otro derecho reservado, por lo que manifiesta en este acto bajo protesta de decir verdad, no encontrarse en ninguno de los supuestos de infracción a la Ley Federal de Derechos de Autor ni a la Ley de Propiedad Industrial.</dd>
                <br><dd>En caso de que sobreviniera alguna reclamación o controversia legal en contra de <b>“ICATECH”</b> por cualquiera de las causas antes mencionadas, la única obligación de éste será dar aviso al <b>“PRESTADOR DE SERVICIOS”</b> en el domicilio previsto en este instrumento para que ponga a salvo a <b>“ICATECH”</b> de cualquier controversia.</dd>
                <br><dd><b>OCTAVA.- RESCISIÓN. “ICATECH”</b> podrá rescindir el presente contrato sin responsabilidad alguna, sin necesidad de declaración judicial, bastando para ello una notificación por escrito cuando concurran causas de interés general, cuando el <b>“PRESTADOR DE SERVICIOS”</b> incumpla algunas de las obligaciones del presente Contrato y demás disposiciones contenidas en las leyes que le sean aplicables y cuando a juicio de <b>“ICATECH”</b> incurra en las siguientes causales:</dd>
            </div>
            <ol type = "I">
                <li>Negligencia o impericia. </li>
                <li>Falta de probidad u honradez.</li>
                <li>Por prestar los servicios de forma ineficiente e inoportuna.</li>
                <li>Por no apegarse a lo estipulado en el presente contrato.</li>
                <li>Por no observar la discreción debida respecto a la información a la que tenga acceso como consecuencia de la información de los servicios encomendados.</li>
                <li>Por suspender injustificadamente la prestación de los servicios o por negarse a corregir lo rechazado por <b>“ICATECH”</b>.</li>
                <li>Por negarse a informar a <b>“ICATECH”</b> sobre los resultados de la prestación del servicio encomendado. </li>
                <li>Por impedir el desempeño normal de las labores durante la prestación de los servicios. </li>
                <li>Si se comprueba que la protesta a que se refiere la Declaración II.4 de <b>“PRESTADOR DE SERVICIOS”</b> se realizó con falsedad.</li>
                <li>Por no otorgar '.($data->tipo_curso == 'CURSO' ? 'el curso' : 'la certificación extraordinaria').' en el tiempo establecido. </li>
            </ol>
            <div align="justify"><dd>Asimismo; en caso de tener evidencias de que '.($data->tipo_curso == 'CURSO' ? 'el curso  no fue impartido' : 'la certificación extraordinaria  no fue impartida').', se procederá a dar por rescindido el contrato, y se interpondrá la acción legal que corresponda.</dd>
                <br><dd>El <b>“ICATECH”</b> podrá dar por terminadas las obligaciones contractuales contraídas con el <b>“PRESTADOR DE SERVICIOS”</b> de forma anticipada.</dd>
                <br><dd><b>“ICATECH”</b> se reservará el derecho de aceptar la terminación anticipada del contrato, sin que ello implique la renuncia a deducir las acciones legales que en su caso procedan.</dd>
                <br><dd><b>NOVENA.- CESIÓN. “PRESTADOR DE SERVICIOS”</b> no podrá en ningún caso ceder total o parcialmente a terceros llámese persona física o persona moral, los derechos y obligaciones derivadas del presente contrato.</dd>
                <br><dd><b>DÉCIMA.- RELACIONES PROFESIONALES. “ICATECH”</b> no adquiere ni reconoce obligación alguna de carácter laboral a favor del <b>“PRESTADOR DE SERVICIOS”</b>, en virtud de no ser aplicables a la relación contractual que consta en este instrumento, los artículos 1º y 8º de la Ley Federal del Trabajo y 123 apartado “A” y “B” de la Constitución Política de los Estados Unidos Mexicanos, por lo que no será considerado al <b>“PRESTADOR DE SERVICIOS”</b> como trabajador de <b>“ICATECH”</b> para los efectos legales y en particular para obtener las prestaciones establecidas en su artículo 5 A, fracciones V, VI y VII de la Ley del Instituto Mexicano del Seguro Social.</dd>
                <br><dd>En el presente instrumento se obliga al <b>“PRESTADOR DE SERVICIOS”</b> a ser el único responsable del cumplimiento con las normas laborales, fiscales, o cualquier otro acto contractual de diversa índole, incluso las de seguridad social e INFONAVIT que pudieran derivarse de la prestación de los servicios aquí contratados, consecuentemente libera de toda responsabilidad a <b>“ICATECH”</b> de las obligaciones que pudieran presentarse por estos conceptos.</dd>
                <br><dd><b>DÉCIMA PRIMERA.- RECONOCIMIENTO CONTRACTUAL</b>. El presente contrato se rige por lo dispuesto en el Título Décimo del Contrato de Prestación de Servicios, Capítulo I, del Código Civil del Estado de Chiapas y del Código Civil Federal en su precepto legal 1803, fracción I, por lo que no existe relación de dependencia ni de subordinación entre <b>“ICATECH”</b> y <b>“PRESTADOR DE SERVICIOS”</b>, ni podrán tenerse como tales los necesarios nexos de coordinación entre uno y otro.</dd>
                <br><dd>El presente contrato constituye el acuerdo de voluntades entre las partes, en relación con el objeto del mismo y deja sin efecto cualquier otra negociación o comunicación entre éstas, ya sea oral o escrita con anterioridad a la fecha de su firma.</dd>
                <br><dd><b>DÉCIMA SEGUNDA</b>.- Manifiestan ambas partes bajo protesta de decir verdad que en el presente contrato no ha mediado dolo, error, mala fe, engaño, violencia, intimidación, ni cualquiera otra causa que pudiera invalidar el contenido y fuerza.</dd>
                <br><dd><b>DÉCIMA TERCERA.- DOMICILIOS</b>. Para los efectos del presente instrumento las partes señalan como sus domicilios legales los estipulados en el Apartado de Declaraciones del presente instrumento legal.</dd>
                <br><dd>Mientras las partes no notifiquen por escrito el cambio de su domicilio, los emplazamientos y demás diligencias judiciales y extrajudiciales, se practicarán en el domicilio señalado en esta cláusula.</dd>
                <br><dd><b>DÉCIMA CUARTA.- RESPONSABILIDAD DEL “PRESTADOR DE SERVICIOS”</b>. Será el responsable de la ejecución de los trabajos y deberá sujetarse en la realización de éstos, a todos aquellos reglamentos administrativos y manuales que las autoridades competentes hayan emitido, así como a las disposiciones establecidas por <b>“ICATECH”</b>.</dd>
                <br><dd><b>DÉCIMA QUINTA</b>.- Las partes convienen que los datos personales insertos en el presente instrumento legal son protegidos por la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Chiapas y la Ley de Transparencia y Acceso a la Información Pública del Estado de Chiapas, así como los Lineamientos Generales de la Custodia y Protección de Datos Personales e Información Reservada y Confidencial en Posesión de los Sujetos Obligados del Estado de Chiapas y demás normatividad aplicable.</dd>
                <br><dd><b>DÉCIMA SEXTA.- JURISDICCIÓN</b>. Para la interpretación y cumplimiento del presente Contrato, así como para todo aquello que no esté expresamente estipulado en el mismo, las partes se someterán a la jurisdicción y competencia de los tribunales del fuero común de la ciudad de Tuxtla Gutiérrez, Chiapas, renunciando al fuero que pudiera corresponderles por razón de su domicilio presente o futuro.</dd>
                <br><dd>Leído que fue el presente contrato a las partes que en él intervienen y una vez enterados de su contenido y alcance legal, son conformes con los términos del mismo y lo suscriben el día de su inicio y ratifican para constancia en el Municipio de '. $data_contrato->municipio. ', Chiapas.</dd>
            </div>';
        }

        //$resp = ['body' => $body, 'body_html' => $body_html];
        return $body_html;
    }

    private function incapacidad($incapacidad, $incapacitado) {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre','fun.curp','fun.cargo','fun.correo','org.nombre as org_nombre','fun.incapacidad')
                    ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                    ->Where('fun.id', $incapacidad->id_firmante)
                    ->First();

                return($firmanteIncapacidad);
            } else {
                // La fecha de hoy NO está dentro del rango
                if($fechaTermino->isPast()) {
                    $newIncapacidadHistory = 'Ini:'.$incapacidad->fecha_inicio.'/Fin:'.$incapacidad->fecha_termino.'/IdFun:'.$incapacidad->id_firmante;
                    array_push($incapacidad->historial, $newIncapacidadHistory);
                    $incapacidad->fecha_inicio = $incapacidad->fecha_termino = $incapacidad->id_firmante = null;
                    $incapacidad = json_encode($incapacidad);

                    DB::Table('tbl_funcionarios')->Where('nombre',$incapacitado)
                        ->Update([
                            'incapacidad' => $incapacidad
                    ]);
                }

                return false;
            }
        }
        return false;
    }

    //obtener el token
    public function generarToken() {

        $resToken = Http::withHeaders([
            'Accept' => 'application/json'
        ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
            'nombre' => 'SISTEM_IVINCAP',
            'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
        ]);

        // Token Prueba
        // $resToken = Http::withHeaders([
        //     'Accept' => 'application/json'
        // ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
        //     'nombre' => 'FirmaElectronica',
        //     'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
        // ]);

        $token = $resToken->json();

        Tokens_icti::Where('sistema','sivyc')->update([
            'token' => $token
        ]);
        return $token;
    }

    // obtener la cadena original
    public function getCadenaOriginal($xmlBase64, $token) {

        $response1 = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        // api prueba
        // $response1 = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response1;
    }

    protected function toMonth($m)
    {
        switch ($m) {
            case 1:
                return "Enero";
            break;
            case 2:
                return "Febrero";
            break;
            case 3:
                return "Marzo";
            break;
            case 4:
                return "Abril";
            break;
            case 5:
                return "Mayo";
            break;
            case 6:
                return "Junio";
            break;
            case 7:
                return "Julio";
            break;
            case 8:
                return "Agosto";
            break;
            case 9:
                return "Septiembre";
            break;
            case 10:
                return "Octubre";
            break;
            case 11:
                return "Noviembre";
            break;
            case 12:
                return "Diciembre";
            break;


        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }
}
