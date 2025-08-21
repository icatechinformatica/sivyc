<?php

namespace App\Services\Recibo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReciboService
{
    /**
     * Verifica si existen recibos nulos en la ubicación del usuario
     */
    public function verificarReciboNulo()
    {
        $ubicacion = DB::table('tbl_unidades')
            ->where('id', Auth::user()->unidad)
            ->value('ubicacion');

        return DB::table('tbl_recibos')
            ->whereNull('folio_recibo')
            ->where('unidad', $ubicacion)
            ->exists();
    }
}
