<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Spatie\ArrayToXml\ArrayToXml;
use Illuminate\Http\Request;
use App\Models\Tokens_icti;
use Carbon\Carbon;

class EFirmaService
{
    public function __construct()
    {
        # TODO: aqui podríamos inicializar la clase con las variables que cada uno utiliza
    }

    public function generarXml($params)
    {
        dd('a');
        switch ($params['TYPE']) {
            case 'value':
                # TODO: METODO REALIZAR AJUSTES PARA NECESIDADES ESPECIFICAS EN EL SISTEMA
                break;

            default:
                # code...
                break;
        }
    }

    protected function createBody(array $params = [])
    {
        // TODO: en este metodo tendría que realizarlo cada quien para ajustarlos a sus necesidades especificas
        switch ($params['TYPE']) {
            case 'Contrato':
                $this->body_contrato($params);
                # TODO: validar y retornar dependiendo del type que se encuentre
                break;

            default:
                # code...
                break;
        }
    }

    protected function getCadenaOriginal()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
            'xml_OriginalBase64' => $xmlBase64
        ]);

        // api prueba
        // $response = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token,
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/Tools/generar_cadena_original', [
        //     'xml_OriginalBase64' => $xmlBase64
        // ]);

        return $response;
    }

    protected function generarToken(array $param = [])
    {
        switch ($param['STATE']) {
            case 'produccion':
                # TODO: produccion
                $resToken = Http::withHeaders([
                    'Accept' => 'application/json'
                ])->post('https://interopera.chiapas.gob.mx/gobid/api/AppAuth/AppTokenAuth', [
                    'nombre' => 'SISTEM_IVINCAP',
                    'key' => 'B8F169E9-C9F6-482A-84D8-F5CB788BC306'
                ]);
                break;
            case 'prueba':
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

    public function sellar()
    {
        //Sellado de producción
        $responseStamp = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$token
        ])->post('https://api.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
            'xml_Firmado' => $xml
        ]);

        // Sellado de prueba
        // $responseStamp = Http::withHeaders([
        //     'Accept' => 'application/json',
        //     'Authorization' => 'Bearer '.$token
        // ])->post('https://apiprueba.firma.chiapas.gob.mx/FEA/v2/NotariaXML/sellarXML', [
        //     'xml_Firmado' => $xml
        // ]);
        return $responseStamp;
    }
}
