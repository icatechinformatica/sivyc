<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Http\Request;
use App\Models\Tokens_icti;
use Carbon\Carbon;
use App\Utilities\MyUtility;

class EFirmaService
{
    public function __construct()
    {
        # TODO: aqui podríamos inicializar la clase con las variables que cada uno utiliza
    }

    public function generarXml($params)
    {
        switch ($params['TYPE']) {
            case 'value':
                # TODO: METODO REALIZAR AJUSTES PARA NECESIDADES ESPECIFICAS EN EL SISTEMA
                break;

            default:
                # code...
                break;
        }
    }

    protected function getCadenaOriginal(array $param = [])
    {

        switch ($param['STATE']) {
            case 'PRODUCTION':
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
                    'xml_OriginalBase64' => $xmlBase64
                ]);
                return $response;
                break;

            case 'TEST':
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
                    'xml_OriginalBase64' => $xmlBase64
                ]);
                return $response;
                break;

            default:
                # defecto
                break;
        }

    }

    protected function generarToken(array $param = [])
    {
        switch ($param['STATE']) {
            case 'PRODUCTION':
                # TODO: produccion
                $resToken = Http::withHeaders([
                    'Accept' => 'application/json'
                ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
                    'nombre' => 'SISTEM_IVINCAP',
                    'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
                ]);
                break;
            case 'TEST':
                # TODO: prueba
                $resToken = Http::withHeaders([
                    'Accept' => 'application/json'
                ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
                    'nombre' => 'FirmaElectronica',
                    'key' => '19106D6F-E91F-4C20-83F1-1700B9EBD553'
                ]);
                break;
        }

        Tokens_icti::Where('sistema','sivyc')->update([
            'token' => $token
        ]);
        return $token;
    }

    public function sellar(array $param = [])
    {

        switch ($param['STATE']) {
            case 'PRODUCTION':
                $responseStamp = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
                    'xml_Firmado' => $xml
                ]);
                return $responseStamp;
                break;
            case 'TEST':
                $responseStamp = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
                    'xml_Firmado' => $xml
                ]);
                return $responseStamp;
                break;
            default:
                # code...
                break;
        }
    }

    public function setBody(array $param = [])
    {
        switch ($param['TYPE']) {
            case 'RF001':
                // destructurar
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
                ] = $param;

                // Preparar valores con formato
                $unidad      = htmlspecialchars(strtoupper($unidadUbicacion));
                $memo        = htmlspecialchars($memorandum);
                $mun         = htmlspecialchars($municipio);
                $fecha       = htmlspecialchars($fechaFormateada);
                $tit         = htmlspecialchars(strtoupper($titulo));
                $nom         = htmlspecialchars(strtoupper($nombre));
                $importeLetra = $this->letras($importeMemo);
                $car         = htmlspecialchars($cargo);
                $importe     = number_format($importeMemo, 2, '.', ',');
                $intervalo   = $this->formatoIntervaloFecha($periodoInicio, $periodoFin);

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
                    <div class="ccp">C.c.p </div>
                </div>
                HTML;

                return $html;
                break;

            default:
                # code...
                break;
        }
    }

    public static function letras($cantidad, $ver_decimal=true){
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

    protected function formatoIntervaloFecha($fechaIni, $fechaFin)
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
}
