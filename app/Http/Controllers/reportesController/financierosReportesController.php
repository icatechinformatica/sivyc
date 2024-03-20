<?php

namespace App\Http\Controllers\reportesController;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\ExportExcel;
use App\Models\tbl_unidades;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use PDF;

class financierosReportesController extends Controller
{
    public function index(){
        $unidades = tbl_unidades::SELECT('ubicacion')->WHERE('id', '!=', '0')->ORDERBY('ubicacion','asc')
                                ->GROUPBY('ubicacion')
                                ->GET();

        return view('layouts.pages.reportes.financieros.reporte_cursos', compact('unidades'));
    }

    public function cursos_xls(Request $request) {
        $query = DB::Table('tbl_cursos')->Select('tbl_cursos.clave','tbl_cursos.dura','tbl_cursos.nombre','folios.folio_validacion')
            ->Join('tbl_unidades','tbl_unidades.unidad','tbl_cursos.unidad')
            ->Join('folios','folios.id_cursos','tbl_cursos.id')
            ->Join('pagos','pagos.id_curso','tbl_cursos.id')
            ->Where('tbl_unidades.ubicacion',$request->unidad);
        switch ($request->status) {
            case 'En Espera':
                $query = $query->WhereBetween('pagos.solicitud_fecha', [$request->fecha1, $request->fecha2])->Where('pagos.status_recepcion','En Espera');
            break;
            case 'VALIDADO':
                $query = $query->WhereBetween('pagos.recepcion', [$request->fecha1, $request->fecha2])->Where('pagos.status_recepcion','VALIDADO');
            break;
            case 'PAGADO':
                $query = $query->WhereBetween('pagos.fecha_transferencia', [$request->fecha1, $request->fecha2])->Where('pagos.status_transferencia','PAGADO');
            break;
        }

        $data = $query->Get();

        $head = ['CLAVE','HORAS CURSO','INSTRUCTOR','SUFICIENCIA PRESUPUESTAL'];
        $title = "Cursos Reporte " . $request->status;
        $name = $title."_".date('Ymd').".xlsx";
        $view = 'layouts.pages.reportes.financieros.xls_reporte_cursos';
        return Excel::download(new ExportExcel($data,$head, $title,$view), $name);
    }
}
