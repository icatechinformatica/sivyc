<?php
namespace App\Repositories;

use App\Interfaces\Reporterf001Interface;
use App\Utilities\MyUtility;
use App\Models\Unidad;
use Illuminate\Support\Facades\Auth;
use App\Models\Reportes\Recibo;
use Carbon\Carbon;
use App\Models\Reportes\Rf001Model;

class Reporterf001Repository implements Reporterf001Interface
{
    public function index($user): Array
    {
        $ubicacion = Unidad::where('id', $user->unidad)->value('ubicacion');
        $unidades = Unidad::where('ubicacion',$ubicacion)->orderby('unidad')->pluck('unidad','unidad');
        $anios = MyUtility::ejercicios();
        return $data = [
            'anios' => $anios,
            'unidades' => $unidades,
        ];
    }

    public function getReciboQry($unidad)
    {
        // $recibo = Recibo::where('id_concepto', '>', 1)
        $recibo = Recibo::where('tbl_recibos.status_recibo', 'PAGADO')
            ->where('tbl_unidades.unidad', $unidad)
            ->with('concepto:id,concepto')
            ->select('tbl_recibos.*', 'cat_conceptos.concepto', 'tbl_recibos.id as id_recibo', 'tbl_unidades.clave_contrato')
            ->addSelect(\DB::raw("
                    CASE
                        WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                        WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                    END as file_pdf"))
            ->join('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->join('tbl_unidades', 'tbl_unidades.unidad', '=', 'tbl_recibos.unidad')
            ->leftJoin('tbl_cursos','tbl_cursos.folio_grupo','=', 'tbl_recibos.folio_grupo');
        return $recibo;
    }

    public function generateRF001Format($request)
    {
        $seleccionados = $request->input('seleccionados', []);
        $dataAdd = [];

        foreach ($seleccionados as $seleccionado) {
            list($contrato, $numRecibo, $id) = explode("_", $seleccionado);
            # code...
            $query = \DB::table('tbl_recibos')
            ->leftjoin('tbl_cursos', 'tbl_recibos.id_curso', '=', 'tbl_cursos.id')
            ->leftjoin('cat_conceptos', 'cat_conceptos.id', '=', 'tbl_recibos.id_concepto')
            ->select('tbl_recibos.folio_recibo', 'tbl_recibos.unidad as unidad', 'tbl_recibos.importe', 'tbl_recibos.status_folio', 'tbl_recibos.status_recibo', 'cat_conceptos.concepto', 'tbl_recibos.depositos', 'tbl_recibos.importe', 'tbl_recibos.importe_letra', 'tbl_cursos.curso', 'tbl_recibos.descripcion', 'tbl_recibos.num_recibo')
            ->addSelect(\DB::raw("
                    CASE
                        WHEN tbl_cursos.comprobante_pago <> 'null' THEN concat('uploadFiles',tbl_cursos.comprobante_pago)
                        WHEN tbl_recibos.file_pdf <> 'null' THEN tbl_recibos.file_pdf
                    END as file_pdf"))
            ->when($numRecibo, function ($query, $numRecibo) {
                return $query->where('tbl_recibos.num_recibo', '=', $numRecibo);
            })->first();

            $JsonObj = [
                'descripcion' => $query->descripcion,
                'folio' => $query->folio_recibo,
                'curso' => $query->curso,
                'concepto' => $query->concepto,
                'documento' => $query->file_pdf,
                'importe' => $query->importe,
                'importe_letra' => $query->importe_letra,
            ];
            $dataAdd[] = $JsonObj;
        }

       return Rf001Model::create([
            'memorandum' => trim($request->get('consecutivo')),
            'estado' => trim('ENVIADO'),
            'movimientos' => json_encode($dataAdd, JSON_UNESCAPED_UNICODE),
            'id_unidad' => $request->get('id_unidad'),
            'unidad' => $request->get('unidad'),
            'periodo_inicio' => $request->get('periodoInicio'),
            'periodo_fin' => $request->get('periodoFIn'),
        ]);
    }

    public function sentRF001Format($request)
    {
        $rf001Model = new Rf001Model();

        return $rf001Model::where("estado", 'ENVIADO')->latest()->paginate(10 ?? 5);
    }

    public function getDetailRF001Format($concentrado)
    {
        $rf001Detalle = new Rf001Model();
        return $rf001Detalle::findOrFail($concentrado);
    }
}
