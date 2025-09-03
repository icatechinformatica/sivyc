<?php 
namespace App\Services;

class ConsultaDatosCURPService {
    public function __construct(){}

    public function consultarDatosPorCurp($curp) {
        // Consumir el microservicio
        $url = 'http://localhost:3001/curp';
        $client = new \GuzzleHttp\Client([
            'timeout' => 60,
        ]);
        try {
            $response = $client->post($url, [
                'json' => [ 'curp' => $curp ]
            ]);
            $body = $response->getBody()->getContents();
            $datos = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $datos;
            } else {
                return [
                    'error' => 'Respuesta JSON inválida',
                    'detalle' => $body
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => 'No se pudo conectar al microservicio CURP',
                'detalle' => $e->getMessage()
            ];
        }
    }
}