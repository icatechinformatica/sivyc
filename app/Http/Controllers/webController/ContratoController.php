<?php

namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\contratos;
use App\Models\supre;
use App\Models\folio;

class ContratoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supre = new supre();
        $data = $supre::SELECT('tabla_supre.id','tabla_supre.no_memo','tabla_supre.unidad_capacitacion','tabla_supre.fecha','folios.status','folios.id_folios',
                               'folios.folio_validacion')
                        ->where('folios.status', '!=', 'En Proceso')
                        ->LEFTJOIN('folios', 'tabla_supre.id', '=', 'folios.id_supre')
                        ->get();
        return view('layouts.pages.vstacontratoini', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $folio = new folio();
        $data = $folio::SELECT('folios.iva','tbl_cursos.clave')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','id', '=', 'folios.id_cursos')
                        ->FIRST();
        /**
         * TODO: se tiene que obtener el id del contrato que se va a generar y hacer una consulta
         */
        // vista
        return view('layouts.pages.frmcontrato', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // vamos a guardar el registro
        $validateData = $request->validate([
            'numero_contrato' => 'required',
            'folio_ine' => 'required',
            'cantidad_letras' => 'required',
            'lugar_expedicion' => 'required',
            'fecha_firma' => 'required',
            'testigo_icatech' => 'required',
            'testigo_instructor' => 'required',
            'municipio' => 'required'
        ]);

        $CreateContrato = contratos::create($validateData);

    }

    public function solicitud_pago($id){
        return view('layouts.pages.vstasolicitudpago');
    }


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
