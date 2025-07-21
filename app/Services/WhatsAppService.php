<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\WspCola;

class WhatsAppService
{
    public function send($telefono, $mensaje)
    {
        $telefono_formateado = '521'.$telefono;
        // funcion para envio instantaneo de mensaje
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

    public function cola($telefono, $mensaje, $prueba) {
        //verificar si es prueba
        if($prueba) {
            $telefono = '9612255159';
        }
        // Validar el número de teléfono
        $telefono_formateado = '521'.$telefono;

        $nuevo_cola = new WspCola();
        $nuevo_cola->telefono = $telefono_formateado;
        $nuevo_cola->mensaje = $mensaje;
        $nuevo_cola->estatus = 'cola';
        $nuevo_cola->sent_at = now();
        $nuevo_cola->id_user_sent = Auth::id();
        $nuevo_cola->save();

        return [
            'numero' => $telefono_formateado,
            'status' => true,
            'respuesta' => 'Mensaje en cola para envío',
        ];
    }
}
