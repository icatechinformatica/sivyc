<?php

namespace App\Services;
use PDF;
use App\Models\Reportes\Rf001Model;
use App\Models\Unidad;
use Carbon\Carbon;
use Spatie\ArrayToXml\ArrayToXml;

class ReportService
{
    public function __construct()
    {
        // setear los datos
    }

    public function getReport($distintivo, $organismo, $id, $nombreElaboro, $puestoElaboro)
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $rf001Detalle = new Rf001Model();
        $rf001 = $rf001Detalle::findOrFail($id);
        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $unidad = strtoupper($data->ubicacion);
        $municipio = mb_strtoupper($data->municipio, 'UTF-8');
        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;
        $dirigido = \DB::table('tbl_funcionarios')->where('id', 12)->first();
        $conocimiento = \DB::table('tbl_funcionarios')
            ->leftjoin('tbl_organismos', 'tbl_organismos.id', '=', 'tbl_funcionarios.id_org')
            ->where('tbl_organismos.id', 13)
            ->select('tbl_organismos.nombre', 'tbl_funcionarios.nombre as nombre_funcionario', 'tbl_funcionarios.cargo', 'tbl_funcionarios.titulo')
            ->first();
        $direccion = $data->direccion;

        $delegado = \DB::Table('tbl_organismos AS o')->Select('f.nombre','f.cargo')
            ->Join('tbl_funcionarios AS f', 'f.id_org', 'o.id')
            ->Join('tbl_unidades AS u', 'u.id', 'o.id_unidad')
            ->Where('f.activo', 'true')
            ->Where('o.nombre','LIKE','DELEG%')
            ->Where('u.unidad', $rf001->unidad)
            ->First();
        // return $data;

        return PDF::loadView('reportes.rf001.reporterf001', compact('distintivo', 'organismo', 'data', 'unidad', 'rf001', 'municipio', 'fecha_comp', 'dirigido', 'direccion', 'conocimiento', 'nombreElaboro', 'puestoElaboro', 'delegado'));
    }

    public function xmlFormat($id, $organismo, $unidad, $usuario)
    {
        $rf001 = (new Rf001Model())->findOrFail($id); // obtener RF001 por id

        // dd($rf001);
        // creación del cuerpo
        $body = $this->createBody($id, $rf001);

        $ubicacion = Unidad::where('id', $unidad)->value('ubicacion');

        $dataFirmantes = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
            ->Join('tbl_funcionarios AS fun','fun.id_org','org.id')
            ->Join('tbl_unidades AS u', 'u.id', 'org.id_unidad')
            ->Where('org.id_parent',1)
            ->Where('fun.activo', 'true')
            ->Where('u.unidad', $ubicacion)
            ->First();

        $body = $this->createBody($id, $dataFirmantes);
        $nameFileOriginal = 'contrato '.$rf001->memorandum.'.pdf';
        $numFirmantes = '1';

        //Creacion de array para pasarlo a XML
        $ArrayXml = [
            'emisor' => [
                '_attributes' => [
                    'nombre_emisor' => $usuario->name,
                    'cargo_emisor' => $usuario->puesto,
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
                    $dataFirmantes
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

        return $body;
    }

    protected function createBody($id, $firmante)
    {
        #Distintivo
        $distintivo = \DB::connection('pgsql')->table('tbl_instituto')->value('distintivo');
        $rf001 = (new Rf001Model())->findOrFail($id);

        $data = \DB::table('tbl_unidades')->where('unidad', $rf001->unidad)->first();
        $unidad = strtoupper($data->ubicacion);
        $municipio = mb_strtoupper($data->municipio, 'UTF-8');

        #OBTENEMOS LA FECHA ACTUAL
        $fechaActual = getdate();
        $anio = $fechaActual['year']; $mes = $fechaActual['mon']; $dia = $fechaActual['mday'];
        $dia = ($dia < 10) ? '0'.$dia : $dia;

        $fecha_comp = $dia.' de '.$meses[$mes-1].' del '.$anio;

        $bodyHtml = null;

        #fecha del envio de documento
        $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

        $bodyHtml = $distintivo. "\n".
        "\n REPORTE FOTOGRÁFICO DE INSTRUCTOR EXTERNO\n".
        "\n UNIDAD DE CAPACITACIÓN ".$unidad.
        "\n ".$municipio.", CHIAPAS. A ".$fecha_gen.".\n";

    }

    private function incapacidad($incapacidad, $incapacitado)
    {
        $fechaActual = now();
        if(!is_null($incapacidad->fecha_inicio)) {
            $fechaInicio = \Carbon::parse($incapacidad->fecha_inicio);
            $fechaTermino = \Carbon::parse($incapacidad->fecha_termino)->endOfDay();
            if ($fechaActual->between($fechaInicio, $fechaTermino)) {
                // La fecha de hoy está dentro del rango
                $firmanteIncapacidad = \DB::Table('tbl_organismos AS org')->Select('org.id','fun.nombre AS funcionario','fun.curp','fun.cargo','fun.correo','org.nombre','fun.incapacidad')
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

                    \DB::Table('tbl_funcionarios')->Where('nombre',$incapacitado)
                        ->Update([
                            'incapacidad' => $incapacidad
                    ]);
                }

                return false;
            }
        }
        return false;
    }
}
