<?php

namespace App\Repositories;
use App\Interfaces\CredencialesInterface;
use PHPQRCode\QRcode;

final class CredencialRepository implements CredencialesInterface
{
    public function generarQrCode()
    {
        ob_start(); // Inicia el almacenamiento en búfer de la salida
        QRcode::png('https://google.com'); // Genera el código QR directamente
        $imageData = ob_get_contents(); // Obtiene los datos generados en el búfer
        ob_end_clean(); // Limpia el búfer de salida

        // Devolver la imagen QR como una respuesta HTTP con el tipo de contenido 'image/png'
        return $imageData;
    }
}
