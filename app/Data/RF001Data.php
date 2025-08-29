<?php

namespace App\Data;

class RF001Data
{
    public string $unidad;
    public string $memo;
    public string $fecha;
    public string $mun;
    public string $tit;
    public string $nom;
    public string $car;
    public string $intervalo;
    public string $importe;
    public string $letra;
    public string $ccpHtml;
    public string $ccpValidador;
    public string $elaboroHtml;
    public string $cuentaTexto;
    public string $elaboracion;
    public string $periodoTexto;
    public string $fObservacion;
    public string $recibos;
    public string $fichas;
    public string $dinamico;
    public string $leyenda;
    public string $direccion;
    // Puedes agregar más propiedades según necesidades futuras

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
