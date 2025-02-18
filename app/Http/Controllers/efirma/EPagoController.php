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
use App\Models\Tokens_icti;
use App\Models\contratos;
use App\Models\folio;
use Carbon\Carbon;
use PDF;

class EPagoController extends Controller
{
    public function generar_xml($id_pago){
        // dd($id_pago);
        $info = DB::Table('pagos')->Select('tbl_unidades.*','tbl_cursos.clave','tbl_cursos.nombre','tbl_cursos.curp','instructores.correo',
                    'contratos.numero_contrato','folios.id_folios','pagos.no_memo')
                ->Join('contratos','contratos.id_contrato','pagos.id_contrato')
                ->Join('folios','folios.id_folios','contratos.id_folios')
                ->Join('tabla_supre','tabla_supre.id','folios.id_supre')
                ->Join('tbl_unidades','tbl_unidades.unidad','tabla_supre.unidad_capacitacion')
                ->Join('tbl_cursos','tbl_cursos.id','folios.id_cursos')
                ->join('instructores','instructores.id','tbl_cursos.id_instructor')
                ->Where('pagos.id',$id_pago)
                ->First();

        $nameFileOriginal = 'solicitud de pago '.$info->clave.'.pdf';
        $numDocs = DocumentosFirmar::Where('tipo_archivo', 'Solicitud Pago')->Where('numero_o_clave', $info->clave)->WhereIn('status',['CANCELADO','CANCELADO ICTI'])->Get()->Count();
        $numDocs = '0'.($numDocs+1);
        $numOficioBuilder = explode('/',$info->no_memo);
        $position = count($numOficioBuilder) - 2;
        array_splice($numOficioBuilder, $position, 0, $numDocs);
        $numOficioInterno = implode('/',$numOficioBuilder);
        $numOficio = $info->no_memo;



        $body = $this->create_body($info->id_folios, $info->no_memo); //creacion de body hemos reemplazado numOficio por $info->no_memo mientras se autoriza el uso del consecutivo electronico
        $numFirmantes = '1';
        $arrayFirmantes = [];

        $funcionarios = $this->funcionarios($info->ubicacion);
        $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('fun.titular', true)
            ->Where('u.unidad', $info->ubicacion)
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
                    'nombre_emisor' => $funcionarios['director'],
                    'cargo_emisor' => $funcionarios['directorp'],
                    'dependencia_emisor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas'
                    // 'curp_emisor' => $dataEmisor->curp
                ],
            ],
            'receptores' => [
                'receptor' => [
                    '_attributes' => [
                        'nombre_receptor' => $funcionarios['destino'],
                        'cargo_receptor' => $funcionarios['destinop'],
                        'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                        'tipo_receptor' => 'JDP'
                    ]
                ]
                // 'receptor' => [
                //     0 => ['_attributes' => [
                //             'nombre_receptor' => $funcionarios['destino'],
                //             'cargo_receptor' => $funcionarios['destinop']. '.- Presente',
                //             'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                //             'tipo_receptor' => 'JDP'
                //     ]],
                //     1 => ['_attributes' => [
                //         'nombre_receptor' => $funcionarios['ccp1'],
                //         'cargo_receptor' => $funcionarios['ccp1p']. '.- Para su conocimiento',
                //         'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                //         'tipo_receptor' => 'CC'
                //     ]],
                //     2 => ['_attributes' => [
                //         'nombre_receptor' => $funcionarios['ccp2'],
                //         'cargo_receptor' => $funcionarios['ccp2p']. '.- Mismo fin',
                //         'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                //         'tipo_receptor' => 'CC'
                //     ]],
                //     3 => ['_attributes' => [
                //         'nombre_receptor' => $funcionarios['delegado'],
                //         'cargo_receptor' => $funcionarios['delegadop']. '.- Mismo fin',
                //         'dependencia_receptor' => 'Instituto de Capacitación y Vinculación Tecnológica del Estado de Chiapas',
                //         'tipo_receptor' => 'CC'
                //     ]]
                // ]
            ],
            'archivo' => [
                '_attributes' => [
                    'nombre_archivo' => $nameFileOriginal
                    // 'md5_archivo' => $md5
                    // 'checksum_archivo' => utf8_encode($text)
                ],
                // 'cuerpo' => ['Por medio de la presente me permito solicitar el archivo '.$nameFile]
                'cuerpo' => [strip_tags($body['header']). strip_tags($body['body']).strip_tags($body['ccp']).strip_tags($body['footer'])]
            ],
            'firmantes' => [
                '_attributes' => [
                    'num_firmantes' => $numFirmantes
                ],
                'firmante' =>
                [
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
                'asunto_docto' => 'Solicitud de Pago',
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
            $dataInsert = DocumentosFirmar::Where('numero_o_clave',$info->clave)->Where('tipo_archivo','Solicitud Pago')->WhereIn('status',['CANCELADO','EnFirma'])->First();
            if(isset($dataInsert->obj_documento)) {
                if($dataInsert->status != 'CANCELADO') {
                    $firmantes = json_decode($dataInsert->obj_documento, true);
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
            // $dataInsert->obj_documento_interno = json_encode($body);
            $dataInsert->status = 'EnFirma';
            // $dataInsert->link_pdf = $urlFile;
            $dataInsert->cadena_original = $response->json()['cadenaOriginal'];
            $dataInsert->tipo_archivo = 'Solicitud Pago';
            $dataInsert->numero_o_clave = $info->clave;
            $dataInsert->nombre_archivo = $nameFileOriginal;
            $dataInsert->documento = $result;
            $dataInsert->documento_interno = $result;
            $dataInsert->num_oficio = $numOficio;
            $dataInsert->body_html = json_encode($body);
            // dd($dataInsert);
            // $dataInsert->md5_file = $md5;
            $dataInsert->save();
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function create_body($id_folio, $numOficio = NULL) {
        $body_html = NULL;
        $data = folio::SELECT('tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.mod','tbl_cursos.inicio','tbl_cursos.tipo_curso','tbl_cursos.instructor_mespecialidad',
                'tbl_cursos.termino','tbl_cursos.modinstructor','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.id AS id_curso','tbl_unidades.ubicacion','tbl_cursos.soportes_instructor','instructores.nombre',
                'instructores.apellidoPaterno','instructores.apellidoMaterno','especialidad_instructores.id', 'tbl_cursos.instructor_mespecialidad as memorandum_validacion',//'especialidad_instructores.memorandum_validacion',
                'instructores.rfc','instructores.id AS id_instructor','instructores.banco','instructores.no_cuenta',
                'instructores.interbancaria','folios.importe_total','folios.id_folios','contratos.unidad_capacitacion',
                'contratos.id_contrato','contratos.numero_contrato','pagos.created_at','pagos.solicitud_fecha','pagos.no_memo','pagos.liquido')
            ->WHERE('folios.id_folios', '=', $id_folio)
            ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
            ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
            ->LEFTJOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
            ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
            ->LEFTJOIN('especialidad_instructores', 'especialidad_instructores.id', '=', 'contratos.instructor_perfilid')
            ->Join('tbl_unidades', 'tbl_unidades.unidad', 'tbl_cursos.unidad')
            ->FIRST();

            $data->soportes_instructor = json_decode($data->soportes_instructor);

        $distintivo= DB::table('tbl_instituto')->pluck('distintivo')->first();
        $funcionarios = $this->funcionarios($data->ubicacion);

        $direccion = DB::Table('tbl_unidades')->WHERE('unidad',$data->ubicacion)->VALUE('direccion');
        $direccion = explode("*", $direccion);

        if($data->solicitud_fecha == NULL)
        {
            $date = strtotime($data->created_at);
            $D = date('d', $date);
            $M = $this->toMonth(date('m',$date));
            $Y = date("Y",$date);
        }
        else
        {
            $date = strtotime($data->solicitud_fecha);
            $D = date('d', $date);
            $M = $this->toMonth(date('m',$date));
            $Y = date("Y",$date);
        }

        if($data->tipo_curso=='CERTIFICACION'){
            $tipo='DE LA CERTIFICACIÓN EXTRAORDINARIA';
        }else{
            $tipo='DEL CURSO';
        }

        // $memoContrato = DB::Table('documentos_firmar')->Where('tipo_archivo','Contrato')
        //     ->Where('status','VALIDADO')
        //     ->Where('numero_o_clave', $data->clave)
        //     ->Value('num_oficio');

        // if(is_null($memoContrato)) {
            $memoContrato = $data->numero_contrato;
        // }

        $body_html['header']  = '  <header>
            <img class="izquierda" src="'. public_path('img/formatos/bannerhorizontal.jpeg'). '">
            <br><h6>'. $distintivo. '</h6>
        </header>';

        $body_html['body'] = '<div align=right>
            <b>Unidad de Capacitación '.$data->unidad_capacitacion.'.</b>
        </div>
        <div align=right>
            <b>Memorandum No. ';
            if(is_null($numOficio)) {
                $body_html['body'] = $body_html['body'] . $data->no_memo;
            } else {
                $body_html['body'] = $body_html['body'] . $numOficio;
            }
            $body_html['body'] = $body_html['body'] . '.</b>
        </div>
        <div align=right>
            <b>'.$data->unidad_capacitacion.', Chiapas '.$D.' de '.$M.' del '.$Y.'.</b>
        </div>
        <b>'.$funcionarios['destino'].'.</b>
        <br>'.$funcionarios['destinop'].'.
        <br>Presente.
        <br><p class="text-justify">En virtud de haber cumplido con los requisitos de apertura <font style="text-transform:lowercase;"> '.$tipo.'</font> y validación de instructor, solicito de la manera más atenta gire sus apreciables instrucciones a fin de que proceda el pago correspondiente, que se detalla a continuación:</p>
        <div align=center>
            <FONT SIZE=2><b>DATOS '.$tipo.'</b></FONT>
        </div>
        <table>
            <tbody>
                <tr>
                    <td><small>Curso: '.$data->curso.'</small></td>
                    <td><small>Clave: '.$data->clave.'</small></td>
                </tr>
                <tr>
                    <td><small>Especialidad: '.$data->espe.'</small></td>
                    <td><small>Modalidad: '.$data->mod.'</small></td>
                </tr>
                <tr>
                    <td><small>Fecha de Inicio y Término: '.$data->inicio.' AL '.$data->termino.'</small></td>
                    <td><small>Horario: '.$data->hini.' A '.$data->hfin.'</small></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div align=center>
            <FONT SIZE=2> <b>DATOS DEL INSTRUCTOR</b></FONT>
        </div>
        <table>
            <tbody>
                <tr>
                    <td><small>Nombre: '.$data->nombre. ' '. $data->apellidoPaterno.' '.$data->apellidoMaterno.'</small></td>
                    <td><small>Número de Contrato: '.$memoContrato.'</small></td>
                </tr>
                <tr>
                    <td><small>Registro STPS: NO APLICA</small></td>
                    <td><small>Memorándum de Validación: '.$data->instructor_mespecialidad.'</small></td>
                </tr>
                <tr>
                    <td><small>RFC: '.$data->rfc.'</small></td>
                    <td><small>Importe: '.$data->liquido.'</small></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div align=center>
            <FONT SIZE=2> <b>DATOS DE LA CUENTA PARA DEPOSITO O TRANSFERENCIA INTERBANCARIA</b></FONT>
        </div>
        <table>
            <tbody>'.($data->modinstructor == 'HONORARIOS' ?
                '<tr>
                <td><small>Banco: '.$data->banco.'</small></td>
                </tr>
                <tr>
                    <td><small>Número de Cuenta: '.$data->no_cuenta.'</small></td>
                </tr>
                <tr>
                    <td><small>Clabe Interbancaria: '.$data->interbancaria.'</small></td>
                </tr>'
            : ($data->banco == NULL ?
                '<tr>
                    <td><small>Banco: NO APLICA</small></td>
                </tr>
                <tr>
                    <td><small>Número de Cuenta: NO APLICA</small></td>
                </tr>
                <tr>
                    <td><small>Clabe Interbancaria: NO APLICA</small></td>
                </tr>'
            :   '<tr>
                    <td><small>Banco: '.$data->soportes_instructor->banco.'</small></td>
                </tr>
                <tr>
                    <td><small>Número de Cuenta: '.$data->soportes_instructor->no_cuenta.'</small></td>
                </tr>
                <tr>
                    <td><small>Clabe Interbancaria: '.$data->soportes_instructor->interbancaria.'</small></td>
                </tr>')) .
            '</tbody>
        </table>
        <p class="text-left"><p><small>Nota: El Expediente Único soporte documental <font style="text-transform:lowercase;">'.$tipo.'</font>, obra en poder de la Unidad de Capacitación.</small></p></p>';

        $body_html['ccp'] = '<p style="font-size: 7px;">
            <b><small>C.c.p. '. $funcionarios['ccp1']. '.- '. $funcionarios['ccp1p']. '.-Para su conocimiento.</small><br/>
            <small>C.c.p. '. $funcionarios['ccp2']. '.- '. $funcionarios['ccp2p']. '.-Mismo fin.</small><br/>
            <small>C.c.p. '. $funcionarios['delegado']. '.- '. $funcionarios['delegadop']. '.-Mismo fin.</small><br/>
            <small>Archivo/ Minutario</small><br/>
            <small>Validó: '. $funcionarios['director']. '.- '. $funcionarios['directorp']. '.</small><br/>
            <small>Elaboró: '. $funcionarios['delegado']. '.- '.$funcionarios['delegadop']. '.</small></b>
        </p>';

        $body_html['footer'] = '<footer>
            <img class="izquierdabot" src="'. public_path('img/formatos/footer_horizontal.jpeg'). '">
            <p class="direccion"><b>';
            foreach($direccion as $point => $ari) {
                if($point != 0) {
                    $body_html['footer'] = $body_html['footer']. '<br>';
                }
                $body_html['footer'] = $body_html['footer']. $ari;
            }
            $body_html['footer'] = $body_html['footer']. '</b></p>
        </footer>';

        return $body_html;
    }

    private function incapacidad($incapacidad, $incapacitado) {

        $fechaActual = now();
        if(!is_null($incapacidad) && !is_null($incapacidad->fecha_inicio)) {
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

    public function funcionarios($unidad) {
        $query = clone $direc = clone $ccp1 = clone $ccp2 = clone $delegado = clone $academico = clone $vinculacion = clone $destino = DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo','f.incapacidad')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Where('f.activo', 'true')
            ->Where('f.titular', true);

        $direc = $direc->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.id_parent',1)
            ->Where('u.unidad', $unidad)
            ->First();

        $destino = $destino->Where('o.id',13)->First();
        $ccp1 = $ccp1->Where('o.id',1)->First();
        $ccp2 = $ccp2->Where('o.id',12)->First();
        $delegado = $delegado->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $unidad)
            ->First();

        $academico = $academico->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.cargo','LIKE','%ACADÉMICO%')
            ->Where('u.unidad', $unidad)
            ->First();

        $vinculacion = $vinculacion->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.cargo','LIKE','%VINCULACIÓN%')
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
            'academico' => $academico->nombre,
            'academicop' => $academico->cargo,
            'elabora' => strtoupper(Auth::user()->name),
            'elaborap' => strtoupper(Auth::user()->puesto)
        ];

        return $funcionarios;
    }

    public function update_body() {
        set_time_limit(0);
        $solpas = DocumentosFirmar::Where('tipo_archivo','Solicitud Pago')->Select('id')
        ->orderBy('id','desc')
        ->Get();
        foreach($solpas as $dcSolpa_id) {
            $solpa = DocumentosFirmar::Where('id', $dcSolpa_id->id)->First();
            $id_folio = DB::Table('tbl_cursos')->Join('folios','folios.id_cursos','tbl_cursos.id')->Where('clave',$solpa->numero_o_clave)->Value('folios.id_folios');
            $body = $this->create_body($id_folio);
            $array_html['header'] = $body['header'];
            $array_html['footer'] = $body['footer'];
            $array_html['body'] = $body['body'];
            $array_html['ccp'] = $body['ccp'];
            $solpa->body_html = $array_html;
            $solpa->save();
        }
        dd('complete');
    }
}
