<?php

namespace App\Http\Controllers\efirma;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Spatie\ArrayToXml\ArrayToXml;
use App\Models\DocumentosFirmar;
use Illuminate\Http\Request;
use App\Models\Tokens_icti;
use App\Models\tbl_curso;
use PHPQRCode\QRcode;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use PDF;

class AsistenciaController extends Controller
{
    function __construct() {
        $this->mes = ["01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE"];
    }
    ### BY JOSE LUIS / VALIDACIÓN DE INCAPACIDAD
    public function valid_incapacidad($dataFirmante){
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
                return redirect()->route('firma.inicio')->with('danger', 'LA ESTRUCTURA DEL JSON DE LA INCAPACIDAD NO ES VALIDA!');
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
                    ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                    'fun.cargo','fun.correo', 'org.nombre', 'fun.incapacidad')
                    ->join('tbl_funcionarios AS fun', 'fun.id','org.id')
                    ->where('fun.id', $id_firmante)
                    ->first();

                    if ($dataIncapacidad != null) {$result = $dataIncapacidad;}
                    else{return redirect()->route('firma.inicio')->with('danger', 'NO SE ENCONTRON DATOS DE LA PERSONA QUE TOMARÁ EL LUGAR DEL ACADEMICO!');}

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
                            return redirect()->route('firma.inicio')->with('danger', 'Error: ' . $th->getMessage());
                        }

                    }
                }
            }

        }
        return $result;
    }

    public function rechazo(Request $request) {
        $curso = tbl_curso::Where('id', $request->txtIdRechazo)->First();
        $curso->observacion_asistencia_rechazo = $request->motivoRechazo;
        $curso->asis_finalizado = FALSE;
        $curso->save();

        return redirect()->route('firma.inicio')->with('success', 'Documento Rechazado Exitosamente!');
    }

    public function asistencia_pdf($id) {
        $objeto = $dataFirmante = $uuid = $cadena_sello = $fecha_sello = $qrCodeBase64 = $EFolio = $firmantes = null;
        if ($id) {
            // $curso = tbl_cursos::where('clave', '=', $clave)->first();
            $curso = DB::Table('tbl_cursos')->select(
                'tbl_cursos.*',
                DB::raw('right(clave,4) as grupo'),
                'inicio',
                'termino',
                DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
                DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
                'u.plantel',
                )->where('tbl_cursos.id',$id);
            $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
            if ($curso) {
                if ($curso->status_curso == "AUTORIZADO") {
                    $documento = DocumentosFirmar::where('numero_o_clave', $curso->clave)
                        ->WhereNotIn('status',['CANCELADO','CANCELADO ICTI'])
                        ->Where('tipo_archivo','Lista de asistencia')
                        ->first();

                    if(is_null($documento)) {
                        $body = $this->create_body($curso->clave);
                        $body_html = $body['body_html'];
                        $header = $body['header'];
                    } else {
                        $body = json_decode($documento->obj_documento_interno);
                        $body_html = $body->body_html;
                        $header = $body->header;
                        if(isset($body->firmantes)) {
                            $firmantes = $body->firmantes;
                        }
                    }


                    //firmas electronicas
                    $documento = DocumentosFirmar::where('numero_o_clave', $curso->clave)
                        ->Where('tipo_archivo','Lista de asistencia')
                        ->Where('status','VALIDADO')
                        ->first();
                    if(isset($documento->uuid_sellado)){
                        $objeto = json_decode($documento->obj_documento,true);
                        $no_oficio = json_decode(json_encode(simplexml_load_string($documento['documento_interno'], "SimpleXMLElement", LIBXML_NOCDATA),true));
                        // dd($no_oficio);
                        $no_oficio = $no_oficio->{'@attributes'}->no_oficio;
                        $uuid = $documento->uuid_sellado;
                        $cadena_sello = $documento->cadena_sello;
                        $fecha_sello = $documento->fecha_sellado;
                        $folio = $documento->nombre_archivo;
                        $tipo_archivo = $documento->tipo_archivo;
                        $totalFirmantes = $objeto['firmantes']['_attributes']['num_firmantes'];

                        // $dataFirmante = DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre', 'fun.incapacidad')
                        //         ->Join('tbl_funcionarios AS fun','fun.id','org.id')
                        //         ->Where('org.id', Auth::user()->id_organismo)
                        //         ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        //         ->OrWhere('org.id_parent', Auth::user()->id_organismo)
                        //         // ->Where('org.nombre', 'NOT LIKE', 'CENTRO%')
                        //         ->Where('org.nombre', 'LIKE', 'DEPARTAMENTO ACADEMICO%')
                        //         ->First();

                        ###Buscamos al funcionario y el puesto By Jose Luis
                        $puestoUsuario = $objeto['firmantes']['firmante'][0][1]['_attributes']['curp_firmante'];

                        $dataFirmante = DB::Table('tbl_organismos AS org')
                        ->Select('org.id', 'fun.nombre AS funcionario','fun.curp',
                        'fun.cargo','fun.correo', 'org.nombre', 'fun.incapacidad')
                            ->join('tbl_funcionarios AS fun', 'fun.id_org','org.id')
                            ->where('fun.curp', '=', $puestoUsuario)
                            ->first();
                        if($dataFirmante == null){return "No se encontraron datos del servidor publico";}


                        //Generacion de QR
                        //Verifica si existe link de verificiacion, de lo contrario lo crea y lo guarda
                        if(isset($documento->link_verificacion)) {
                            $verificacion = $documento->link_verificacion;
                        } else {
                            // $documento->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumentoPrueba/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                            $documento->link_verificacion = $verificacion = "https://innovacion.chiapas.gob.mx/validacionDocumento/consulta/Certificado3?guid=$uuid&no_folio=$no_oficio";
                            $documento->save();
                        }

                        ob_start();
                        QRcode::png($verificacion);
                        $qrCodeData = ob_get_contents();
                        ob_end_clean();
                        $qrCodeBase64 = base64_encode($qrCodeData);
                        // Fin de Generacion
                    }

                    if(!is_null($documento)){
                        $EFolio = $documento->num_oficio;
                    }

                    $pdf = PDF::loadView('layouts.FirmaElectronica.reporteAsistencia', compact('body_html','header','objeto','dataFirmante','uuid','cadena_sello','fecha_sello','qrCodeBase64','EFolio','firmantes'));
                    $pdf->setPaper('Letter', 'landscape');
                    $file = "ASISTENCIA_$id.PDF";
                    return $pdf->stream($file);

                    // if ($fecha_valida < 0) $message = "No prodece el registro de calificaciones, la fecha de termino del curso es el $curso->termino.";
                } // else $message = "El Curso fué $curso->status y turnado a $curso->turnado.";
            }
        }
    }

    private function create_body($clave, $firmantes = null) {
        $curso = DB::Connection('pgsql')->Table('tbl_cursos')->select(
            'tbl_cursos.*',
            DB::raw('right(clave,4) as grupo'),
            'inicio',
            'termino',
            DB::raw("to_char(inicio, 'DD/MM/YYYY') as fechaini"),
            DB::raw("to_char(termino, 'DD/MM/YYYY') as fechafin"),
            'u.plantel',
            )->where('tbl_cursos.clave',$clave);
        $curso = $curso->leftjoin('tbl_unidades as u','u.unidad','tbl_cursos.unidad')->first();
        $alumnos = DB::Connection('pgsql')->Table('tbl_inscripcion as i')->select(
            'i.id',
            'i.matricula',
            'i.alumno',
            'i.calificacion',
            'f.folio',
            'i.asistencias'
        )->leftJoin('tbl_folios as f', function ($join) {
            $join->on('f.id', '=', 'i.id_folio');
        })->where('i.id_curso', $curso->id)
            ->where('i.status', 'INSCRITO')
            ->orderby('i.alumno')->get();

        foreach ($alumnos as $key => $value) {
            $value->asistencias = json_decode($value->asistencias, true);
        }

        $mes = $this->mes;
        $consec = 1;
        $inicio = explode('-', $curso->inicio); $inicio[2] = '01';
        $termino = explode('-', $curso->termino); $termino[2] = '01';
        $meses = $this->verMeses(array($inicio[0].'-'.$inicio[1].'-'.$inicio[2], $termino[0].'-'.$termino[1].'-'.$termino[2]));

        $array_html['header'] = '<header>
            <img src="img/reportes/sep.png" alt="sep" width="16%" style="position:fixed; left:0; margin: -70px 0 0 20px;" />
            <h6>SUBSECRETARÍA DE EDUCACIÓN E INVESTIGACIÓN TECNOLÓGICAS</h6>
            <h6>DIRECCIÓN GENERAL DE CENTROS DE FORMACIÓN PARA EL TRABAJO</h6>
            <h6>LISTA DE ASISTENCIA</h6>
            <h6>(LAD-04)</h6>
        </header>';
        $array_html['body_html'] = null;
        if (isset($meses)) {
            foreach ($meses as $key => $mes) {
                $consec = 0;
                $array_html['body_html'] = $array_html['body_html']. '<table class="tabla">
                    <thead>
                        <tr>
                            <td ';
                                if (explode('-', $mes['ultimoDia'])[2] == 28) { $array_html['body_html'] = $array_html['body_html']. 'colspan="33"'; }
                                elseif (explode('-', $mes['ultimoDia'])[2] == 29) { $array_html['body_html'] = $array_html['body_html']. 'colspan="34"';}
                                elseif (explode('-', $mes['ultimoDia'])[2] == 30) { $array_html['body_html'] = $array_html['body_html']. 'colspan="35"';}
                                else { $array_html['body_html'] = $array_html['body_html']. 'colspan="36"';}
                                $array_html['body_html'] = $array_html['body_html']. '>
                                <div id="curso">
                                    UNIDAD DE CAPACITACIÓN:
                                    <span class="tab">'. $curso->plantel. ' '. $curso->unidad. '</span>
                                    CLAVE CCT: <span class="tab">'. $curso->cct. '</span>
                                    CICLO ESCOLAR: <span class="tab">'. $curso->ciclo. '</span>
                                    GRUPO: <span class="tab">'. $curso->folio_grupo. '</span>
                                    MES: <span class="tab">'. $mes['mes']. '</span>
                                    AÑO: &nbsp;&nbsp;'. $mes['year']. '
                                    <br />
                                    AREA: <span class="tab1">'. $curso->area. '</span>
                                    ESPECIALIDAD: <span class="tab1">'. $curso->espe. '</span>
                                    CURSO: <span class="tab1">'. $curso->curso. '</span>
                                    CLAVE: &nbsp;&nbsp;'. $curso->clave .'
                                    <br />
                                    FECHA INICIO: <span class="tab1">'. $curso->fechaini. '</span>
                                    FECHA TERMINO: <span class="tab1">'. $curso->fechafin. '</span>
                                    HORARIO: '. $curso->dia. ' DE '. $curso->hini. ' A '. $curso->hfin. '&nbsp;&nbsp;&nbsp;
                                    CURP: &nbsp;&nbsp;'. $curso->curp. ' &nbsp;&nbsp;&nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th ';
                                if (explode('-', $mes['ultimoDia'])[2] == 28) {$array_html['body_html'] = $array_html['body_html']. 'colspan="33"';}
                                elseif (explode('-', $mes['ultimoDia'])[2] == 29){$array_html['body_html'] = $array_html['body_html']. 'colspan="34"';}
                                elseif (explode('-', $mes['ultimoDia'])[2] == 30){$array_html['body_html'] = $array_html['body_html']. 'colspan="35"';}
                                else{ $array_html['body_html'] = $array_html['body_html']. 'colspan="36"';}
                                $array_html['body_html'] = $array_html['body_html']. 'style="border-left: white; border-right: white;">
                            </th>
                        </tr>
                        <tr>
                            <th width="15px" rowspan="2">N<br />U<br />M</th>
                            <th width="100px" rowspan="2">NÚMERO DE <br />CONTROL</th>
                            <th width="280px">NOMBRE DEL ALUMNO</th>';
                            foreach ($mes['dias'] as $keyD => $dia) {
                                $counting = $keyD+1;
                                $array_html['body_html'] = $array_html['body_html']. '<th width="10px" rowspan="2"><b>'. $counting . "</b></th>\n";
                            }
                            $array_html['body_html'] = $array_html['body_html']. '<th colspan="2"><b>TOTAL</b></th>
                        </tr>
                        <tr>
                            <th>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                            <th> A </th>
                            <th> I </th>
                        </tr>
                    </thead>
                    <tbody>';
                        $i = 16;
                        foreach ($alumnos as $a){
                            $tAsis = 0;
                            $tFalta = 0;
                            $consec++;
                            $array_html['body_html'] = $array_html['body_html']. '<tr>
                            <td>'. $consec .'</td>
                            <td>'. $a->matricula. '</td>
                            <td>'. $a->alumno. '</td>';
                            foreach ($mes['dias'] as $dia) {
                                $array_html['body_html'] = $array_html['body_html']. '<td>';
                                if ($a->asistencias != null) {
                                    foreach ($a->asistencias as $asistencia) {
                                        if ($asistencia['fecha'] == $dia && $asistencia['asistencia'] == true) {
                                            $array_html['body_html'] = $array_html['body_html']. '<strong>*</strong>';
                                            $tAsis++;
                                        } elseif($asistencia['fecha'] == $dia && $asistencia['asistencia'] == false) {
                                            $array_html['body_html'] = $array_html['body_html']. 'x';
                                            $tFalta++;
                                        }
                                    }
                                }
                                $array_html['body_html'] = $array_html['body_html']. "</td>";
                            }
                            $array_html['body_html'] = $array_html['body_html']. '<td>'. $tAsis. '</td>
                            <td>'. $tFalta. '</td>
                            </tr>';
                            if($consec > $i && isset($alumnos[$consec]->alumno)) {
                                $array_html['body_html'] = $array_html['body_html']. '</tbody>
                                </table>
                                <br><br><br>
                                <div class="page-break"></div>';
                                $i = $i+15;

                                $array_html['body_html'] = $array_html['body_html']. '<table class="tabla">
                                <thead>
                                    <tr>
                                        <td ';
                                            if (explode('-', $mes['ultimoDia'])[2] == 28) { $array_html['body_html'] = $array_html['body_html']. 'colspan="33"'; }
                                            elseif (explode('-', $mes['ultimoDia'])[2] == 29) { $array_html['body_html'] = $array_html['body_html']. 'colspan="34"';}
                                            elseif (explode('-', $mes['ultimoDia'])[2] == 30) { $array_html['body_html'] = $array_html['body_html']. 'colspan="35"';}
                                            else { $array_html['body_html'] = $array_html['body_html']. 'colspan="36"';}
                                            $array_html['body_html'] = $array_html['body_html']. '>
                                            <div id="curso">
                                                UNIDAD DE CAPACITACIÓN:
                                                <span class="tab">'. $curso->plantel. ' '. $curso->unidad. '</span>
                                                CLAVE CCT: <span class="tab">'. $curso->cct. '</span>
                                                CICLO ESCOLAR: <span class="tab">'. $curso->ciclo. '</span>
                                                GRUPO: <span class="tab">'. $curso->grupo. '</span>
                                                MES: <span class="tab">'. $mes['mes']. '</span>
                                                AÑO: &nbsp;&nbsp;'. $mes['year']. '
                                                <br />
                                                AREA: <span class="tab1">'. $curso->area. '</span>
                                                ESPECIALIDAD: <span class="tab1">'. $curso->espe. '</span>
                                                CURSO: <span class="tab1">'. $curso->curso. '</span>
                                                CLAVE: &nbsp;&nbsp;'. $curso->clave .'
                                                <br />
                                                FECHA INICIO: <span class="tab1">'. $curso->fechaini. '</span>
                                                FECHA TERMINO: <span class="tab1">'. $curso->fechafin. '</span>
                                                HORARIO: '. $curso->dia. ' DE '. $curso->hini. ' A '. $curso->hfin. '&nbsp;&nbsp;&nbsp;
                                                CURP: &nbsp;&nbsp;'. $curso->curp. ' &nbsp;&nbsp;&nbsp;
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th ';
                                            if (explode('-', $mes['ultimoDia'])[2] == 28) {$array_html['body_html'] = $array_html['body_html']. 'colspan="33"';}
                                            elseif (explode('-', $mes['ultimoDia'])[2] == 29){$array_html['body_html'] = $array_html['body_html']. 'colspan="34"';}
                                            elseif (explode('-', $mes['ultimoDia'])[2] == 30){$array_html['body_html'] = $array_html['body_html']. 'colspan="35"';}
                                            else{ $array_html['body_html'] = $array_html['body_html']. 'colspan="36"';}
                                            $array_html['body_html'] = $array_html['body_html']. 'style="border-left: white; border-right: white;">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th width="15px" rowspan="2">N<br />U<br />M</th>
                                        <th width="100px" rowspan="2">NÚMERO DE <br />CONTROL</th>
                                        <th width="280px">NOMBRE DEL ALUMNO</th>';
                                        foreach ($mes['dias'] as $keyD => $dia) {
                                            $counting = $keyD+1;
                                            $array_html['body_html'] = $array_html['body_html']. '<th width="10px" rowspan="2"><b>'. $counting . "</b></th>\n";
                                        }
                                        $array_html['body_html'] = $array_html['body_html']. '<th colspan="2"><b>TOTAL</b></th>
                                    </tr>
                                    <tr>
                                        <th>PRIMER APELLIDO/SEGUNDO APELLIDO/NOMBRE(S)</th>
                                        <th> A </th>
                                        <th> I </th>
                                    </tr>
                                </thead>
                                <tbody>';
                            }
                        }
                        $array_html['body_html'] = $array_html['body_html']. '</tbody>
                    <tfoot>
                    </tfoot>
                </table>
                <br><br><br>';
                if ($key < count($meses) - 1) {
                    $array_html['body_html'] = $array_html['body_html']. '<p style="page-break-before: always;"></p>';
                }
            }
        } else {
             dd('El Curso no tiene registrado la fecha de inicio y de termino' );
        }

        return $array_html;
    }

    function verMeses($a) {
        $f1 = new DateTime($a[0]);
        $f2 = new DateTime($a[1]);

        // obtener la diferencia de fechas
        $d = $f1->diff($f2);
        $difmes =  $d->format('%m');
        $messs = $this->mes;

        $meses = [];
        $temp = [
            'fecha' => $f1->format('Y-m-d'),
            'ultimoDia' => date("Y-m-t", strtotime($f1->format('Y-m-d'))),
            'mes' => $messs[$f1->format('m')],
            'year' => $f1->format('Y'),
            'dias' => $this->getDays($f1->format('Y-m-d'), date("Y-m-t", strtotime($f1->format('Y-m-d'))))
        ];
        array_push($meses, $temp);

        $impf = $f1;
        for ($i = 1; $i <= $difmes; $i++) {
            // despliega los meses
            $impf->add(new DateInterval('P1M'));
            $temp = [
                'fecha' => $impf->format('Y-m-d'),
                'ultimoDia' => date("Y-m-t", strtotime($impf->format('Y-m-d'))),
                'mes' => $messs[$f1->format('m')],
                'year' => $impf->format('Y'),
                'dias' => $this->getDays($impf->format('Y-m-d'), date("Y-m-t", strtotime($impf->format('Y-m-d'))))
            ];
            array_push($meses, $temp);
        }
        return $meses;
    }

    function getDays($dateInicio, $dateFinal) {
        $dias = [];
        for ($i = $dateInicio; $i <= $dateFinal; $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
            array_push($dias, $i);
        }
        return $dias;
    }

    public function generarToken() {
        ## Producción
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

        Tokens_icti::Where('sistema','sivyc')->update([
            'token' => $token
        ]);
        return $token;
    }

    // obtener la cadena original
    public function getCadenaOriginal($xmlBase64, $token) {
        // dd(config('app.cadena'));
        // dd(Config::get('app.cadena', 'default'));

        ##Producción
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
