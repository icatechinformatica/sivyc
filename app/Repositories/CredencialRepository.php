<?php

namespace App\Repositories;
use App\Interfaces\CredencialesInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\OpenSans;
use App\Models\Catalogos\Funcionario;

class CredencialRepository implements CredencialesInterface
{
    public function generarQrCode($id)
    {
            $logoPath = public_path('img/credencial/logo_humanismo_gobierno.png');

            $url = route('perfil', ['id' => $id]);

            if (!file_exists($logoPath)) {
                throw new \Exception("El archivo logo no se encuentra en la ruta: $logoPath");
            }

            $qrCode = QrCode::create($url)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setSize(200)
                ->setMargin(5)
                ->setForegroundColor(new Color(211, 194, 180)) // Color del QR
                ->setBackgroundColor(new Color(255, 255, 255)); // Color de fondo

            // Configurar el logo
            $logo = Logo::create($logoPath)
            ->setResizeToWidth(45);

            // Configurar la etiqueta
            $label = Label::create('Código Generado')
            ->setFont(new OpenSans(16))
            ->setTextColor(new Color(0, 0, 0)); // Color del texto de la etiqueta

            // Crear el writer (PNG en este caso)
            $writer = new PngWriter();

            // Generar el resultado
            $result = $writer->write($qrCode, $logo, $label);

            return $result;
    }

    public function getFuncionarios()
    {
        return (new Funcionario())->where('status', 'true');
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
