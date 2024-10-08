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
use App\Models\supre;
use App\Models\folio;
use Carbon\Carbon;
use PDF;

class ESupreController extends Controller
{
    public function generar_xml($id_supre){
        // dd($id_supre);
        $info = DB::Table('folios')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo',
                    'tabla_supre.no_memo','folios.id_folios')
                ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
                ->Join('tbl_unidades','tbl_unidades.unidad','tabla_supre.unidad_capacitacion')
                ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('tabla_supre.id',$id_supre)
                ->First();

        $nameFileOriginal = 'solicitud de suficiencia presupuestal '.$info->clave.'.pdf';
        $numDocs = DocumentosFirmar::Where('tipo_archivo', 'supre')->Where('numero_o_clave', $info->clave)->WhereIn('status',['CANCELADO','CANCELADO ICTI'])->Get()->Count();
        $numDocs = '0'.($numDocs+1);
        $numOficioBuilder = explode('/',$info->no_memo);
        $position = count($numOficioBuilder) - 2;
        array_splice($numOficioBuilder, $position, 0, $numDocs);
        $numOficio = implode('/',$numOficioBuilder);


        $body = $this->create_body($id_supre, $info->no_memo); //creacion de body hemos reemplazado numOficio por $info->no_memo mientras se autoriza el uso del consecutivo electronico
        if(is_null($body))
        {
            $error = ['error' => 1];
            return $error;
        }

        $numFirmantes = '1';
        $arrayFirmantes = [];

        $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre','fun.curp','fun.cargo','fun.correo','org.nombre AS org_nombre','fun.incapacidad')
                            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
                            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
                            ->Where('org.id_parent',1)
                            ->Where('fun.activo', 'true')
                            ->Where('u.unidad', $info->ubicacion)
                            ->First();

        // Info de director firmante
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

        $anexos= ['_attributes' =>
            [
                'nombre_anexo' => 'formato-de-solcitud-de-suficiencia-presupuestal-'.$numOficio.'.pdf',
                'md5_anexo' => $body['anexoMD5']
            ]
        ];
        array_pop($body);

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
                'cuerpo' => [strip_tags($body['supre'])]
            ],
            'anexos' => [
                '_attributes' => [
                    'num_anexos' => '1'
                ],
                'anexo' => $anexos
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
                'asunto_docto' => 'Solicitud de Suficiencia Presupuestal',
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
            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','supre')->WhereIn('status',['CANCELADO','EnFirma'])->First();
            if(isset($dataInsert->obj_documento)) {
                $firmantes = json_decode($dataInsert->obj_documento, true);
                if($dataInsert->status != 'CANCELADO') {
                    foreach($firmantes['firmantes']['firmante']['0'] as $firmante) {
                        if(isset($firmante['_attributes']['certificado'])) {
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

            $dataInsert->obj_documento = json_encode($ArrayXml);
            $dataInsert->obj_documento_interno = json_encode($body);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'supre';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            $dataInsert->num_oficio = $numOficio;
            $dataInsert->save();

            return TRUE;
        } else {
            $error = ['error' => 2];
            return $error;
        }

    }

    public function create_body($id, $numOficio = NULL) {
        // dd($id);
        $body_html = array();
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data_supre = supre::WHERE('id', '=', $id)->FIRST(); //cambiar data2 a data_supre en tabla supre
        $data= supre::SELECT('tabla_supre.fecha','folios.folio_validacion','folios.importe_hora','folios.iva','folios.importe_total',
                        'folios.comentario','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'tbl_cursos.curso AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.tipo_curso',
                        'tbl_cursos.modinstructor','tbl_cursos.fecha_apertura', 'tbl_cursos.cp')
                    ->WHERE('id_supre', '=', $id )
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();

        $data_folio = folio::WHERE('id_supre', '=', $id)->WHERE('status', '!=', 'Cancelado')->GET();
        $date = strtotime($data_supre->fecha);
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);
        $unidad = tbl_unidades::SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion','direccion')
            ->WHERE('unidad', '=', $data_supre->unidad_capacitacion)
            ->FIRST();
        $unidad->cct = substr($unidad->cct, 0, 4);
        $direccion = explode("*", $unidad->direccion);

        $funcionarios = $this->funcionarios_supre($data_supre->unidad_capacitacion);

        //table supre
        $inicio = date('Y-m-d', strtotime($data[0]->fecha_apertura));
        $Curso = $data[0];

        if($inicio < date('Y-m-d', strtotime('12-10-2023')) && $Curso->cp > 5) {
            $Curso->cp = $Curso->cp - 1;
        } else if ($inicio < date('Y-m-d', strtotime('12-10-2023')) && $Curso->cp == 5) {
            $Curso->cp = 55; // este id es del antiguo C.P. 5
        }

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

        $tipop = $data[0]['modinstructor'];


        $body_html['supre'] = '<div align=right> <b>Unidad de Capacitación '. $unidad->ubicacion.'</b> </div>
        <div align=right> <b>Memorandum No. ';
        if(is_null($numOficio)) {
            $body_html['supre'] = $body_html['supre'].$data_supre->no_memo;
        } else {
            $body_html['supre'] = $body_html['supre'].$numOficio;
        }
        $body_html['supre'] = $body_html['supre'].'</b></div>
        <div align=right> <b>'.$data_supre->unidad_capacitacion.', Chiapas '.$D.' de '.$M.' del '.$Y.'.</b></div>

        <br><br><b>C. '.$funcionarios['destino'].'.</b>
        <br>'.$funcionarios['destinop'].'.
        <br><br>Presente.

        <br><p class="text-justify">Por medio del presente me permito solicitar suficiencia presupuestal, en la partida 12101 '.$data[0]->modinstructor.', para la contratación de instructores externos para la impartición de';
        if ($data[0]->tipo_curso=='CERTIFICACION') {
            $body_html['supre'] =  $body_html['supre'] . ' certificación extraordinaria';
        } else {
            $body_html['supre'] =  $body_html['supre'] . ' curso';
        }

        $body_html['supre'] =  $body_html['supre'] . ' de la';

        if ($unidad->cct == '07EI') {
            $body_html['supre'] =  $body_html['supre'] . ' Unidad de Capacitación <b> '. $unidad->ubicacion.'</b>,';
        } else {
            $body_html['supre'] =  $body_html['supre'] . ' Acción Movil <b> '.$data_supre->unidad_capacitacion.'</b>,';
        }

        $body_html['supre'] =  $body_html['supre'] . ' de acuerdo a los números de folio que se indican en el cuadro analítico siguiente y acorde a lo que se describe en el formato anexo.</p>
        <br><div align=justify><b>Números de Folio</b></div>
        <table class="table table-bordered">
            <thead>
            </thead>
            <tbody>';
                foreach ($data_folio as $key=>$value ) {
                    $body_html['supre'] =  $body_html['supre'] . '<tr><td>'.$value->folio_validacion.'</td>';
                }
            $body_html['supre'] =  $body_html['supre'] . '</tbody>
        </table>
        <br><p class="text-left"><p>Sin más por el momento, aprovecho la ocasión para enviarle un cordial saludo.</p></p>
        <br><p class="text-left"><p>Atentamente.</p></p>';

        $body_html['tabla'] = '<div align=center><b><h6>INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLOGICA DEL ESTADO DE CHIAPAS
            <br>DIRECCIÓN DE PLANEACIÓN
            <br>DEPARTAMENTO DE PROGRAMACIÓN Y PRESUPUESTO
            <br>FORMATO DE SOLICITUD DE SUFICIENCIA PRESUPUESTAL
            <br>UNIDAD DE CAPACITACIÓN '.$data_supre->unidad_capacitacion.' ANEXO DE MEMORÁNDUM No. ';
            if(is_null($numOficio)) {
                $body_html['tabla'] = $body_html['tabla'].$data_supre->no_memo;
            } else {
                $body_html['tabla'] = $body_html['tabla'].$numOficio;
            }
            $body_html['tabla'] = $body_html['tabla'].'</h6></b> </div>
        </div>
        <div class="form-row">
            <table width="700" class="table table-striped" id="table-one">
                <thead>
                    <tr class="active">
                        <td scope="col"><small style="font-size: 10px;">No. DE SUFICIENCIA</small></td>
                        <td scope="col" ><small style="font-size: 10px;">FECHA</small></td>
                        <td scope="col" ><small style="font-size: 10px;">INSTRUCTOR EXTERNO</small></td>
                        <td scope="col" width="10px"><small style="font-size: 10px;">UNIDAD/ACCION MOVIL</small></td>
                        <td scope="col" ><small style="font-size: 10px;">CURSO/CERTIFICACION</small></td>
                        <td scope="col" ><small style="font-size: 10px;">NOMBRE</small></td>
                        <td scope="col"><small style="font-size: 10px;">CLAVE DEL GRUPO</small></td>
                        <td scope="col" ><small style="font-size: 10px;">ZONA ECÓNOMICA</small></td>
                        <td scope="col"><small style="font-size: 10px;">HSM (horas)</small></td>';
                        if($data[0]['fecha_apertura'] <  '2023-10-12') {
                            $body_html['tabla'] = $body_html['tabla']. '<td scope="col" ><small style="font-size: 10px;">IMPORTE POR HORA</small></td>';
                            if($tipop == 'HONORARIOS'){$body_html['tabla'] = $body_html['tabla'].'<td scope="col"><small style="font-size: 10px;">IVA 16%</small></td>';}
                            $body_html['tabla'] = $body_html['tabla'].'<td scope="col" ><small style="font-size: 10px;">PARTIDA/ CONCEPTO</small></td>
                            <td scope="col"><small style="font-size: 10px;">IMPORTE</small></td>';
                        } else {
                            $body_html['tabla'] = $body_html['tabla'].'<td scope="col" ><small style="font-size: 10px;">COSTO POR HORA</small></td>
                            <td scope="col"><small style="font-size: 10px;">TOTAL IMPORTE</small></td>
                            <td scope="col" ><small style="font-size: 10px;">PARTIDA/ CONCEPTO</small></td>';
                        }
                        $body_html['tabla'] = $body_html['tabla'].'<td scope="col" ><small style="font-size: 10px;">OBSERVACION<small></td>
                    </tr>
                </thead>
                <tbody>';
                    foreach ($data as $key=>$item) {
                        $body_html['tabla'] = $body_html['tabla']. '<tr>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->folio_validacion.'</small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->fecha.'</small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->nombre.' '.$item->apellidoPaterno.' '.$item->apellidoMaterno.'</small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->unidad.'</small></td>';
                            if ($item->tipo_curso=='CERTIFICACION') {
                                $body_html['tabla'] = $body_html['tabla'].'<td><small style="font-size: 10px;">CERTIFICACIÓN</small></td>';
                            } else {
                                $body_html['tabla'] = $body_html['tabla'].'<td><small style="font-size: 10px;">CURSO</small></td>';
                            }
                            $body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->curso_nombre.'</td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->clave.'</small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->ze.'</small></td>
                            <td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->dura.'</small></td>';
                            if($data[0]['fecha_apertura'] <  '2023-10-12') {
                                $body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">'. number_format($item->importe_hora, 2, '.', ',') .'</td>';
                                if($item->modinstructor == 'HONORARIOS'){$body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">'. number_format($item->iva, 2, '.', ',').'</td>';}
                                $body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">';
                                if($item->modinstructor == 'HONORARIOS' || $item->modinstructor == 'HONORARIOS Y ASIMILADOS A SALARIOS'){$body_html['tabla'] = $body_html['tabla'].'12101 HONORARIOS'; } else {$body_html['tabla'] = $body_html['tabla'].'12101 ASIMILADOS A SALARIOS'; } $body_html['tabla'] = $body_html['tabla'].'</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">'. number_format($item->importe_total, 2, '.', ',') .'</td>';
                            } else {
                                $body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">'. number_format($criterio->monto, 2, '.', ',').'</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">'. number_format($item->importe_total, 2, '.', ',') .'</td>
                                <td scope="col" class="text-center"><small style="font-size: 10px;">';
                                if($item->modinstructor == 'HONORARIOS' || $item->modinstructor == 'HONORARIOS Y ASIMILADOS A SALARIOS'){$body_html['tabla'] = $body_html['tabla'].'12101 HONORARIOS'; } else { $body_html['tabla'] = $body_html['tabla'].'12101 ASIMILADOS A SALARIOS'; }$body_html['tabla'] = $body_html['tabla'].'</td>';
                            }
                            $body_html['tabla'] = $body_html['tabla'].'<td scope="col" class="text-center"><small style="font-size: 10px;">'.$item->comentario.'</small></td>
                        </tr>';
                    }
                    $body_html['tabla'] = $body_html['tabla'].'</tbody>
            </table>';

        //Generación de MD5 al anexo
        $uuid = null;
        $bodyTabla = $body_html['tabla'];
        $pdf = PDF::loadView('layouts.pdfpages.solicitudsuficiencia', compact('bodyTabla','distintivo','direccion','uuid','funcionarios'));
        $pdf->setPaper('A4', 'Landscape');
        $pdfContent = $pdf->output();
        $body_html['anexoMD5'] = md5($pdfContent);

        return $body_html;
    }

    private function incapacidad($incapacidad, $incapacitado) {
        $fechaActual = now();
        if(isset($incapacidad->fecha_inicio) && !is_null($incapacidad->fecha_inicio)) {
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

    public function funcionarios_supre($unidad) {
        $query = clone $direc = clone $ccp1 = clone $ccp2 = clone $delegado = clone $destino = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo','f.incapacidad')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true');

        $direc = $direc->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $destino = $destino->Where('o.id',9)->First();
        $ccp1 = $ccp1->Where('o.id',6)->First();
        $ccp2 = $ccp2->Where('o.id',13)->First();
        $delegado = $delegado->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $unidad)
            ->First();

        //parte de checado de incapacidad
        $direc = $this->incapacidad(json_decode($direc->incapacidad), $direc->nombre) ?: $direc;
        $delegado = $this->incapacidad(json_decode($delegado->incapacidad), $delegado->nombre) ?: $delegado;

        $funcionarios = [
            'director' => $direc->nombre,
            'directorp' => $direc->cargo,
            'destino' => $destino->nombre,
            'destinop' => $destino->cargo,
            'ccp1' => $ccp1->nombre,
            'ccp1p' => $ccp1->cargo,
            'ccp2' => $ccp2->nombre,
            'ccp2p' => $ccp2->cargo,
            'delegado' => $delegado->nombre,
            'delegadop' => $delegado->cargo,
            'elabora' => strtoupper(Auth::user()->name),
            'elaborap' => strtoupper(Auth::user()->puesto)
        ];

        return $funcionarios;
    }
}
