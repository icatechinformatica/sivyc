<?php
namespace App\Filters;

class StatusFilter implements FilterInterface
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function apply($query)
    {
        switch ($this->status) {
            case "POR COBRAR":  case "PAGADO":
                # code...
                return $query->where('tr.status_recibo', $this->status);
                break;

            default:
                # code...
                return $query->where('tr.status_folio', $this->status);
                break;
        }
    }
}
