<?php

namespace App\Services\Plantillas;

use App\Data\RF001Data;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RF001Service
{
    /**
     * Construye el DTO con todos los datos procesados para la plantilla RF001
     * @param $rfgetData
     * @param $dataunidades
     * @param $dirigido
     * @param $movimiento
     * @param $ccp
     * @param $ccpDelegado
     * @param $instituto
     * @param $cuenta
     * @param $fechaFormateada
     * @param $intervalo
     * @param $importeMemo
     * @param $importeLetra
     * @param $periodoTexto
     * @param $fechaObs
     * @param $ccpHtml
     * @param $ccpValidador
     * @param $elaboroHtml
     * @param $recibos
     * @param $fichas
     * @param $tbodyHTML
     * @param $leyenda
     * @param $direccionHtml
     * @return RF001Data
     */
    public function buildRF001Data(
        $rfgetData,
        $dataunidades,
        $dirigido,
        $movimiento,
        $ccp,
        $ccpDelegado,
        $instituto,
        $cuenta,
        $fechaFormateada,
        $intervalo,
        $importeMemo,
        $importeLetra,
        $periodoTexto,
        $fechaObs,
        $ccpHtml,
        $ccpValidador,
        $elaboroHtml,
        $recibos,
        $fichas,
        $tbodyHTML,
        $leyenda,
        $direccionHtml
    ): RF001Data {
        $dto = new RF001Data();
        $dto->unidad = strtoupper($dataunidades->ubicacion);
        $dto->memo = htmlspecialchars($rfgetData->memorandum);
        $dto->fecha = $fechaFormateada;
        $dto->mun = mb_strtoupper($dataunidades->municipio, 'UTF-8');
        $dto->tit = htmlspecialchars(strtoupper($dirigido->titulo));
        $dto->nom = htmlspecialchars(strtoupper($dirigido->nombre));
        $dto->car = htmlspecialchars($dirigido->cargo);
        $dto->intervalo = $intervalo;
        $dto->importe = number_format($importeMemo, 2, '.', ',');
        $dto->letra = $importeLetra;
        $dto->ccpHtml = $ccpHtml;
        $dto->ccpValidador = $ccpValidador;
        $dto->elaboroHtml = $elaboroHtml;
        $dto->cuentaTexto = htmlspecialchars($cuenta);
        $dto->elaboracion = htmlspecialchars(Carbon::parse($rfgetData->created_at)->format('d/m/Y'));
        $dto->periodoTexto = $periodoTexto;
        $dto->fObservacion = $fechaObs;
        $dto->recibos = $recibos;
        $dto->fichas = $fichas;
        $dto->dinamico = $tbodyHTML;
        $dto->leyenda = $leyenda;
        $dto->direccion = $direccionHtml;
        return $dto;
    }
}
