<?php
//Creado por Orlando Chavez
namespace App\Http\Controllers\WebController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\contratos;
use App\Models\InstructorPerfil;
use App\Models\supre;
use App\Models\folio;
use App\Models\pago;
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
                               'folios.folio_validacion','contratos.docs','contratos.id_contrato')
                        ->where('folios.status', '!=', 'En_Proceso')
                        ->LEFTJOIN('folios', 'tabla_supre.id', '=', 'folios.id_supre')
                        ->LEFTJOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
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

    public function modificar($id)
    {
        $folio = new folio();
        $perfil = new InstructorPerfil();

        $datacon = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id')
                        ->WHERE('id_folios', '=', $datacon->id_folios)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();
        $perfil_sel = $perfil::WHERE('id', '=', $datacon->instructor_perfilid)->FIRST();

        $perfil_prof = $perfil::WHERE('numero_control', '=', $data->id)
                               ->WHERE('id', '!=', $datacon->instructor_perfilid)->GET();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        return view('layouts.pages.modcontrato', compact('data','nombrecompleto','perfil_prof','perfil_sel','datacon'));
    }

    public function save_mod(Request $request){
        contratos::where('id_contrato', '=', $request->id_contrato)
        ->update(['numero_contrato' => $request->numero_contrato,
                  'instructor_perfilid' => $request->perfil_instructor,
                  'cantidad_letras1' => $request->cantidad_letras1,
                  'cantidad_letras2' => $request->cantidad_letras2,
                  'municipio' => $request->lugar_expedicion,
                  'fecha_firma' => $request->fecha_firma,
                  'nombre_director' => $request->nombre_director,
                  'unidad_capacitacion' => $request->unidad_capacitacion,
                  'numero_circular' => $request->no_circulardir,
                  'testigo1' => $request->testigo1,
                  'puesto_testigo1' => $request->puesto_testigo1,
                  'testigo2' => $request->testigo2,
                  'puesto_testigo2' => $request->puesto_testigo2]);

        supre::where('id', '=', $request->id_supre)
        ->update(['status' => 'En_Proceso']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Contrato Modificado');
    }

    public function solicitud_pago($id){
        $X = new contratos();
        $folio = new folio();
        $dataf = $folio::where('id_folios', '=', $id)->first();
        $datac = $X::where('id_folios', '=', $id)->first();
        return view('layouts.pages.vstasolicitudpago', compact('datac','dataf'));
    }

    public function save_doc(Request $request){
        $pago = new pago();

        $pago->no_memo = $request->no_memo;
        $pago->elaboro = $request->elaboro;
        $pago->nombre_para = $request->nombre_para;
        $pago->puesto_para = $request->puesto_para;
        $pago->nombre_ccp1 = $request->nombre_ccp1;
        $pago->puesto_ccp1 = $request->puesto_ccp1;
        $pago->nombre_ccp2 = $request->nombre_ccp2;
        $pago->puesto_ccp2 = $request->puesto_ccp2;
        $pago->nombre_ccp3 = $request->nombre_ccp3;
        $pago->puesto_ccp3 = $request->puesto_ccp3;
        $pago->id_contrato = $request->id_contrato;
        $pago->save();


        $file = $request->file('doc_pdf'); # obtenemos el archivo
        $urldocs = $this->pdf_upload($file, $request->id_contrato); #invocamos el método
        // guardamos en la base de datos
        $contrato = contratos::find($request->id_contrato);
        $contrato->docs = trim($urldocs);
        $contrato->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Agregado');

    }


    public function contrato_pdf($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio','instructor_perfil.especialidad')
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->LEFTJOIN('instructor_perfil', 'instructor_perfil.id', '=', 'contratos.instructor_perfilid')
                          ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = date('m',$date);
        $Y = date("Y",$date);

        $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('data_contrato','data','nomins','D','M','Y'));

        return $pdf->download('medium.pdf');
    }

    public function solicitudpago_pdf($id){

        $data = folio::SELECT('tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.espe','tbl_cursos.mod','tbl_cursos.inicio',
                              'tbl_cursos.termino','tbl_cursos.hini','tbl_cursos.hfin','tbl_cursos.id AS id_curso','instructores.nombre',
                              'instructores.apellidoPaterno','instructores.apellidoMaterno','instructores.memoramdum_validacion',
                              'instructores.rfc','instructores.id AS id_instructor','instructores.banco','instructores.no_cuenta',
                              'instructores.interbancaria','folios.importe_total','folios.id_folios','contratos.unidad_capacitacion',
                              'contratos.nombre_director','pagos.created_at','pagos.no_memo','pagos.nombre_ccp1','pagos.puesto_ccp1',
                              'pagos.nombre_ccp2','pagos.puesto_ccp2','pagos.nombre_ccp3','pagos.puesto_ccp3','pagos.elaboro','pagos.nombre_para',
                              'pagos.puesto_para')
                        ->WHERE('folios.id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->LEFTJOIN('contratos', 'contratos.id_folios', '=', 'folios.id_folios')
                        ->LEFTJOIN('pagos', 'pagos.id_contrato', '=', 'contratos.id_contrato')
                        ->FIRST();

        $date = strtotime($data->created_at);
        $D = date('d', $date);
        $M = date('m',$date);
        $Y = date("Y",$date);

        $pdf = PDF::loadView('layouts.pdfpages.procesodepago', compact('data','D','M','Y'));

        return $pdf->download('medium.pdf');
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

    protected function pdf_upload($pdf, $id)
    {
        $tamanio = $pdf->getClientSize(); #obtener el tamaño del archivo del cliente
        $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $pdfFile = trim("docs"."_".date('YmdHis')."_".$id.".".$extensionPdf);
        $pdf->storeAs('/uploadContrato/contrato/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadContrato/contrato/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }
}
