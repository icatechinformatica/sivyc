<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\RH;

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
        $query = funcionario::RightJoin('tbl_checador_asistencias', 'tbl_checador_asistencias.numero_enlace','tbl_funcionario.clave_empleado')->OrderBy('fecha','desc')->select('tbl_checador_asistencias.*', 'tbl_funcionario.nombre_trabajador','nombre_adscripcion','curp_usuario');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('numero_enlace', 'LIKE', "%$search%")
                ->orWhere('nombre_trabajador', 'LIKE', "%$search%");
        }

        $data = $query->paginate(25);

        if ($request->ajax()) {
            return view('layouts.pages.RH.table_data', compact('data'))->render();
        }

        return view('layouts.pages.RH.index', compact('data'));
    }

    public function descarga_nube() {
        $url = 'https://api.us.crosschexcloud.com/'; // Reemplaza con la URL correcta
        $token = $this->get_api_token($url); // funcion donde se consigue el token para accesar

        $hoy = Carbon::now()->format('Y-m-d');
        $beginTime = $hoy.'T00:00:00+00:00';
        $endTime = $hoy.'T23:59:59+00:00';
        $page = 1;
        $perPage = 100;

        $data = $this->get_records($url,$beginTime, $endTime, $page, $perPage, $token); // funcion donde se consiguen los registros de los checadores de la nube
        $totalPages = $data['pageCount'];

        for ($x = 1; $x <= $totalPages; $x++) {
            foreach($data['list'] as $record) {
                $time = explode('T',$record['checktime']); //se procesa la fecha y hora
                $time[1] = substr($time[1], 0, 8);

                $registro = checador_asistencia::Where('numero_enlace',$record['employee']['workno'])->Where('fecha',$time[0])->First();
                if(is_null($registro)) {
                    $this->add_registro($record['employee']['workno'],$time[0],$time[1]);
                } else {
                    $this->update_registro($registro, $time[1]);
                }
            }
            $page++;
            $data = $this->get_records($url,$beginTime, $endTime, $page, $perPage, $token);
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
        if($entrada < '14:59:59') { // se analiza si el horario es de salida o de entrada
            $new['entrada'] = $entrada;
            if($entrada > '08:14:59' && $entrada < '08:30:00') { // se analiza si existe retraso
                $new['retardo'] = true;
            } else if($entrada > '08:29:59') { // se analiza si es falta
                $new['inasistencia'] = true;
            }
        } else {
            $new['salida'] = $entrada;
        }

        $new->save();

        return $new;
    }

    private function update_registro($registro, $salida) {
        if($salida > '07:30:00' && $salida < '08:30:00') { // se analiza si el horario es de salida o de entrada
            $registro->entrada = $salida;
        } else if(($salida > '15:59:59' && $salida < '16:29:59') || is_null($registro->salida)) { // se analiza si el horario es salida y esta dentro de la hora adecuada
            $registro->salida = $salida;
        }

        $registro->save();

        return $registro;
    }

    private function get_api_token($url) {
        $response = Http::post($url, [
            'header' => [
                'nameSpace' => 'authorize.token',
                'nameAction' => 'token',
                'version' => '1.0',
                'requestId' => (string) Str::uuid(), // Genera un UUID dinámico
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
            'payload' => [
                'api_key' => '060b777c75ccfa52ba7505bdc24ddcf9',
                'api_secret' => '316dc3b75403372e665fe8ad6b4927ee',
            ]
        ]);

        $token = $response->json()['payload']['token'];

        return $token;
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
}
