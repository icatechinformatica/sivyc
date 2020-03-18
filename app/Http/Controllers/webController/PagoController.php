<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use App\Models\contratos;
use App\Models\folio;
use Illuminate\Http\Request;
use Redirect,Response;
use App\Http\Controllers\Controller;

class PagoController extends Controller
{
    public function fill(Request $request)
    {
        $instructor = new instructor();
        $input = $request->numero_contrato;
        $newsAll = $instructor::where('id', $input)->first();
        return response()->json($newsAll, 200);
    }

    public function index()
    {
        $contrato = new contratos();

        $contratos_folios = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1', 'contratos.cantidad_letras2',
        'contratos.numero_circular', 'contratos.nombre_director', 'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.testigo1', 'contratos.puesto_testigo1',
        'contratos.testigo2', 'contratos.puesto_testigo2', 'contratos.fecha_firma', 'contratos.docs', 'contratos.observacion', 'folios.status')
        ->WHERE('folios.status', '=', 'verificando_pago')
        ->ORWHERE('folios.status', '=', 'pago_verificado')
        ->ORWHERE('folios.status', '=', 'finalizado')
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->GET();


        return view('layouts.pages.vstapago', compact('contratos_folios'));
    }

    public function crear_pago()
    {
        return view('layouts.pages.frmpago');
    }

    public function modificar_pago()
    {
        return view('layouts.pages.modpago');
    }

    public function verificar_pago($idfolios)
    {
        $folio = folio::findOrfail($idfolios);
        $folio->status = 'pago_verificado';
        $folio->save();
        return redirect()->route('pago-inicio');
    }
}
