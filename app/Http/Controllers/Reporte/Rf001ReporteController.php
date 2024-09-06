<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Interfaces\Reporterf001Interface;

class Rf001ReporteController extends Controller
{
    private Reporterf001Interface $rfoo1Repository;
    public function __construct(Reporterf001Interface $rfoo1Repository)
    {
        $this->rfoo1Repository = $rfoo1Repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // datos de información!!!
        $organismo = Auth::user()->id_organismo;
        $unidad = Auth::user()->unidad;
        return (new ReportService())->xmlFormat($id, $organismo, $unidad, Auth::user());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $solicitud)
    {
        $idFormato = $id;
        return view('reportes.rf001.formatofirma', compact('idFormato', 'solicitud'))->render();
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

    public function generate_report($id)
    {
        $organismo = Auth::user()->id_organismo;
        $unidad = Auth::user()->unidad;
        $data = (new ReportService())->xmlFormat($id, $organismo, $unidad, Auth::user());
        return response()->json([
            'resp' => $data
         ], Response::HTTP_CREATED);
    }

    public function efirma()
    {
        return view('reportes.rf001.child.firma')->render();
    }

    public function getTokenFirma(Request $request)
    {
        return (new ReportService())->generarToken();
    }
}
