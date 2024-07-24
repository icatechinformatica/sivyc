<?php

namespace App\Http\Controllers\efirma;

use App\Models\especialidad_instructor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\DocumentosFirmar;
use Illuminate\Http\Request;
use App\Models\tbl_unidades;
use App\Models\Tokens_icti;
use App\Models\tbl_curso;
use App\Models\supre;
use App\Models\folio;
use Carbon\Carbon;
use PDF;

class EValsupreController extends Controller
{
    public function generar_xml($id_supre){
        // dd($id_supre);
        $info = DB::Table('folios')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo',
                    'tabla_supre.folio_validacion','folios.id_folios')
                ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
                ->Join('tbl_unidades','tbl_unidades.unidad','tabla_supre.unidad_capacitacion')
                ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('tabla_supre.id',$id_supre)
                ->First();

        $nameFileOriginal = 'validacion de suficiencia presupuestal '.$info->clave.'.pdf';
        $numDocs = DocumentosFirmar::Where('tipo_archivo', 'valsupre')->Where('numero_o_clave', $info->clave)->WhereIn('status',['CANCELADO','CANCELADO ICTI'])->Get()->Count();
        $numDocs = '0'.($numDocs+1);
        $numOficioBuilder = explode('/',$info->folio_validacion);
        $position = count($numOficioBuilder) - 2;
        array_splice($numOficioBuilder, $position, 0, $numDocs);
        $numOficio = implode('/',$numOficioBuilder);

        $body = $this->create_body($id_supre, $info->folio_validacion); //creacion de body hemos reemplazado numOficio por $info->no_memo mientras se autoriza el uso del consecutivo electronico
        if(is_null($body))
        {
            $error = ['error' => 1];
            return $error;
        }

        $numFirmantes = '1';
        $arrayFirmantes = [];

        $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
                            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
                            ->Where('org.id',9)
                            ->Where('fun.activo', 'true')
                            ->First();

        // Info de director firmante
        if(isset($dataFirmante->incapacidad)) {
            $incapacidadFirmante = $this->incapacidad(json_decode($dataFirmante->incapacidad), $dataFirmante->funcionario);
            if($incapacidadFirmante != FALSE) {
                $dataFirmante = $incapacidadFirmante;
            }
        }
        $temp = ['_attributes' =>
            [
                'curp_firmante' => $dataFirmante->curp,
                'nombre_firmante' => $dataFirmante->funcionario,
                'email_firmante' => $dataFirmante->correo,
                'tipo_firmante' => 'FM'
            ]
        ];

        array_push($arrayFirmantes, $temp);

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => Auth::user()->name,
                    'cargo_emisor' => Auth::user()->puesto,
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
                'asunto_docto' => 'Validación de Suficiencia Presupuestal',
                'tipo_docto' => 'OFC',
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
        if ($response->json()['cadenaOriginal'] != null) {
            $sobrescribir = True;
            // Actualizar  este dataInsert ya que se pondra un consecutivo interno y poder hacer mas documentos por si alguno se cancela
            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','valsupre')->Where('status','EnFirma')->First();
            if(isset($dataInsert->obj_documento)) {
                $firmantes = json_decode($dataInsert->obj_documento, true);
                foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                    if(isset($firmante['_attributes']['certificado'])) {
                        // generar un return con error (4)
                        $sobrescribir = False;
                    }
                }
            } else {
                $sobrescribir = False;
            }

            if(!$sobrescribir) {
                $dataInsert = new DocumentosFirmar();
            }

            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($body);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'valsupre';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            $dataInsert->num_oficio = $numOficio;
            // $dataInsert->md5_file = $md5;
            $dataInsert->save();

            return TRUE;
        } else {
            $error = ['error' => 2];
            return $error;
        }

    }

    public function create_body($id, $numOficio = NULL) {
        // dd($numOficio);
        $body_html = array();
        $supre = new supre;
        $curso = new tbl_curso;
        $recursos = array();
        $i = 0;
        $data = supre::SELECT('tabla_supre.*','folios.folio_validacion AS folio_unidad','folios.importe_hora','folios.iva','folios.importe_total',
                        'folios.comentario','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                        'tbl_cursos.unidad','tbl_cursos.modinstructor','tbl_cursos.curso AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze',
                        'tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.tipo_curso','tbl_cursos.modinstructor',
                        'tbl_cursos.cp','tbl_cursos.fecha_apertura')
                    ->WHERE('id_supre', '=', $id )
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();

        $numOficioSupre = DB::Table('documentos_firmar')->Where('status','VALIDADO')
            ->Where('tipo_archivo','supre')
            ->Where('numero_o_clave',$data[0]->clave)
            ->Value('num_oficio');

        $inicio = date('Y-m-d', strtotime($data[0]->fecha_apertura));
        $Curso = $data[0];

        if ($Curso->ze == 'II')
        {
            $queryraw = "jsonb_array_elements(ze2->'vigencias') AS vigencia";
        }
        else
        {
            $queryraw = "jsonb_array_elements(ze3->'vigencias') AS vigencia";
        }

        $criterio = DB::table('criterio_pago')->select('fecha', 'monto')
            ->fromSub(function ($query) use ($Curso, $inicio, $queryraw) {
                $query->selectRaw("(vigencia->>'fecha')::date AS fecha, (vigencia->>'monto')::numeric AS monto")
                    ->from('criterio_pago')
                    ->crossJoin(DB::raw($queryraw))
                    ->where('id', $Curso->cp)
                    ->whereRaw("(vigencia->>'fecha')::date <= ?", [$inicio]);
            }, 'sub')
            ->orderBy('fecha', 'DESC')
            ->limit(1)
            ->first();

        $direccion = tbl_unidades::WHERE('unidad',$Curso->unidad_capacitacion)->VALUE('direccion');
        $direccion = explode("*", $direccion);

        $date = strtotime($data[0]->fecha);
        $D = date('d', $date);
        $M = $this->monthToString(date('m',$date));
        $Y = date("Y",$date);

        $datev = strtotime($data[0]->fecha_validacion);
        $Dv = date('d', $datev);
        $Mv = $this->monthToString(date('m',$datev));
        $Yv = date("Y",$datev);

        $funcionarios = $this->funcionarios_valsupre($data[0]->unidad_capacitacion);
        // dd($numOficio);

        $body_html = '<div>
        <div id="wrappertop">
            <div align=center>
                <FONT SIZE=0><b>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS<br/>
                <FONT SIZE=0>DIRECCION DE PLANEACION</FONT><br/>
                <FONT SIZE=0>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO</FONT><br/>
                <FONT SIZE=0>FORMATO DE VALIDACIÓN DE SUFICIENCIA PRESUPUESTAL</FONT><br/>
                <FONT SIZE=0>EN ATENCIÓN AL MEMORÁNDUM ';
                if(is_null($numOficioSupre)) {
                    $body_html = $body_html . $data[0]->no_memo;
                } else {
                    $body_html = $body_html . $numOficioSupre;
                }
                $body_html = $body_html . '</FONT></p>
            </div>
            <div class="c"><FONT SIZE=0>Folio de Validación: ';
            if(is_null($numOficio)) {
                $body_html = $body_html . $data[0]->folio_validacion;
            } else {
                $body_html = $body_html .$numOficio;
            }
            $body_html = $body_html . '<br/>
            Fecha: '.$Dv.' de '.$Mv.' del '.$Yv.'</FONT>
            </div>
            <div class="b"> <FONT SIZE=0>UNIDAD DE CAPACITACIÓN '.$data[0]->unidad_capacitacion.'</font><br/>
                <FONT SIZE=0><b>C. '.$funcionarios['director'].'</b></FONT><br/>
                <FONT SIZE=0><b>'.$funcionarios['directorp'].'</b></FONT><br/>
                <FONT SIZE=0><b>PRESENTE</b></FONT><br/></div>
                <div class="d"> <FONT SIZE=0>En atención a su solicitud con memorándum No.';
                if(is_null($numOficioSupre)) {
                    $body_html = $body_html . $data[0]->no_memo;
                } else {
                    $body_html = $body_html . $numOficioSupre;
                }
                $body_html = $body_html . ' de fecha '.$D.' de '.$M.' del '.$Y.'; me permito comunicarle lo siguiente:<br/></font>
                    <font size=0>La Secretaria de Hacienda aprobó el presupuesto del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas, en lo general para el Ejercicio Fiscal '.$Y.', en ese sentido, con Fundamento en el Art. 13 Y Art. 38 del decreto de presupuesto
                    de egresos del Estado de Chiapas para el Ejercicio Fiscal '.$Y.' y en apego al tabulador de pagos del Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas por servicios de ';
                    if($data[0]->tipo_curso == 'CURSO'){
                        $body_html = $body_html . 'Capacitación';
                    } else {
                        $body_html = $body_html . 'Certificación Extraordinaria';
                    }
                    $body_html = $body_html .', al Padrón de Instructores externos del ICATECH
                    y a la clave de autorización de apertura de cursos y certificación, y demás disposiciones normativas aplicables vigentes; le informo que una vez revisada su solicitud y la información descrita en el formato de Validación de Suficiencia Presupuestal, se otorga la Validación
                    Presupuestal, con el fin de que conforme a lo indicado en la normatividad aplicable vigente se continúe y se cumpla con los procedimientos administrativos que correspondan, observando además el contrato de prestación de servicios profesionales por honorarios ';
                    if($data[0]->modinstructor=='ASIMILADOS A SALARIOS') {
                        $body_html = $body_html .'asimilados a salarios ';
                    }
                    $body_html = $body_html .'en su modalidad de ';
                    if($data[0]->tipo_curso == 'CURSO') {
                        $body_html = $body_html . 'Horas-Curso ';
                    } else {
                        $body_html = $body_html . 'Certificación Extraordinaria ';
                    }
                    $body_html = $body_html .'que celebran el ICATECH con el prestador de servicios. ';
                    if($data['0']->cp == 12 || $data['0']->cp == 11) {
                        $body_html = $body_html . 'Es importante mencionar que la presente validacion tendrá efecto financiero en el mes de diciembre del ejercicio fiscal 2023. ';
                    }
                    $body_html = $body_html .'<br/></font>
                    <br><font size=0>Por lo anterior, me permito remitir a usted el original de la solicitud, así como su respectivo respaldo documental, debidamente validado presupuestalmente.<br/></font>
                    <font size=0>La presente validación presupuestal no implica ninguna autorización de pago de recursos, si no que únicamente se refiere a la verificación de la disponibilidad presupuestal, No omito manifestarle que, en estricto apego a la normatividad vigente establecida,
                    el área administrativa solicitante, es responsable de la correcta aplicación de los recursos públicos validados, en tal sentido el ejercicio y comprobación del gasto, deberá sujetarse a las disposiciones legales aplicables para tal efecto.<br/></font>
                </div>
            <br>
        </div>
        <div class="form-row">
            <table width="700"  class="table table-striped" id="table-one">
                <thead>
                    <tr class="active">
                        <td width="10px"><small style="font-size: 8px;">No. DE SUFICIENCIA</small></td>
                        <td scope="col" ><small style="font-size: 8px;">FECHA</small></td>
                        <td scope="col" ><small style="font-size: 8px;">INSTRUCTOR EXTERNO</small></td>
                        <td width="10px"><small style="font-size: 8px;">UNIDAD/ ACCION MOVIL</small></td>
                        <td scope="col" style="width: 12px"><small style="font-size: 8px;">CURSO/ CERTIFCACION</small></td>
                        <td scope="col" style="width: 100px;"><small style="font-size: 8px;">NOMBRE</small></td>
                        <td scope="col"><small style="font-size: 8px;">CLAVE DEL GRUPO</small></td>
                        <td scope="col" style="width: 10px;"><small style="font-size: 8px;">ZONA ECÓNOMICA</small></td>
                        <td scope="col" style="width: 20px"><small style="font-size: 8px;">HSM (horas)</small></td>';
                        if($data[0]['fecha_apertura'] <  '2023-10-12') {
                            $body_html = $body_html .'<td scope="col" style="width: 20px"><small style="font-size: 8px;">IMPORTE POR HORA</small></td>';
                            if($tipop->modinstructor == 'HONORARIOS') {
                                $body_html = $body_html . '<td scope="col" style="width: 20px"><small style="font-size: 8px;">IVA 16%</small></td>';
                            }
                            $body_html = $body_html .'<td scope="col" style="width: 20px"><small style="font-size: 8px;">PARTIDA/ CONCEPTO</small></td>
                            <td scope="col"><small style="font-size: 8px;">IMPORTE</small></td>';
                        } else {
                            $body_html = $body_html .'<td scope="col" style="width: 20px"><small style="font-size: 8px;">COSTO POR HORA</small></td>
                            <td scope="col"><small style="font-size: 8px;">TOTAL IMPORTE</small></td>
                            <td scope="col" style="width: 20px"><small style="font-size: 8px;">PARTIDA/ CONCEPTO</small></td>';
                        }
                        $body_html = $body_html .'<td scope="col" style="width: 20px"><small style="font-size: 8px;">Fuente de Financiamiento</small></td>
                        <td width="140px" ><small style="font-size: 8px;">OBSERVACION</small></td>
                    </tr>
                </thead>
                <tbody>';
                    foreach ($data as $key=>$item) {
                        $body_html = $body_html .'<tr>
                            <td><small style="font-size: 8px;">'.$item->folio_unidad.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->fecha.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->nombre.' '.$item->apellidoPaterno.' '.$item->apellidoMaterno.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->unidad.'</small></td>';
                            if ($item->tipo_curso=='CERTIFICACION') {
                                $body_html = $body_html .'<td><small style="font-size: 8px;">CERTIFICACIÓN</small></td>';
                            } else {
                                $body_html = $body_html .'<td><small style="font-size: 8px;">CURSO</small></td>';
                            }
                            $body_html = $body_html .'<td><small style="font-size: 8px;">'.$item->curso_nombre.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->clave.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->ze.'</small></td>
                            <td><small style="font-size: 8px;">'.$item->dura.'</small></td>';
                            if($data[0]['fecha_apertura'] <  '2023-10-12') {
                                $body_html = $body_html .'<td><small style="font-size: 8px;">'.number_format($item->importe_hora, 2, '.', ',').'</small></td>';
                                if($item->modinstructor == 'HONORARIOS') {
                                    $body_html = $body_html .'<td><small style="font-size: 8px;">'.number_format($item->iva, 2, '.', ',').'</small></td>';
                                }
                                $body_html = $body_html ."<input id='hombre".$key."' ".'name="hombre" hidden value="'.$item->hombre.'">
                                <input id="mujer'.$key.'" name="mujer" hidden value="'.$item->mujer.'">
                                <td><small style="font-size: 8px;">';
                                if($item->modinstructor == 'HONORARIOS') {
                                    $body_html = $body_html .'12101 Honorarios';
                                } else {
                                    $body_html = $body_html .'12101 Asimilados a Salarios';
                                }
                                $body_html = $body_html .'</small></td>
                                <td><small style="font-size: 8px;">'.number_format($item->importe_total, 2, '.', ',').'</small></td>';
                            } else {
                                $body_html = $body_html .'<td><small style="font-size: 8px;">'.number_format($criterio->monto, 2, '.', ',').'</small></td>
                                <td><small style="font-size: 8px;">'.number_format($item->importe_total, 2, '.', ',').'</small></td>
                                <input id="hombre'.$key.'" name="hombre" hidden value="'.$item->hombre.'">
                                <input id="mujer'.$key.'" name="mujer" hidden value="'.$item->mujer.'">
                                <td><small style="font-size: 8px;">';
                                if($item->modinstructor == 'HONORARIOS') {
                                    $body_html = $body_html .'12101 Honorarios';
                                } else {
                                    $body_html = $body_html .'12101 Asimilados a Salarios';
                                }
                                $body_html = $body_html .'</small></td>';
                            }
                            $body_html = $body_html .'<td style="text-align: center; font-size: 10px;"><small>';
                                if($data[0]->financiamiento == NULL) {
                                    $body_html = $body_html .'Federal';
                                } else if($data[0]->financiamiento == 'FEDERAL Y ESTATAL') {
                                    $body_html = $body_html .'Federal '.$data[0]->porcentaje_financiamiento['federal'].'%<br>
                                    Estatal '.$data[0]->porcentaje_financiamiento['estatal'].'%';
                                }else {
                                    $body_html = $body_html .$data[0]->financiamiento;
                                }
                                $body_html = $body_html .'</small></td>
                            <td><small style="font-size: 8px;">'.$item->comentario.'</small></td>
                        </tr>';
                    }
                    $body_html = $body_html .'</tbody>
            </table>
        </div>
    </div>';
    if(!is_null($data[0]->observacion_validacion)) {
        $body_html = $body_html .'<div class="d">
            <small><small><b>Observación del Departamento de Programación y Presupuesto:</b> '.$data[0]->observacion_validacion.'</small></small>
        </div><br>';
    }

        return $body_html;
    }

    private function incapacidad($incapacidad, $incapacitado) {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
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

    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }

    public function funcionarios_valsupre($unidad) {
        $query = clone $direc = clone $ccp1 = clone $ccp2 = clone $ccp3 = clone $delegado = clone $remitente = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true');

        $direc = $direc->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $remitente = $remitente->Where('o.id',9)->First();
        $ccp1 = $ccp1->Where('o.id',1)->First();
        $ccp2 = $ccp2->Where('o.id',6)->First();
        $ccp3 = $ccp3->Where('o.id',13)->First();
        $delegado = $delegado->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $unidad)
            ->First();

        $funcionarios = [
            'director' => $direc->nombre,
            'directorp' => $direc->cargo,
            'remitente' => $remitente->nombre,
            'remitentep' => $remitente->cargo,
            'ccp1' => $ccp1->nombre,
            'ccp1p' => $ccp1->cargo,
            'ccp2' => $ccp2->nombre,
            'ccp2p' => $ccp2->cargo,
            'ccp3' => $ccp3->nombre,
            'ccp3p' => $ccp3->cargo,
            'delegado' => $delegado->nombre,
            'delegadop' => $delegado->cargo,
            'elabora' => strtoupper(Auth::user()->name),
            'elaborap' => strtoupper(Auth::user()->puesto)
        ];

        return $funcionarios;
    }
}
