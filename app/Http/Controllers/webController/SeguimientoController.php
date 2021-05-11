<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\tbl_curso;

class SeguimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /***
         * OBTENEMOS LA FECHA ACTUAL
         */
        $mesesCalendarizado = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"); // ARREGLO DE MESES CALENDARIZADOS
        $fecha_ahora = Carbon::now();
        $fechaActual = Carbon::parse($fecha_ahora);
        $date = $fecha_ahora->format('Y-m-d'); // fecha
        //MES ACTUAL
        $mesActual = $mesesCalendarizado[($fechaActual->format('n')) - 1];
        /**
         * VARIABLES DE BUSQUEDA
         */
        $busquedaPorMes = $request->get('busquedaMes');
        $busquedaPorUnidad = $request->get('busquedaPorUnidad');
        /**
         * GENERAMOS LA CONSULTA DÓNDE SE CONTABILIZAN TODOS LOS DATOS 
         * DEL SERVIDOR POR EL MES EN EL QUE NOS ENCONTRAMOS
         * ES DECIR EL ULTIMO MES QUE SE HA REPORTADO
         */
        // DB::connection()->enableQueryLog();
        $query_entrega_contable_fotmatot = tbl_curso::searchbyunidadmes($busquedaPorUnidad, $busquedaPorMes)
            ->select('tblU.ubicacion', 
                 DB::raw("COUNT(tbl_cursos.id) AS total_cursos"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'NO REPORTADO' THEN 1 ELSE 0 END ) AS no_reportado_unidad"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'TURNADO_DTA' THEN 1 ELSE 0 END ) AS turnado_dta"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'TURNADO_PLANEACION' THEN 1 ELSE 0 END ) AS turnado_planeacion"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'REPORTADO' THEN 1 ELSE 0 END ) AS reportado"),
                 DB::raw("Round(SUM( CASE WHEN tbl_cursos.status = 'TURNADO_DTA' THEN 1 ELSE 0 END ) * 100/COUNT(tbl_cursos.id)::numeric, 2) AS porcentaje")
                )
        ->JOIN('tbl_unidades as tblU','tblU.unidad', '=', 'tbl_cursos.unidad')
        ->WHEREIN('tblU.ubicacion', ['JIQUIPILAS', 'SAN CRISTOBAL', 'TAPACHULA', 'TONALA', 'YAJALON', 'REFORMA', 
        'OCOSINGO', 'TUXTLA', 'CATAZAJA', 'COMITAN', 'VILLAFLORES'])
        ->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), $mesActual)
        ->groupBy('tblU.ubicacion')->get();

        // dd(DB::getQueryLog());

        $unidadesIcatech = DB::table('tbl_unidades')->select('ubicacion')->groupby('ubicacion')->get();
        $meses = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
        return view('reportes.seguimiento_avances_unidades_formato_t', compact('unidadesIcatech', 'meses', 'query_entrega_contable_fotmatot', 'mesActual'));

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
