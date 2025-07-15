<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransactionService;
use App\Factories\Transaction\TransactionFactory;

class DummyController extends Controller
{
    protected $transactionService;
    public function __construct(TransactionFactory $factory)
    {
        //cat\CatConcepto
        //Reportes\Recibo
        //Reportes\Rf001Model
        $str = 'Reportes\Rf001Model'; // podemos llamar a diferentes modelos -- desde un mismo controlador
        $this->transactionService = (new TransactionService($str, $factory));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $var = $this->transactionService->obtenerTodoDatos();
        return response()->json([
            'success' => true,
            'mensaje' => 'Productos.',
            'producto' => $var,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('vista_dumy');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $variable = ['concepto' => $reques->get('nombre'), ];
        $data = $request->only(['concepto', 'importe', 'tipo', 'activo']);
        $insertar = $this->transactionService->crearDato($data);
        return response()->json([
            'success' => true,
            'mensaje' => 'Producto creado correctamente.',
            'producto' => $insertar,
        ]);
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
