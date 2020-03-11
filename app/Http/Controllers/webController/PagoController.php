<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use App\Models\contratos;
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
        $pago = new pago();

        $dataCont = $contrato::WHERE('status', '=', 'En Proceso')->LATEST()->GET();

        $dataPago = $pago::where('id', '!=', '0')->latest()->get();


        return view('layouts.pages.vstapago', compact('dataPago', 'dataCont'));
    }

    public function crear_pago()
    {
        return view('layouts.pages.frmpago');
    }

    public function modificar_pago()
    {
        return view('layouts.pages.modpago');
    }
}
