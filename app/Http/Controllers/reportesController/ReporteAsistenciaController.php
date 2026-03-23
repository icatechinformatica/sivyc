<?php

namespace App\Http\Controllers\reportesController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\AsistenciaCrosschexExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAsistenciaController extends Controller
{

    public function filtros()
    {
        $unidades = DB::Table('tbl_organismos')->Select('id','nombre')->Where('id_parent',1)->Where('dif_dpto',1)->Where('nombre', '!=', 'COORDINACIÓN ECE-CONOCER')->orderBy('nombre', 'asc')->get();

        return view('reportes.asistencia_crosschex_filtros', compact('unidades'));
    }

    public function export(Request $request)
    {
        $desde = $request->from;
        $hasta = $request->to;
        $unidadFiltro = $request->unidad_id;
        //para pruebas sin request
        // $desde = '2026-03-01';
        // $hasta = '2026-03-11';
        // $unidadFiltro = 95;


        return Excel::download(new AsistenciaCrosschexExport($desde, $hasta, $unidadFiltro), 'reporte.xlsx');
    }
}
