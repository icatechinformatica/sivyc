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
use App\Models\directorio;
use App\Models\contrato_directorio;
use App\Models\especialidad;
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
        $perfil = new especialidad();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id','instructores.id_especialidad')
                        ->WHERE('id_folios', '=', $id)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();

        $perfil_prof = $perfil::WHERE('id', '=', $data->id_especialidad)->GET();

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
        $contrato->instructor_perfilid = $request->perfil_instructor;
        $contrato->cantidad_letras1 = $request->cantidad_letras;
        $contrato->cantidad_numero = $request->cantidad_numero;
        $contrato->municipio = $request->lugar_expedicion;
        $contrato->fecha_firma = $request->fecha_firma;
        $contrato->unidad_capacitacion = $request->unidad_capacitacion;
        $contrato->id_folios = $request->id_folio;
        $contrato->save();

        $id_contrato = contratos::SELECT('id_contrato')->WHERE('numero_contrato', '=', $request->numero_contrato)->FIRST();
        $directorio = new contrato_directorio();
        $directorio->contrato_iddirector = $request->id_director;
        $directorio->contrato_idtestigo1 = $request->id_testigo1;
        $directorio->contrato_idtestigo2 = $request->id_testigo2;
        $directorio->contrato_idtestigo3 = $request->id_testigo3;
        $directorio->id_contrato = $id_contrato->id_contrato;
        $directorio->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Contratado']);

        return redirect()->route('contrato-inicio')
                    ->with('success','Suficiencia Presupuestal Validado');
    }

    public function modificar($id)
    {
        $folio = new folio();
        $especialidad = new especialidad();

        $datacon = contratos::WHERE('id_contrato', '=', $id)->FIRST();
        $data = $folio::SELECT('folios.id_folios','folios.iva','tbl_cursos.clave','tbl_cursos.nombre','instructores.nombre AS insnom','instructores.apellidoPaterno',
                               'instructores.apellidoMaterno','instructores.id')
                        ->WHERE('id_folios', '=', $datacon->id_folios)
                        ->LEFTJOIN('tbl_cursos','tbl_cursos.id', '=', 'folios.id_cursos')
                        ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                        ->FIRST();
        $perfil_sel = $especialidad::WHERE('id', '=', $datacon->instructor_perfilid)->FIRST();

        $perfil_prof = $especialidad::WHERE('id', '=', $data->id)
                               ->WHERE('id', '!=', $datacon->instructor_perfilid)->GET();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','id')->WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::SELECT('nombre','apellidoPaterno','apellidoMaterno','puesto','id')->WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $nombrecompleto = $data->insnom . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;
        return view('layouts.pages.modcontrato', compact('data','nombrecompleto','perfil_prof','perfil_sel','datacon','director','testigo1','testigo2','testigo3','data_directorio'));
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
        $urldocs = $this->pago_upload($file, $request->id_contrato); #invocamos el método
        // guardamos en la base de datos
        $contrato = contratos::find($request->id_contrato);
        $contrato->docs = trim($urldocs);
        $contrato->save();

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['status' => 'Verificando_Pago']);

        return redirect()->route('contrato-inicio')
                        ->with('success','Solicitud de Pago Agregado');

    }

    public function get_directorio(Request $request){

        $search = $request->search;

        if($search == ''){
            $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->limit(5)->get();
        }else{
            $directorio = directorio::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno','puesto')->where('nombre', 'like', '%' .$search . '%')->limit(5)->get();
        }

        $response = array();
        foreach($directorio as $dir){
            $response[] = array("value"=>$dir->id,"label"=>$dir->nombre . " " .$dir->apellidoPaterno . " " . $dir->apellidoMaterno, "charge"=>$dir->puesto);
        }

        echo json_encode($response);
        exit;
    }


    public function contrato_pdf($id)
    {

        $contrato = new contratos();

        $data_contrato = contratos::WHERE('id_contrato', '=', $id)->FIRST();

        $data_directorio = contrato_directorio::WHERE('id_contrato', '=', $id)->FIRST();
        $director = directorio::WHERE('id', '=', $data_directorio->contrato_iddirector)->FIRST();
        $testigo1 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo1)->FIRST();
        $testigo2 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo2)->FIRST();
        $testigo3 = directorio::WHERE('id', '=', $data_directorio->contrato_idtestigo3)->FIRST();

        $data = $contrato::SELECT('folios.id_folios','folios.importe_total','tbl_cursos.id','tbl_cursos.horas','instructores.nombre','instructores.apellidoPaterno',
                                  'instructores.apellidoMaterno','instructores.folio_ine','instructores.rfc','instructores.curp',
                                  'instructores.domicilio','instructor_perfil.especialidad','especialidades.nombre AS nomes')
                          ->LEFTJOIN('folios', 'folios.id_folios', '=', 'contratos.id_folios')
                          ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                          ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                          ->LEFTJOIN('instructor_perfil', 'instructor_perfil.id', '=', 'contratos.instructor_perfilid')
                          ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'contratos.instructor_perfilid')
                          ->FIRST();
        $nomins = $data->nombre . ' ' . $data->apellidoPaterno . ' ' . $data->apellidoMaterno;

        $date = strtotime($data_contrato->fecha_firma);
        $D = date('d', $date);
        $M = $this->toMonth(date('m', $date));
        $Y = date("Y", $date);

        $monto = explode(".",strval($data_contrato->cantidad_numero));

        $pdf = PDF::loadView('layouts.pdfpages.contratohonorarios', compact('director','testigo1','testigo2','testigo3','data_contrato','data','nomins','D','M','Y','monto'));

        return $pdf->stream('medium.pdf');
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

    protected function pago_upload($pdf, $id)
    {
        $tamanio = $pdf->getClientSize(); #obtener el tamaño del archivo del cliente
        $extensionPdf = $pdf->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $pdfFile = trim("docs"."_".date('YmdHis')."_".$id.".".$extensionPdf);
        $pdf->storeAs('/uploadContrato/contrato/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadContrato/contrato/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    protected function toMonth($m)
    {
        switch ($m) {
            case 1:
                return "Enero";
            break;
            case 2:
                return "Febrero";
            break;
            case 3:
                return "Marzo";
            break;
            case 4:
                return "Abril";
            break;
            case 5:
                return "Mayo";
            break;
            case 6:
                return "Junio";
            break;
            case 7:
                return "Julio";
            break;
            case 8:
                return "Agosto";
            break;
            case 9:
                return "Septiembre";
            break;
            case 10:
                return "Octubre";
            break;
            case 11:
                return "Noviembre";
            break;
            case 12:
                return "Diciembre";
            break;


        }
    }
}
