<?php
namespace App\Services;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Factories\ElectronicDocumentFactory;
use PDF;
use Illuminate\Support\Facades\DB;
use App\Utilities\MyUtility;

class DocumentoService
{
    private $factory;
    public function __construct(ElectronicDocumentFactory $factory)
    {
        $this->factory = $factory;
    }

    public function generarDocumento(array $parameters = [])
    {
        switch ($parameters['TYPE']) {
            case 'RF001':
                # REPORTE CONCENTRADO INGRESOS PROPIOS
                # DESTRUCTURAR
                [
                    'unidadUbicacion'   => $unidadUbicacion,
                    'memorandum'        => $memorandum,
                    'municipio'         => $municipio,
                    'fechaFormateada'   => $fechaFormateada,
                    'titulo'            => $titulo,
                    'nombre'            => $nombre,
                    'cargo'             => $cargo,
                    'importeMemo'       => $importeMemo,
                    'periodo_inicio'    => $periodoInicio,
                    'periodo_fin'       => $periodoFin,
                    'id_unidad'         => $idUnidad,
                    'movimientos'       => $movimientos,
                    'creado'            => $creado
                ] = $parameters;
                # preparar valores con formato
                $valores = [
                    'unidad'    => ['value' => $unidadUbicacion, 'upper' => true],
                    'memo'      => ['value' => $memorandum],
                    'mun'       => ['value' => $municipio],
                    'fecha'     => ['value' => $fechaFormateada],
                    'tit'       => ['value' => $titulo, 'upper' => true],
                    'nom'       => ['value' => $nombre, 'upper' => true],
                    'car'       => ['value' => $cargo],
                    'importeLetra' => ['value' => $this->letras($importeMemo)],
                    'importe' => ['value' => number_format($importeMemo, 2, '.', ',')],
                    'intervalo' => ['value' => $this->formatoIntervaloFecha($periodoInicio, $periodoFin)],
                    'idUnidad' => ['value' => $idUnidad]
                ];

                $count = 0;
                $ccpHtml = ''; // Aquí se guarda el contenido generado en el foreach
                $validadores = ['DIRECTOR', 'DIRECTORA', 'ENCARGADO DE LA UNIDAD', 'ENCARGADA DE LA UNIDAD'];
                $ccpValidador = '';
                $bandera = false;
                $elaboroHtml = '';
                $instituto = \DB::table('tbl_instituto')->first();
                // Decodificar el campo cuentas_bancarias
                $cuentas_bancarias = json_decode($instituto->cuentas_bancarias, true); // true convierte el JSON en un array asociativo
                $cuenta = $cuentas_bancarias[$unidadUbicacion]['BBVA'];

                foreach ($valores as $key => $info) {
                    $val = $info['value'];
                    if (!empty($info['upper'])) {
                        $val = strtoupper($val);
                    }
                    $$key = htmlspecialchars($val);
                }

                $ccp = $this->setCpp($idUnidad);

                foreach ($ccp as $key => $value) {
                    if ($count === 0) {
                        $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Para su conocimiento. <br>';
                    } elseif (
                        !str_contains($value->cargo, 'DIRECTOR') &&
                        !str_contains($value->cargo, 'DIRECTORA') &&
                        !str_contains($value->cargo, 'ENCARGADO DE LA UNIDAD') &&
                        !str_contains($value->cargo, 'ENCARGADA DE LA UNIDAD')
                    ) {
                        if ($key == 1) {
                            $ccpHtml .= 'Archivo / Minutario. <br>';
                        }
                        $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Mismo fin. <br>';
                    }
                    $count++;
                }


                foreach ($ccp as $v) {
                    foreach ($validadores as $validador) {
                        if (str_contains($v->cargo, $validador)) {
                            $ccpValidador .= 'Validó: ' . htmlspecialchars($v->nombre) . '. ' . htmlspecialchars($v->cargo) . '. <br>';
                            break;
                        }
                    }
                }

                $ccpDelegado = $this->setFuncionarios($idUnidad);

                foreach ($ccpDelegado as $ke => $val) {
                    if (!$bandera) {
                        if (str_contains($val->cargo, 'DELEGADO') || str_contains($val->cargo, 'DELEGADA')) {
                            $elaboroHtml .= 'Elaboró: '.htmlspecialchars($val->nombre).'. '.htmlspecialchars($val->cargo).'. <br>';
                            $bandera = true;
                        } elseif (
                            str_contains($val->cargo, 'DIRECTOR') ||
                            str_contains($val->cargo, 'DIRECTORA') ||
                            str_contains($val->cargo, 'ENCARGADO DE LA UNIDAD') ||
                            str_contains($val->cargo, 'ENCARGADA DE LA UNIDAD')
                        ) {
                            $elaboroHtml .= 'Elaboró: '.htmlspecialchars($val->nombre).'. '.htmlspecialchars($val->cargo).'. <br>';
                            $bandera = true;
                        }
                    }
                }


                $html = <<<HTML
                            <div class="contenedor">
                                <div class="bloque_dos" align="right" style="font-family: Arial, sans-serif; font-size: 14px;">
                                    <p class="delet_space_p color_text"><b>UNIDAD DE CAPACITACIÓN {$unidad}</b></p>
                                    <p class="delet_space_p color_text">MEMORÁNDUM No. {$memo}</p>
                                    <p class="delet_space_p color_text">{$mun}, CHIAPAS; <span class="color_text">{$fecha}</span></p>
                                </div>
                                <br>
                                <div class="bloque_dos" align="left" style="font-family: Arial, sans-serif; font-size: 14px;">
                                    <p class="delet_space_p color_text"><b>{$tit} {$nom}</b></p>
                                    <p class="delet_space_p color_text"><b>{$car}</b></p>
                                    <p class="delet_space_p color_text"><b>PRESENTE.</b></p>
                                </div>
                                <div class="contenido" style="font-family: Arial, sans-serif; font-size: 14px; margin-top: 25px" align="justify">
                                    Por medio del presente, me permito enviar a usted el Concentrado de Ingresos Propios (FORMA RF-001) de la Unidad de Capacitación
                                    <span class="color_text"> {$unidad}, </span> correspondiente a la semana comprendida {$intervalo}.
                                    El informe refleja un total de \${$importe} ({$importeLetra}), mismo que se adjunta para su conocimiento y trámite correspondiente.
                                </div>
                                <br>
                                <div class="tabla_alumnos">
                                    <p style="font-family: Arial, sans-serif; font-size: 14px;">Sin otro particular, aprovecho la ocasión para saludarlo.</p>
                                </div>
                                <br><br>
                                <div class="ccp" style="font-size: 9px;">
                                    C.c.p <br>
                                    {$ccpHtml}
                                    <br>
                                    {$ccpValidador}
                                    <br>
                                    {$elaboroHtml}
                                </div>
                            </div>
                        HTML;

                # GENERAR FORMATO RF001

                $fechaElaboracion = htmlspecialchars(Carbon::parse($creado)->format('d/m/Y'));

                $fechaInicio = new \DateTime($periodoInicio);
                $fechaFin = new \DateTime($periodoFin);
                $dateCreacion = \Carbon\Carbon::parse($creado);
                $dateCreacion->locale('es'); // Configurar el idioma a español
                $nombreMesCreacion = $dateCreacion->translatedFormat('F');

                $periodoTexto = htmlspecialchars($fechaInicio->format('d/m/Y')) . ' AL ' . htmlspecialchars($fechaFin->format('d/m/Y'));
                $nombreUnidad = htmlspecialchars(strtoupper($unidadUbicacion));
                $cuentaTexto = htmlspecialchars($cuenta);

                // Ordenar movimientos por número en el folio
                usort($movimientos, function ($a, $b) {
                    preg_match('/\d+/', $a['folio'], $matchA);
                    preg_match('/\d+/', $b['folio'], $matchB);
                    return ((int)($matchA[0] ?? 0)) <=> ((int)($matchB[0] ?? 0));
                });

                $html .= <<<HTML
                        <div class="contenedor">
                            <div style="text-align: center; font-size: 10px;">
                                <p>
                                    FORMA RF-001<br>
                                    INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS<br>
                                    UNIDAD DE CAPACITACIÓN {$unidad}<br>
                                    CONCENTRADO DE INGRESOS PROPIOS
                                </p>
                            </div>
                            <table class="tabla_con_border" style="padding-top: 9px;">
                                <tr>
                                    <td width="200px">FECHA DE ELABORACIÓN</td>
                                    <td width="750px" colspan="8" style="border-left-style: dotted;"></td>
                                    <td width="200px" style="text-align:center;">SEMANA</td>
                                    <td colspan="13" style="border: inset 0pt;"></td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">$fechaElaboracion</td>
                                    <td colspan="8" style="border-left-style: dotted;"></td>
                                    <td style="text-align:center;">$periodoTexto</td>
                                    <td colspan="13" style="border: inset 0pt;"></td>
                                </tr>
                            </table>
                            <center class="espaciado"></center>
                            <table class="tabla_con_border">
                                <tr><td style="text-align: center;"><b>DEPÓSITO(S) EFECTUADO(S) A LA CUENTA BANCARIA:</b></td></tr>
                                <tr><td style="text-align: center;">NO. CUENTA $cuentaTexto</td></tr>
                            </table>
                            <table class="tabla_con_border" style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; width: 20%; word-wrap: break-word;"><b>N°. RECIBO Y/O FACTURA</b></th>
                                        <th style="text-align: center; width: 20%; word-wrap: break-word;"><b>MOVTO BANCARIO Y/O <br> NÚMERO DE FOLIO</b></th>
                                        <th style="text-align: center; width: 45%; word-wrap: break-word;">CONCEPTO DE COBRO</th>
                                        <th style="text-align: center; width: 15%; word-wrap: break-word;">IMPORTE</th>
                                    </tr>
                                </thead>
                                <tbody>
                        HTML;

                $importeTotal = 0;
                $counter = 0;

                foreach ($movimientos as $item) {
                    $depositos = json_decode($item['depositos'] ?? '[]', true);
                    $foliosDeposito = [];

                    foreach ($depositos as $k) {
                        $counter++;
                        $foliosDeposito[] = $k['folio'];
                    }

                    // Agrupar por cada 3 con salto de línea
                    $foliosAgrupados = array_chunk($foliosDeposito, 3);
                    $foliosHTML = implode('<br>', array_map(fn($chunk) => implode(', ', $chunk), $foliosAgrupados));

                    $conceptoTexto = ($item['concepto'] === 'CURSO DE CAPACITACIÓN O CERTIFICACIÓN')
                        ? htmlspecialchars($item['curso'])
                        : htmlspecialchars($item['concepto']);

                    $importeTotal += $item['importe'];
                    $importeTexto = number_format($item['importe'], 2, '.', ',');

                    $html .= <<<HTML
                                                    <tr>
                                                        <td style="text-align: center;">{$item['folio']}</td>
                                                        <td style="text-align: center;">$foliosHTML</td>
                                                        <td style="text-align: left; font-size: 9px;">$conceptoTexto</td>
                                                        <td style="text-align: center;">$ $importeTexto</td>
                                                    </tr>
                                                HTML;
                }

                $totalTexto = number_format($importeTotal, 2, '.', ',');

                $html .= <<<HTML
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align:right;"><b>TOTAL</b></td>
                                                        <td style="text-align:center;"><b>$ $totalTexto</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <center class="espaciado"></center>
                                            HTML;

                // Observaciones
                $recibos = implode(', ', array_map(fn($m) => htmlspecialchars($m['folio']), $movimientos));

                $foliosDepositos = [];
                foreach ($movimientos as $v) {
                    $depositos = json_decode($v['depositos'] ?? '[]', true);
                    foreach ($depositos as $j) {
                        $foliosDepositos[] = htmlspecialchars($j['folio']);
                    }
                }
                $fichas = implode(', ', $foliosDepositos);
                $fechaObs = $dateCreacion->day . "/" . Str::upper($nombreMesCreacion) . "/" . $dateCreacion->year;

                $html .= <<<HTML
                                            <table class="tabla_con_border">
                                                <tr>
                                                    <td colspan="3">OBSERVACIONES:</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" style="vertical-align: top;">
                                                        <b>SE ENVIAN RECIBO OFICIAL:</b> $recibos
                                                        <p><b>FICHAS DE DEPOSITO:&nbsp;</b>$fichas&nbsp; <b>$fechaObs</b></p>
                                                    </td>
                                                </tr>
                                                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                            </table>
                                            <p style="font-size: 8px;">
                                                DECLARO BAJO PROTESTA DE DECIR VERDAD, QUE LOS DATOS CONTENIDOS EN ESTE CONCENTRADO SON VERÍDICOS
                                                Y MANIFIESTO TENER CONOCIMIENTO DE LAS SANCIONES QUE SE APLICARÁN EN CASO CONTRARIO
                                            </p>
                                        </div>
                                    HTML;

                return $html;
                break;
            case 'REPORTE_FOTOGRAFICO':
                # TODO:
                break;
            default:
                # code...
                break;
        }
    }

    public function setCpp($idUnidad)
    {
        $query = \DB::table('tbl_funcionarios as funcionario')
        ->join('tbl_organismos as organismos', 'funcionario.id_org', '=', 'organismos.id')
        ->select('funcionario.nombre', 'funcionario.id_org', 'organismos.id_parent', 'funcionario.cargo')
        ->where('funcionario.activo', 'true')
        ->where('funcionario.titular', true)
        ->where(function ($query) use ($idUnidad) {
            $query->where(function ($sub) use ($idUnidad) {
                $sub->where('organismos.id_unidad', $idUnidad)
                    ->where(function ($q) {
                        $q->where('funcionario.cargo', 'like', 'DELEG%')
                        ->orWhere('organismos.id_parent', 1);
                    });
            })
            ->orWhere('organismos.id_parent', 0)
            ->orWhere('funcionario.id_org', 13);
        })
        ->where(function ($query) {
            $query->whereNull('funcionario.incapacidad')
                ->orWhere('funcionario.incapacidad', '{}')
                ->orWhereNull(\DB::raw("funcionario.incapacidad->>'id_firmante'"));
        })
        ->orderBy('funcionario.id_org', 'asc')
        ->get();

        return $query;
    }

    public function setFuncionarios($idUnidad)
    {
        return \DB::table('tbl_funcionarios AS funcionario')
                ->join('tbl_organismos AS organismos', 'funcionario.id_org', '=', 'organismos.id')
                ->select('funcionario.nombre', 'funcionario.id_org', 'organismos.id_parent', 'funcionario.cargo')
                ->where([
                    ['funcionario.activo', 'true'],
                    ['funcionario.titular', true],
                    ['organismos.id_unidad', $idUnidad],
                ])
                ->where(function ($query) {
                    $query->where('funcionario.cargo', 'like', 'DELEG%')
                        ->orWhere('organismos.id_parent', 1);
                })
                ->where(function ($query) {
                    $query->whereNull('funcionario.incapacidad')
                        ->orWhere('funcionario.incapacidad', '{}')
                        ->orWhereRaw("funcionario.incapacidad->>'id_firmante' IS NULL");
                })
                ->orderByDesc('funcionario.id_org')
                ->get();

    }

    public function getPlantilla(int $id, string $modelo, array $params, string $directiva)
    {
        $repositorio = $this->factory->make($modelo);
        return $repositorio->obtenerPlantilla($id, $params, $directiva);

    }

    public function obtenermultiplesCondiciones(string $modelo, array $params)
    {
        $repositorio = $this->factory->make($modelo);
        return $repositorio->consultaMultiple($params);
    }

    public function obtenerPlantillas(string $modelo)
    {
        // obtención de las plantillas TODAS
        return $this->ElectronicDocument->obtenerTodosLosDatos();
    }

    public function procesarPlantilla($contenido, array $variables)
    {
        foreach ($variables as $key => $value) {
            if (is_array($value) || is_object($value)) {
                // Si no puedes procesarlo como string, usa json_encode o maneja según el caso
                $value = json_encode($value);
            } elseif (is_bool($value)) {
                $value = $value ? 'Sí' : 'No';
            } elseif (is_null($value)) {
                $value = '';
            }

            $contenido = str_replace("@$key", (string) $value, $contenido);
        }

        return $contenido;
    }


    public function formatoFechaCrearMemo($fecha)
    {
        //parsear la fecha utilizando Carbon
        $parserDate = Carbon::parse($fecha);
        // configurar al idioma español
        $parserDate->locale('es');

        $formattedDate = $parserDate->translatedFormat('d'). ' DE '. mb_strtoupper($parserDate->translatedFormat('F'), 'UTF-8'). ' DEL '. $parserDate->translatedFormat('Y');
        return $formattedDate;
    }

    public function formatoIntervaloFecha($fechaIni, $fechaFin)
    {
        // Parsear las fechas usando Carbon
        $dateInit = Carbon::parse($fechaIni);
        $dateEnd = Carbon::parse($fechaFin);

        // Configurar el idioma a español
        $dateInit->locale('es');
        $dateEnd->locale('es');

        // Comparar si los meses de las fechas de inicio y fin son iguales
        if($dateInit->translatedFormat('F') === $dateEnd->translatedFormat('F')) {
            // Mismo mes, formato: "del 7 al 11 de octubre del 2024"
            $formattedDates = 'del ' . $dateInit->translatedFormat('j') . ' al ' . $dateEnd->translatedFormat('j') . ' de ' . $dateEnd->translatedFormat('F') . ' del ' . $dateEnd->translatedFormat('Y');
        } else {
            // Meses diferentes, formato: "del 30 de septiembre al 4 de octubre del 2024"
            $formattedDates = 'del ' . $dateInit->translatedFormat('j') . ' de ' . $dateInit->translatedFormat('F') . ' al ' . $dateEnd->translatedFormat('j') . ' de ' . $dateEnd->translatedFormat('F') . ' del ' . $dateEnd->translatedFormat('Y');
        }

        // Imprimir el resultado
        return $formattedDates;
    }

    public function letras($cantidad, $ver_decimal=true){
        $unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
        $centenas = ["cien", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
        $especiales = ["diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete","dieciocho", "diecinueve"];

        $entero = floor($cantidad);//dd($entero);
        $decimal = round(($cantidad - $entero) * 100);
        $pesos = ($entero == 1) ? "peso" : "pesos";
        $centavos = ($decimal == 1) ? "centavo" : "centavos";
        $parteEntera = "";
        $parteDecimal = "";

        if ($entero >= 1 && $entero <= 999999999) {
            $millones = floor($entero / 1000000);
            $millar = floor(($entero % 1000000) / 1000); //dd($millar);
            $centena =  floor(($entero % 1000) / 100); //dd($centena);
            $decena = floor(($entero % 100) / 10); //dd($decena);
            $unidad = $entero % 10; //dd($unidad);
            //dd($millar);
            if ($millones > 0){
                $parteEntera .= MyUtility::letras($millones, false);
                if($millones>1) $parteEntera .= " millones ";
                else $parteEntera .= " millon ";
            }

            if ($millar > 0) {
                if ($millar == 1) $parteEntera .= " un";
                elseif ($millar >= 2 && $millar <= 9) $parteEntera .= $unidades[$millar];
                elseif ($millar >= 10 && $millar <= 19) $parteEntera .= $especiales[$millar-10];
                else $parteEntera .= MyUtility::letras($millar, false);

                $parteEntera .= " mil ";
            }

            if ($centena > 0){
                if($centena==1 and $decena==0) $parteEntera .=  $centenas[0] . " ";
                else $parteEntera .= $centenas[$centena] . " ";
            }
            if ($decena > 0){
                $parteEntera .= $decenas[$decena] . " ";
            }
            if ($unidad > 0) {
                $d = floor($decena / 1);
                $u = $unidad % 10; //dd($d);
                if ($unidad == 1){
                    if($d>0) $parteEntera .= " y un ";
                    else $parteEntera .= " un ";
                }
                if ($unidad >= 2 && $unidad <= 9){
                    if ($d > 0) $parteEntera .= " y ".$unidades[$u] ;
                    else $parteEntera .= $unidades[$u];
                }
            }
            $parteEntera .= " ";
        } else $parteEntera = "No soportado";
      //  dd($parteEntera);
        if ($decimal > 0) {
            if ($decimal >= 10 && $decimal <= 15) {
                $parteDecimal .= $especiales[$decimal - 10];
            } else {
                $d = floor($decimal / 10);
                $u = $decimal % 10;
                if ($d > 0) $parteDecimal .= $decenas[$d] . " y ";
                if ($u > 0) $parteDecimal .= $unidades[$u];
            }
            $parteDecimal = " $decimal/100 MN ";
        }else $parteDecimal = " 00/100 MN ";

        if(!$ver_decimal) $parteDecimal="";
        else $parteDecimal = " $pesos" . $parteDecimal;

        return strtoupper(trim($parteEntera) . $parteDecimal );
    }

    public function generarPdfDocument($contenido, $orientacion = 'Landscape')
    {
        // generar el PDF
        $pdf = PDF::loadview('formatoPdf.PlantillaRf', $contenido)
            ->setPaper('A4', $orientacion); //Configura el tamaño de papel y la orientación

        $dompdf = $pdf->getDomPDF();
        $options = $dompdf->getOptions();
        $options->setIsHtml5ParserEnabled(true);  // Habilita el parser HTML5
        $options->setIsRemoteEnabled(true);       // Permitir imágenes remotas
        $dompdf->setOptions($options)->output();
        // reenderizar PDF
        return $pdf;
    }

    public function consultaDinamica(array $params)
    {
        // usar DB o Eloquent Model
        $query = isset($params['model']) && class_exists($params['model'])
            ? app($params['model'])::query()
            : DB::table($params['table']);

        // agregar los joins
         if (!empty($params['joins']) && is_array($params['joins'])) {
            foreach ($params['joins'] as $join) {
                $query->join(
                    $join['table'],
                    $join['first'],
                    $join['operator'] ?? '=',
                    $join['second'],
                    $join['type'] ?? 'inner'
                );
            }
        }

        // campos a seleccionar
        if (!empty($params['select'])) {
            $query->select($params['select']);
        }

        // condiciones del where

        if (!empty($params['where']) && is_array($params['where'])) {
            foreach ($params['where'] as $condition) {
                $query->where(
                    $condition['column'],
                    $condition['operator'] ?? '=',
                    $condition['value']
                );
            }
        }

        // obtener first o get
        return $params['first'] ?? true ? $query->first() : $query->get();
    }

    public function paraMes(int $mes)
    {
        switch ($mes) {
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

    public function formatoNumero(string $numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }
}
