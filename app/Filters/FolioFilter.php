<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FolioFilter implements FilterInterface
{
    protected $folio;

    public function  __construct($folio)
    {
        $this->folio = $folio;
    }

    public function apply($query)
    {
        return $query->where(DB::raw('CONCAT(tbl_recibos.id,tbl_recibos.folio_recibo,tbl_recibos.folio_grupo)'), 'ILIKE', '%' . $this->folio . '%');
    }
}
