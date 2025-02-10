<?php

namespace App\Repositories;
use App\Interfaces\CredencialesInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Matrix\Module\RoundModule;
use Endroid\QrCode\Matrix\Eye\CircleEye;
use App\Models\Catalogos\Funcionario;

final class CredencialRepository implements CredencialesInterface
{
    public function generarQrCode($id)
    {
            $logoPath = public_path('img/credencial/logo_humanismo_gobierno.png');

            $url = route('perfil', ['id' => $id]);

            if (!file_exists($logoPath)) {
                throw new \Exception("El archivo logo no se encuentra en la ruta: $logoPath");
            }

            $builder = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(5)
            ->logoPath($logoPath)
            ->logoResizeToWidth(45)
            ->logoPunchoutBackground(false)
            ->labelText('Código Generado')
            ->labelFont(new OpenSans(16))
            ->foregroundColor(new Color(211, 194, 180)) // QR en rojo
            ->backgroundColor(new Color(255, 255, 255)); // Ojos en forma de círculo; // Fondo blanco;

            $result = $builder->build();

            // Convertir la imagen a base64
            // $qrCodeBase64 = base64_encode($result->getString());

            return $result;
    }

    public function getFuncionarios()
    {
        return (new Funcionario())->where('status', 'true')->paginate(15 ?? 10);
    }

    public function descargarQr($id)
    {
        $logoPath = public_path('img/credencial/logo_humanismo_gobierno.png');

            if (!file_exists($logoPath)) {
                throw new \Exception("El archivo logo no se encuentra en la ruta: $logoPath");
            }

            $builder = Builder::create()
            ->writer(new PngWriter())
            ->data('https://www.google.com')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(180)
            ->margin(5)
            ->logoPath($logoPath)
            ->logoResizeToWidth(45)
            ->logoPunchoutBackground(false)
            ->foregroundColor(new Color(211, 194, 180)) // QR en rojo
            ->backgroundColor(new Color(255, 255, 255));


        return $builder->build();
    }

    public function getFuncionario($id)
    {
        return (new Funcionario())->findOrFail($id);
    }
}
