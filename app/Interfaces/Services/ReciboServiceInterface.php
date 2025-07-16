<?php

namespace App\Interfaces\Services;

interface ReciboServiceInterface
{
    /**
     * Verifica si existen recibos nulos en la ubicación del usuario
     */
    public function verificarReciboNulo();
}
