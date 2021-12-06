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
    public function index(Request $request) {
        
        $year = $request->busquedaYear;
        $mes = $request->busquedaMes;
        $unidad = $request->busquedaPorUnidad;

        if (empty($request->get('busquedaPorUnidad'))) {
            # si está vacio se agrega parte de la condicion
            $condition_ =  ['JIQUIPILAS', 'SAN CRISTOBAL', 'TAPACHULA', 'TONALA', 'YAJALON', 'REFORMA', 
            'OCOSINGO', 'TUXTLA', 'CATAZAJA', 'COMITAN', 'VILLAFLORES'];
        } else {
            # de no ser así se envía con la variable que tiene el request
            $condition_ = [$request->get('busquedaPorUnidad')] ;
        }

        /* if (empty($request->get('busquedaMes'))) {
            $mesesCalendarizado = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"); // ARREGLO DE MESES CALENDARIZADOS
            $fecha_ahora = Carbon::now();
            $fechaActual = Carbon::parse($fecha_ahora);
            $date = $fecha_ahora->format('Y-m-d'); // fecha
            $messeleccionado = $mesesCalendarizado[($fechaActual->format('n')) - 1];
        } else {
            $messeleccionado = $request->get('busquedaMes');
        } */
        
        /**
         * GENERAMOS LA CONSULTA DÓNDE SE CONTABILIZAN TODOS LOS DATOS 
         * DEL SERVIDOR POR EL MES EN EL QUE NOS ENCONTRAMOS
         * ES DECIR EL ULTIMO MES QUE SE HA REPORTADO
         */
        $query_entrega_contable_fotmatot = tbl_curso::select('tblU.ubicacion',
                 DB::raw("COUNT(tbl_cursos.id) AS total_cursos"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'NO REPORTADO' THEN 1 ELSE 0 END ) AS no_reportado_unidad"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'TURNADO_DTA' THEN 1 ELSE 0 END ) AS turnado_dta"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'TURNADO_PLANEACION' THEN 1 ELSE 0 END ) AS turnado_planeacion"),
                 DB::raw("SUM(  CASE  WHEN tbl_cursos.status = 'REPORTADO' THEN 1 ELSE 0 END ) AS reportado"),
                 DB::raw("Round(SUM( CASE WHEN tbl_cursos.status = 'TURNADO_DTA' THEN 1 ELSE 0 END ) * 100/COUNT(tbl_cursos.id)::numeric, 2) AS porcentaje")
                )
        ->JOIN('tbl_unidades as tblU','tblU.unidad', '=', 'tbl_cursos.unidad')
        ->WHEREIN('tblU.ubicacion', $condition_)
        ->whereMonth('fecha_turnado', $request->busquedaMes)
        ->whereYear('fecha_turnado', $request->busquedaYear)
        // ->WHERE(DB::raw("to_char(tbl_cursos.fecha_turnado, 'TMMONTH')"), $messeleccionado)
        ->groupBy('tblU.ubicacion')->get();

        $unidadesIcatech = DB::table('tbl_unidades')->select('ubicacion')->groupby('ubicacion')->get();

        return view('reportes.seguimiento_avances_unidades_formato_t', compact('unidadesIcatech','query_entrega_contable_fotmatot', 'year', 'mes', 'unidad'));

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
