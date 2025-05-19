<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\reportesController;

use setasign\Fpdi\PdfParser\StreamReader;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\extensions\FPDIWithRotation;
use Illuminate\Support\Facades\Http;
use App\Models\checador_asistencia;
use Illuminate\Support\Facades\DB;
use App\Exports\ExportExcel;
use Illuminate\Http\Request;
use App\Models\funcionario;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use Redirect,Response;
use Dompdf\Dompdf;
use Carbon\Carbon;
use PDF;
use File;

class RHController extends Controller
{
    public function index(Request $request)
    {
        $query = null;
        $query = funcionario::RightJoin('tbl_checador_asistencias', 'tbl_checador_asistencias.numero_enlace','tbl_funcionario.clave_empleado')
            ->select('tbl_checador_asistencias.*', 'tbl_funcionario.nombre_trabajador','nombre_adscripcion','curp_usuario')
            ->OrderBy('fecha','desc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero_enlace', 'LIKE', "%$search%")
                ->orWhere('nombre_trabajador', 'LIKE', "%$search%");
            });
        }

        if ($request->has('fecha_inicio') && $request->has('fecha_termino')) {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaTermino = $request->input('fecha_termino');

            if (!empty($fechaInicio) && !empty($fechaTermino)) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaTermino]);
            }
        }

        $data = $query->paginate(25);

        if ($request->ajax()) {
            return view('layouts.pages.reportes.RH.table_data', compact('data'))->render();
        }

        return view('layouts.pages.reportes.RH.index', compact('data'));
    }

    public function reporte_quincenal(Request $request)
    {
        $query = null;
        $query = funcionario::Select('nombre_trabajador','nombre_adscripcion','curp_usuario','clave_empleado AS numero_enlace')
            ->OrderBy('numero_enlace','asc');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('clave_empleado', 'LIKE', "%$search%")
                ->orWhere('nombre_trabajador', 'LIKE', "%$search%");
            });
        }

        $data = $query->paginate(25);

        if ($request->ajax()) {
            return view('layouts.pages.reportes.RH.table_reporteQuincenal', compact('data'))->render();
        }

        return view('layouts.pages.reportes.RH.reporteQuincenal', compact('data'));
    }

    public function reporte_quincenal_detalles($numero_enlace, Request $request)
    {
        // dd('a');
        if(is_null($request->mes)) { // checa si es entrada por primera vez a la vista o es pedida de actualizacion de tarjeta de tiempo
            $date = Carbon::now();
            $mes = null;
        } else {
            $numero_enlace = $request->numero_enlace;
            $mes = $request->mes;
            $year = date('Y'); // Get current year
            $date = Carbon::createFromDate($year, $request->mes, $request->quincena); // Create Carbon date for the first day of that month
        }

        $response = $this->get_reporte_quincenal_detalles($numero_enlace, $date, $mes);
        $data = $response['data'];
        $days = $response['days'];
        $range = $response['range'];
        $dates = $response['dates'];
        $dias_inhabiles = $response['dias_inhabiles'];

        if (!is_null($request) && $request->ajax()) {
            return view('layouts.pages.reportes.RH.table_reporteQuincenalDetalles', compact('data','days','range','dates','numero_enlace','dias_inhabiles'))->render();
        }
        return view('layouts.pages.reportes.RH.reporteQuincenalDetalles', compact('data','days','range','dates','numero_enlace','dias_inhabiles'));
    }

    public function reporte_quincenal_pdf(Request $request) {
        // dd($request);
        $numero_enlace = $request->numero_enlace;
        $year = date('Y'); // Get current year
        $date = Carbon::createFromDate($year, $request->mes, $request->quincena);
        $hoy = Carbon::now()->format('d/m/Y');

        $response = $this->get_reporte_quincenal_detalles($numero_enlace, $date, $request->mes);
        $data = $response['data'];
        $days = $response['days'];
        $range = $response['range'];
        $dates = $response['dates'];
        $dias_inhabiles = $response['dias_inhabiles'];

        $direccion = DB::Table('tbl_instituto')->Value('direccion');
        $supervisa = Funcionario::Select('titulo','nombre_trabajador')->Where('curp_usuario',Auth::user()->curp)->First();

        $nombrePDF = 'TarjetaTiempo-'.$range['quincena'].'-'.$range['month'].'-'.$numero_enlace.'.pdf';
        $pdf = PDF::loadView('layouts.pages.reportes.RH.pdf.reporteQuincenalPDF', compact('data','days','range','dates','numero_enlace','direccion','hoy','dias_inhabiles','supervisa'));
            $pdf->setPaper('legal', 'Landscape');
            return $pdf->stream($nombrePDF);
    }

    public function get_reporte_quincenal_detalles($numero_enlace, $date, $mes)
    {
        if(is_null($mes)) { // se analiza si la variable mes fue enviada desde la actualizacion de pantalla
            $previousMonth = $date->copy()->subMonthNoOverflow();
            if ($date->day > 15) { //si date es mayor a 15 entra aqui para poder generar la quincena pasada
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->day(15);
                $quincena = 'primera';
            } else { // aqui entra cuando es menor o igual a 15
                $start = $previousMonth->copy()->day(16);
                $end = $previousMonth->endOfMonth();
                $quincena = 'segunda';
            }
        } else {
            if ($date->day < 15) { //si date es menor a 15 entra aqui para poder generar la primera quincena
                $start = $date->startOfMonth();
                $end = $date->copy()->day(15);
                $quincena = 'primera';
            } else { // aqui entra cuando es mayor a 15
                $start = $date->copy()->day(16);
                $end = $date->endOfMonth();
                $quincena = 'segunda';
            }
        }

        $range = ['start' => $start->format('d/m/Y'), 'end' => $end->format('d-m-Y'), 'month' => mb_strtoupper($start->translatedformat('F')), 'quincena' => $quincena];
        $days = $dates = array();
        for ($i = $start->day; $i <= $end->day; $i++) {
            $day = $start->copy()->translatedFormat('l');
            array_push($dates, $start->copy()->format('d/m/Y')); // lista de fechas de inicio a fin
            array_push($days, $day); // lista de los dias inicio a fin
            $start->addDay();
        }
        $days = array_map('mb_strtoupper', $days);

        $data = funcionario::RightJoin('tbl_checador_asistencias', 'tbl_checador_asistencias.numero_enlace','tbl_funcionario.clave_empleado')
            ->select(DB::raw("to_char(tbl_checador_asistencias.fecha, 'DD/MM/YYYY') as fecha2"),'tbl_checador_asistencias.*', 'tbl_funcionario.nombre_trabajador','nombre_adscripcion','curp_usuario','horario_checador')
            ->Where('clave_empleado', $numero_enlace)
            ->whereBetween('fecha', [$range['start'], $range['end']])
            ->OrderBy('fecha','asc')
            ->Get(); // info del checador

        // analisis de dias inhabiles en el rango
        $dias_inhabiles = DB::Table('dias_inhabiles')->Select(DB::raw("TO_CHAR(fecha, 'DD/MM/YYYY') as fecha"),'numero_memorandum')->whereBetween('fecha', [$range['start'], $range['end']])->Get()->ToArray();
        $response = ['data' => $data, 'days' => $days, 'range' => $range, 'dates' => $dates, 'dias_inhabiles' => $dias_inhabiles];
        return $response;
    }

    public function agregar_justificante(Request $request) {
        if(is_null($request->registro_id)) {
            $registro = new checador_asistencia;
            $registro->numero_enlace = $request->numero_enlace;
            $registro->fecha = $request->registro_fecha;
            $registro->inasistencia = FALSE;
            $registro->justificante = TRUE;
            $registro->observaciones = $request->justificante;
            $registro->save();
        } else {
            DB::Table('tbl_checador_asistencias')->Where('id',$request->registro_id)
                ->update([
                    'justificante' => TRUE,
                    'observaciones' => $request->justificante
                ]);
        }

        return true;
    }

    public function descarga_nube() {
        $apiKey = ['oficinas centrales' => 'c4b5f541364d3f196899b116b0bebb2d',
                   'sancris' => 'd5ccfdba72c0f69c969838e83e5ca9bf',
                   'villaflores' => '8f1a94d4f51b3715100e04b832ccd8eb',
                   'tapachula' => '75fe37dcb06624390152aa92c6ebf6de',
                   'jiquipilas' => '807fadc301799748f30e6d41e0967875',
                   'yajalon' => '8b7615ce0f0e2417a09fbd5782ef8120',
                   'reforma' => 'd79918ba4899a2258daafed113f5e143'];
        $apiSecret = ['oficinas centrales' => 'd7e30cd8e81c98c580c3f5e57c35bd24',
                      'sancris' => '009ea37f8553c1348b51687d2db164df',
                      'villaflores' => '1b68aa6cab9a7fe4c7ed930ec7684a64',
                      'tapachula' => '6eef6221ba2407db4064ceb70a55d50d',
                      'jiquipilas' => 'e3e23a53816132bbffbcf015caa707c4',
                      'yajalon' => 'e39065bafa43f4e366189e83c2adb39d',
                      'reforma' => 'bb7c9be393ad891bbfe2175104ec76c5'];

        foreach($apiKey as $name => $ak) {
            $url = 'https://api.us.crosschexcloud.com/'; // Reemplaza con la URL correcta
            $hoy = Carbon::now()->format('Y-m-d');
            $dia = Carbon::now();
            $diaSemana = $dia->translatedFormat('l');
            // $beginTime = '2025-03-07T00:00:00+00:00';
            // $endTime = '2025-03-07T23:59:59+00:00';
            $beginTime = $hoy.'T00:00:00-06:00';
            $endTime = $hoy.'T23:59:59-06:00';
            $page = 1;
            $perPage = 100;

            $data = $this->get_api($url, $ak, $apiSecret[$name], $beginTime, $endTime, $page, $perPage); // funcion donde se consigue el token para accesar
            $totalPages = $data['pageCount'];

            for ($x = 1; $x <= $totalPages; $x++) {
                foreach($data['list'] as $record) {
                    $time = explode('T',$record['checktime']); //se procesa la fecha y hora
                    $timezone = substr($time[1],8,13); // se extrae la timezone para generarla en -06
                    $time[1] = substr($time[1], 0, 8);

                    if($timezone = '+00:00') { // si la timezone es 0 se le hara una resta a la hora de 6 para tenerla en -06:00
                        $hour = substr($time[1],0,2) - 6;
                        if($hour < 10) {
                            $hour = '0'.$hour;
                        }
                        $time[1] =  $hour . substr($time[1],2,7);
                    }

                    $registro = checador_asistencia::Where('numero_enlace',$record['employee']['workno'])->Where('fecha',$time[0])->First();
                    if(is_null($registro)) {
                        $this->add_registro($record['employee']['workno'],$time[0],$time[1]);
                    } else {
                        $this->update_registro($registro, $time[1], $diaSemana);
                    }
                }
                $page++;
                $data = $this->get_api($url, $ak, $apiSecret[$name], $beginTime, $endTime, $page, $perPage); // consulta de la segunda hoja
            }
        }
        // dd('complete'); // quitar para subir a produccion
    }

    public function upload(Request $request) {
        // dd($request);
        $data2 = null;
        $file = $request->file('file');

        // Leer el contenido del archivo línea por línea
        $data = [];
        if ($file->isReadable()) {
            $filePath = $file->getRealPath();
            $handle = fopen($filePath, "r");

            while (($line = fgets($handle)) !== false) {
                // Almacenar cada línea en el array
                $data[] = trim($line); // Elimina espacios en blanco y saltos de línea
            }
            fclose($handle);

            if($file->getClientOriginalExtension() == 'txt') {
                $this->save_txt_method($data);
            } else {
                $this->save_dat_method($data);
            }

            return redirect()->route('rh.index')
                ->with('success','Asistencias Cargadas Exitosamente');

        } else {
            return back()->withErrors(['file' => 'El archivo no se puede leer.']);
        }
    }

    private function save_txt_method($data) { // metodo para guardado de la informacion en txt
        foreach ($data as $key => $moist) {
            $moist = explode(",", $moist);
            $registro = checador_asistencia::Where('numero_enlace', trim($moist[0]))->Where('fecha',trim($moist[2]))->First();

            if(is_null($registro)) {// sirve para crear
                $this->add_registro_txt(trim($moist[0]),trim($moist[2]),trim($moist[3]),trim($moist[4]),$key);
            } else { // sirve para actualizar
                $this->add_registro_txt(trim($moist[0]),trim($moist[2]),trim($moist[3]),trim($moist[4]),$key, $registro);
            }
        }

        return true;
    }

    private function save_dat_method($data) {
        foreach ($data as $key => $moist) {
            $moist = explode("\t", $moist);
            $moist[1] = explode(' ', $moist[1]);

            $registro = checador_asistencia::Where('numero_enlace',$moist[0])->Where('fecha',$moist[1][0])->First();
            if(is_null($registro)) {
                $this->add_registro($moist[0],$moist[1][0],$moist[1][1]);
            } else {
                $this->update_registro($registro, $moist[1][1]);
            }
        }

        return true;
    }

    private function add_registro_txt($numero_enlace, $fecha, $entrada, $salida, $key, $registro = null) { // registro si el file subido es txt
        if(is_null($registro)) { // si registro viene nulo se inicializa el modelo
            $registro = new checador_asistencia;
        }

        $registro->numero_enlace = $numero_enlace;
        $registro->fecha = $fecha;
        $registro->entrada = $entrada;
        if(!is_null($salida) && $salida != '') {
            $registro->salida = $salida;
        }

        if($entrada > '08:14:59' && $entrada < '08:30:00') { // se analiza si existe retraso
            $registro->retardo = true;
        } else if($entrada > '08:29:59') { // se analiza si es falta
            $registro->inasistencia = true;
        }

        $registro->save();
        return $registro;
    }

    private function add_registro($numero_enlace, $fecha, $entrada) {
        $new = new checador_asistencia([
            'numero_enlace' => $numero_enlace,
            'fecha' => $fecha
        ]);

        $tipo_horario = Funcionario::Where('clave_empleado', $numero_enlace)->Value('horario_checador');
        if($entrada < '14:59:59') { // se analiza si el horario es de salida o de entrada
            $new['entrada'] = $entrada;
            if((!in_array($tipo_horario, ['3','5']) && $entrada > '08:14:59' && $entrada < '08:30:00') || (in_array($tipo_horario, ['3','5']) && $entrada > '09:14:59' && $entrada < '09:30:00')) { // se analiza si existe retraso, despues del || se analizan los que entran a las 9am
                $new['retardo'] = true;
            } else if((!in_array($tipo_horario, ['3','5']) && $entrada > '08:29:59') || (in_array($tipo_horario, ['3','5']) && $entrada > '09:29:59')) { // se analiza si es falta
                $new['inasistencia'] = true;
            }

        } else {
            $new['salida'] = $entrada;
        }

        $new->save();
        return $new;
    }

    private function update_registro($registro, $salida, $dia) {
        $tipo_horario = Funcionario::Where('clave_empleado', $registro->numero_enlace)->Value('horario_checador');
        if($salida > '07:30:00' && $salida < '09:30:00') { // se analiza si el horario es de salida o de entrada
            $registro->entrada = $salida;
        } else if(($salida > '14:59:59') || is_null($registro->salida) || ($dia == 'viernes' && $salida > '12:59:59')) { // se analiza si el horario es salida y esta dentro de la hora adecuada
            if ($dia == 'viernes' && $salida > '13:30:59') {
                $registro->salida = '13:30:00';
            } else if($salida > '16:30:59'){
                $registro->salida = '16:30:00';
            } else {
                $registro->salida = $salida;
            }
        }

        if(is_null($registro->entrada) || ((!in_array($tipo_horario, ['3','5']) && $registro->entrada > '08:29:59') || (in_array($tipo_horario, ['3','5']) && $registro->entrada > '09:29:59'))  || is_null($registro->salida)) {
            $registro->inasistencia = TRUE;
        } else {
            $registro->inasistencia = FALSE;
        }

        $registro->save();

        return $registro;
    }

    private function get_api($url, $apiKey, $apiSecret, $beginTime, $endTime, $page, $perPage) {
        $response = Http::post($url, [
            'header' => [
                'nameSpace' => 'authorize.token',
                'nameAction' => 'token',
                'version' => '1.0',
                'requestId' => (string) Str::uuid(), // Genera un UUID dinámico
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
            'payload' => [
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ]
        ]);

        $token = $response->json()['payload']['token'];
        $data = $this->get_records($url,$beginTime, $endTime, $page, $perPage, $token); // funcion donde se consiguen los registros de los checadores de la nube

        return $data;
    }
    private function get_records($url,$beginTime, $endTime, $page, $perPage, $token) {
        $response2 = Http::post($url, [
            'header' => [
                'nameSpace' => 'attendance.record',
                'nameAction' => 'getrecord',
                'version' => '1.0',
                'requestId' => (string) Str::uuid(),
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
            'authorize' => [
                'type' => 'token',
                'token' => $token,
            ],
            'payload' => [
                'begin_time' => $beginTime,
                'end_time' => $endTime,
                'order' => 'asc',
                'page' => $page,
                'per_page' => $perPage,
            ],
        ]);

        return $response2->json()['payload'];
    }

    public function api_rh(Request $request)
    {
        // Capturar parámetros desde la URL
        $url = $request->query('url');
        $apiKey = $request->query('key');
        $apiSecret = $request->query('secret');
        $beginTime = $request->query('inicio');
        $endTime = $request->query('final');
        $page = $request->query('pagina', 1);
        $perPage = $request->query('porpagina', 10);

        // Llamar a un método privado para procesar la información
        $response = $this->get_api($url, $apiKey, $apiSecret, $beginTime, $endTime, $page, $perPage);

        return response()->json([$response]);
    }
}
