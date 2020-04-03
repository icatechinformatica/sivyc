<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\supre;
use App\Models\folio;
use App\Models\tbl_curso;
use App\ProductoStock;
use App\Models\cursoValidado;
use App\Models\supre_directorio;
use App\Models\directorio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use function PHPSTORM_META\type;

class supreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    Public function opcion(){
        return view('layouts.pages.vstasolicitudopc');
    }

     public function solicitud_supre_inicio() {
        $supre = new supre();
        $data = $supre::where('id', '!=', '0')->latest()->get();



        return view('layouts.pages.vstasolicitudsupre', compact('data'));
    }

    public function solicitud_folios(){
        $supre = new supre();
        $data2 = $supre::SELECT('tabla_supre.id','tabla_supre.no_memo','tabla_supre.unidad_capacitacion','tabla_supre.fecha','folios.status','folios.id_folios',
        'folios.folio_validacion')
                        ->where('folios.status', '!=', 'x')
                        ->LEFTJOIN('folios', 'tabla_supre.id', '=', 'folios.id_supre')
                        ->get();

        return view('layouts.pages.vstasolicitudfolio', compact('data2'));
    }

    public function frm_formulario() {
        return view('layouts.pages.delegacionadmin');
    }

    public function store(Request $request) {
        $supre = new supre();
        $curso_validado = new tbl_curso();
        $directorio = new supre_directorio();

        //Guarda Solicitud
        $supre->unidad_capacitacion = $request->unidad;
        $supre->no_memo = $request->memorandum;
        $supre->fecha = $request->fecha;
        $supre->status = 'En_Proceso';
        $supre->save();

       $id = $supre->SELECT('id')->WHERE('no_memo', '=', $request->memorandum)->FIRST();
       $directorio->supre_dest = $request->id_destino;
       $directorio->supre_rem = $request->id_remitente;
       $directorio->supre_valida = $request->id_valida;
       $directorio->supre_elabora = $request->id_elabora;
       $directorio->supre_ccp1 = $request->id_ccp1;
       $directorio->supre_ccp2 = $request->id_ccp2;
       $directorio->id_supre = $id->id;
       $directorio->save();

        //Guarda Folios
        foreach ($request->addmore as $key => $value){
            $folio = new folio();
            $folio->folio_validacion = $value['folio'];
            $folio->numero_presupuesto = $value['numeropresupuesto'];
            $folio->iva = $value['iva'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('tbl_cursos.horas','tbl_cursos.id')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            $importe = $value['importe'];
            $importe_hora = $importe / $hora->horas;
            $folio->importe_hora = $importe_hora;
            $folio->importe_total = $value['importe'];
            $folio->id_supre = $id->id;
            $folio->id_cursos = $hora->id;
            $folio->status = 'En_Proceso';
            $folio->save();
        }

        return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function solicitud_modificar($id)
    {
        $supre = new supre();
        $folio = new folio();

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getsupre = $supre::WHERE('id', '=', $id)->FIRST();
        $getfolios = $folio::SELECT('folios.id_folios','folios.folio_validacion','folios.numero_presupuesto',
                                    'folios.importe_total','folios.iva','tbl_cursos.clave')
                            ->WHERE('id_supre','=', $getsupre->id)
                            ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                            ->GET();
        $getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();
        return view('layouts.pages.modsupre',compact('getsupre','getfolios','getdestino','getremitente','getvalida','getelabora','getccp1','getccp2','directorio'));
    }

    public function solicitud_mod_guardar(Request $request)
    {
        $supre = new supre();
        $curso_validado = new tbl_curso();

        supre::where('id', '=', $request->id_supre)
        ->update(['status' => 'En_Proceso',
                  'unidad_capacitacion' => $request->unidad_capacitacion,
                  'no_memo' => $request->no_memo,
                  'fecha' => $request->fecha]);

        supre_directorio::where('id', '=', $request->id_directorio)
        ->update(['supre_dest' => $request->id_destino,
                  'supre_rem' => $request->id_remitente,
                  'supre_valida' => $request->id_valida,
                  'supre_elabora' => $request->id_elabora,
                  'supre_ccp1' => $request->id_ccp1,
                  'supre_ccp2' => $request->id_ccp2,]);

            if($request->id_supre != NULL)
            {
                folio::WHERE('id_supre', '=', $request->id_supre)->DELETE();
            }
            $id = $supre::SELECT('id')->WHERE('no_memo', '=', $request->no_memo)->FIRST();
        //Guarda Folios
        foreach ($request->addmore as $key => $value){
            $folio = new folio();
            $folio->folio_validacion = $value['folio'];
            $folio->numero_presupuesto = $value['numeropresupuesto'];
            $folio->iva = $value['iva'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('tbl_cursos.horas','tbl_cursos.id')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            $importe = $value['importe'];
            $importe_hora = $importe / $hora->horas;
            $folio->importe_hora = $importe_hora;
            $folio->importe_total = $value['importe'];
            $folio->id_supre = $id->id;
            $folio->id_cursos = $hora->id;
            $folio->status = 'En_Proceso';
            $folio->save();
        }

        return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function validacion_supre_inicio(){
        return view('layouts.pages.initvalsupre');
    }

    public function validacion($id){
        $supre = new supre();
        $data =  $supre::WHERE('id', '=', $id)->FIRST();
        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        return view('layouts.pages.valsupre',compact('data','getremitente','directorio'));
    }

    public function supre_rechazo(Request $request){
        $supre = supre::find($request->id);
        $supre->observacion = $request->comentario_rechazo;
        $supre->status = 'Rechazado';
        //dd($supre);
        $supre->save();
            return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Rechazado');
    }

    public function supre_validado(Request $request){
        $supre = supre::find($request->id);
        $supre->status = 'Validado';
        $supre->folio_validacion = $request->folio_validacion;
        $supre->fecha_validacion = $request->fecha_validacion;
        $supre->save();

        supre_directorio::where('id', '=', $request->directorio_id)
        ->update(['val_firmante' => $request->id_firmante,
                  'val_ccp1' => $request->id_ccp1,
                  'val_ccp2' => $request->id_ccp2,
                  'val_ccp3' => $request->id_ccp3,
                  'val_ccp4' => $request->id_ccp4,]);

        folio::where('id_supre', '=', $request->id)
        ->update(['status' => 'Validado']);

            return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Validado');
    }

    public function supre_pdf($id){
        $supre = new supre();
        $folio = new folio();
        $data_supre = $supre::WHERE('id', '=', $id)->FIRST();
        $data_folio = $folio::WHERE('id_supre', '=', $id)->GET();
        $date = strtotime($data_supre->fecha);
        $D = date('d', $date);
        $M = date('m',$date);
        $Y = date("Y",$date);

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();

        $pdf = PDF::loadView('layouts.pdfpages.presupuestaria',compact('data_supre','data_folio','D','M','Y','getdestino','getremitente','getvalida','getelabora','getccp1','getccp2','directorio'));
        return  $pdf->download('medium.pdf');
    }

    public function tablasupre_pdf($id){
        $supre = new supre;
        $curso = new tbl_curso;
        $data = supre::SELECT('tabla_supre.fecha','folios.numero_presupuesto','folios.importe_hora','folios.iva','folios.importe_total',
                        'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'tbl_cursos.nombre AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.horas')
                    ->WHERE('id_supre', '=', $id )
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();
        $data2 = supre::WHERE('id', '=', $id)->FIRST();

        $date = strtotime($data2->fecha);
        $D = date('d', $date);
        $M = date('m',$date);
        $Y = date("Y",$date);

        $datev = strtotime($data2->fecha_validacion);
        $Dv = date('d', $datev);
        $Mv = date('m',$datev);
        $Yv = date("Y",$datev);

        $pdf = PDF::loadView('layouts.pdfpages.solicitudsuficiencia', compact('data','data2','D','M','Y','Dv','Mv','Yv'));
        $pdf->setPaper('A4', 'Landscape');



        return $pdf->download('medium.pdf');

        return view('layouts.pdfpages.solicitudsuficiencia', compact('data','data2'));
    }

    public function valsupre_pdf($id){
        $supre = new supre;
        $curso = new tbl_curso;
        $data = supre::SELECT('tabla_supre.fecha','folios.numero_presupuesto','folios.importe_hora','folios.iva','folios.importe_total',
                        'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'tbl_cursos.nombre AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.horas')
                    ->WHERE('id_supre', '=', $id )
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();
        $data2 = supre::WHERE('id', '=', $id)->FIRST();

        $date = strtotime($data2->fecha);
        $D = date('d', $date);
        $M = date('m',$date);
        $Y = date("Y",$date);

        $datev = strtotime($data2->fecha_validacion);
        $Dv = date('d', $datev);
        $Mv = date('m',$datev);
        $Yv = date("Y",$datev);

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getfirmante = directorio::WHERE('id', '=', $directorio->val_firmante)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->val_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->val_ccp2)->FIRST();
        $getccp3 = directorio::WHERE('id', '=', $directorio->val_ccp3)->FIRST();
        $getccp4 = directorio::WHERE('id', '=', $directorio->val_ccp4)->FIRST();

        $pdf = PDF::loadView('layouts.pdfpages.valsupre', compact('data','data2','D','M','Y','Dv','Mv','Yv','getremitente','getfirmante','getccp1','getccp2','getccp3','getccp4'));
        $pdf->setPaper('A4', 'Landscape');



        return $pdf->download('medium.pdf');

        return view('layouts.pdfpages.valsupre', compact('data','data2'));
    }

}
