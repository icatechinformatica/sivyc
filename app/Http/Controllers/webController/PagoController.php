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
        'contratos.testigo2', 'contratos.puesto_testigo2', 'contratos.fecha_firma', 'contratos.docs', 'contratos.observacion', 'folios.status', 'folios.id_folios')
        ->WHEREIN('folios.status', ['Verificando_Pago','Pago_Verificado','Pago_Rechazado','Finalizado'])
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
        $folio->status = 'Pago_Verificado';
        $folio->save();
        return redirect()->route('pago-inicio');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $contrato = new contratos();

        $contratos = $contrato::SELECT('contratos.id_contrato', 'contratos.numero_contrato', 'contratos.cantidad_letras1', 'contratos.cantidad_letras2',
        'contratos.numero_circular', 'contratos.nombre_director', 'contratos.unidad_capacitacion', 'contratos.municipio', 'contratos.testigo1', 'contratos.puesto_testigo1',
        'contratos.testigo2', 'contratos.puesto_testigo2', 'contratos.fecha_firma', 'contratos.docs', 'contratos.observacion', 'folios.status', 'folios.id_folios')
        ->WHERE('contratos.id_contrato', '=', $id)
        ->LEFTJOIN('folios','folios.id_folios', '=', 'contratos.id_folios')
        ->GET();

        return view('layouts.pages.vstvalidarpago', compact('contratos'));
    }
}
