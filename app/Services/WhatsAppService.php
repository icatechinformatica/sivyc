<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send($telefono_formateado, $mensaje)
    {
        $response = Http::post('https://mensajeria.icatech.gob.mx/send-message', [
            'number' => $telefono_formateado,
            'message' => $mensaje,
        ]);

        return [
            'numero' => $telefono_formateado,
            'status' => $response->successful(),
            'respuesta' => $response->json(),
        ];
    }
}
