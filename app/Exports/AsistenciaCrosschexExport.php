<?php

namespace App\Exports;

use App\Models\VwCrosschexRecord;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class AsistenciaCrosschexExport implements FromView
{
    protected $from;
    protected $to;
    protected $unidadFiltro;

    public function __construct($from, $to, $unidadFiltro = null)
    {
        $this->from         = Carbon::parse($from)->startOfDay();
        $this->to           = Carbon::parse($to)->endOfDay();
        $this->unidadFiltro = $unidadFiltro;
    }

    public function view(): View
    {
        $query = VwCrosschexRecord::with(['funcionario.unidad'])
            ->whereBetween('check_time_local', [$this->from, $this->to]);

        if ($this->unidadFiltro) {
            $query->where('id_organismo', $this->unidadFiltro);
        }

        // Agrupamos por empleado (workno)
        $recordsByEmployee = $query
            ->get()
            ->groupBy('workno');

        return view('reportes.asistencia_crosschex', [
            'from'             => $this->from,
            'to'               => $this->to,
            'recordsByEmployee'=> $recordsByEmployee,
        ]);
    }
}
