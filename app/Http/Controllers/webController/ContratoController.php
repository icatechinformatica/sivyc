<?php

namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\contratos;
use App\Models\InstructorPerfil;
use App\Models\supre;
use App\Models\folio;
use PDF;
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
        $perfil = new InstructorPerfil();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_prof = $perfil::WHERE('numero_control', '=', $data->id)->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        /**
         * TODO: se tiene que obtener el id del contrato que se va a generar y hacer una consulta
         */
        // vista
        return view('layouts.pages.frmcontrato', compact('data','nombrecompleto','perfil_prof'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contrato_save(Request $request)
    {
        $contrato = new contratos();
        $contrato->numero_contrato = $request->numero_contrato;
        $contrato->cantidad_letras1 = $request->cantidad_letras1;
        $contrato->cantidad_letras2 = $request->cantidad_letras2;
        $contrato->numero_circular = $request->no_circulardir;
        $contrato->nombre_director = $request->nombre_director;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->testigo1 = $request->testigo1;
        $contrato->puesto_testigo1 = $request->puesto_testigo1;
        $contrato->testigo2 = $request->testigo2;
        $contrato->puesto_testigo2 = $request->puesto_testigo2;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->id_folios = $request->id_folio;
        $contrato->instructor_perfilid = $request->perfil_instructor;
        $contrato->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Contratado']);

        return redirect()->route('contrato-inicio')
                    ->with('success','Suficiencia Presupuestal Validado');
    }

    public function solicitud_pago($id){
        return view('layouts.pages.vstasolicitudpago');
    }


    public function contrato_pdf($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_folios', '=', $id)->FIRST();
        $data = $contrato::SELECT('folios.id_folios','tbl_cursos.id','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio','instructor_perfil.especialidad')
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->LEFTJOIN('instructor_perfil', 'instructor_perfil.id', '=', 'contratos.instructor_perfilid')
                          ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('data_contrato','data','nomins'));

         return $pdf->stream('medium.pdf');

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
