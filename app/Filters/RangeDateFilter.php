<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RangeDateFilter implements FilterInterface
{
    protected $fechaIni;
    protected $fechaFin;

    public function __construct($fechaIni, $fechaFin)
    {
        $this->fechaIni = $fechaIni;
        $this->fechaFin = $fechaFin;
    }

    public function apply($query)
    {
        return $query->whereBetween('tbl_recibos.fecha_expedicion', [$this->fechaIni, $this->fechaFin]);
    }
}
