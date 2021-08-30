<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\Models\supre;
use App\Models\folio;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\supre_directorio;
use App\Models\directorio;
use App\Models\criterio_pago;
use App\Models\tbl_unidades;
use App\Models\contratos;
use App\Models\contrato_directorio;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PDF;
use function PHPSTORM_META\type;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport; // agregamos la exportación de FormatoTReport
use App\Models\pago;
use Illuminate\Support\Facades\Auth;

class supreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function solicitud_supre_inicio(Request $request) {
        /**
         * parametros de busqueda
         */
        $busqueda_suficiencia = $request->get('busquedaporSuficiencia');
        $tipoSuficiencia = $request->get('tipo_suficiencia');
        $tipoStatus = $request->get('tipo_status');
        $unidad = $request->get('unidad');

        $supre = new supre();
        $data = $supre::BusquedaSupre($tipoSuficiencia, $busqueda_suficiencia, $tipoStatus, $unidad)->where('id', '!=', '0')->latest()->get();
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstasolicitudsupre', compact('data', 'unidades'));
    }

    public function frm_formulario() {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.delegacionadmin', compact('unidades'));
    }

    public function store(Request $request) {
        $memo = supre::SELECT('no_memo')->WHERE('no_memo', '=', $request->memorandum)->FIRST();
        if (is_null($memo))
        {
            foreach ($request->addmore as $key => $value)
            {
                $validacion_folio = folio::SELECT('folio_validacion')
                 ->WHERE('folio_validacion', '=', $value['folio'])
                 ->WHERE('status', '!=', 'Cancelado')
                 ->FIRST();
                 if (isset($validacion_folio))
                 {
                    return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE FOLIO INGRESADO YA SE ENCUENTRA REGISTRADO', $validacion_folio));
                 }
                 $claveval = tbl_curso::SELECT('id')->WHERE('clave', '=', $value['clavecurso'])->FIRST();
                 $validacion_curso = folio::SELECT('id_cursos')
                 ->WHERE('id_cursos', '=', $claveval->id)
                 ->WHERE('status', '!=', 'Cancelado')
                 ->FIRST();
                 if (isset($validacion_curso))
                 {
                    return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL CURSO INGRESADO YA SE ENCUENTRA REGISTRADO', $validacion_curso));
                 }
            }
            $supre = new supre();
            $curso_validado = new tbl_curso();
            $directorio = new supre_directorio();

            //Guarda Solicitud
            $supre->unidad_capacitacion = strtoupper($request->unidad);
            $supre->no_memo = strtoupper($request->memorandum);
            $supre->fecha = strtoupper($request->fecha);
            $supre->status = 'En_Proceso';
            $supre->fecha_status = strtoupper($request->fecha);
            $supre->save();

            $id = $supre->id;
            $directorio->supre_dest = $request->id_destino;
            $directorio->supre_rem = $request->id_remitente;
            $directorio->supre_valida = $request->id_valida;
            $directorio->supre_elabora = $request->id_elabora;
            $directorio->supre_ccp1 = $request->id_ccp1;
            $directorio->supre_ccp2 = $request->id_ccp2;
            $directorio->id_supre = $id;
            $directorio->save();

            //Guarda Folios
            foreach ($request->addmore as $key => $value)
            {
                $folio = new folio();
                $folio->folio_validacion = strtoupper($value['folio']);
                $folio->iva = $value['iva'];
                $folio->comentario = $value['comentario'];
                $clave = strtoupper($value['clavecurso']);
                $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id')
                        ->WHERE('tbl_cursos.clave', '=', $clave)
                        ->FIRST();
                $importe = $value['importe']/1.16;
                $X = $hora->dura;
                if ($X != NULL)
                {
                    if (strpos($hora->dura, " "))
                    {
                        # si tiene un espacio en blanco la cadena
                        $str_horas = explode (" ", $hora->dura);
                        $horas = (int) $str_horas[0];
                    } else
                    {
                        $horas = (int) $hora->dura;
                    }
                    $importe_hora = $importe / $horas;
                    $folio->importe_hora = $importe_hora;
                    $folio->importe_total = $value['importe'];
                    $folio->id_supre = $id;
                    $folio->id_cursos = $hora->id;
                    $folio->status = 'En_Proceso';
                    $folio->save();
                }
                else
                {
                    supre::WHERE('id', '=', $id)->DELETE();
                    supre_directorio::WHERE('id_supre', '=', $id)->DELETE();
                    return redirect()->route('supre-inicio')
                            ->with('success','Error Interno. Intentelo mas tarde.');
                }
            }

            return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
        }
        else
        {
            return redirect()->route('frm-supre')
                    ->withErrors(sprintf('LO SENTIMOS, EL NUMERO DE MEMORANDUM INGRESADO YA SE ENCUENTRA REGISTRADO', $request->memorandum));
        }
    }

    public function solicitud_modificar($id)
    {
        $supre = new supre();
        $folio = new folio();
        $getdestino = null;
        $getremitente = null;
        $getvalida = null;
        $getelabora = null;
        $getccp1 = null;
        $getccp2 = null;

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getsupre = $supre::WHERE('id', '=', $id)->FIRST();

        $unidadsel = tbl_unidades::SELECT('unidad')->WHERE('unidad', '=', $getsupre->unidad_capacitacion)->FIRST();
        $unidadlist = tbl_unidades::SELECT('unidad')->WHERE('unidad', '!=', $getsupre->unidad_capacitacion)->GET();

        $getfolios = $folio::SELECT('folios.id_folios','folios.folio_validacion','folios.comentario',
                                    'folios.importe_total','folios.iva','tbl_cursos.clave')
                            ->WHERE('id_supre','=', $getsupre->id)
                            ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                            ->GET();
        /*if($directorio->supre_dest != NULL)
        {
            $getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        }*/
        if($directorio->supre_rem != NULL)
        {
            $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        }
        if($directorio->supre_valida != NULL)
        {
            $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        }
        if($directorio->supre_elabora != NULL)
        {
            $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        }
       /* if($directorio->supre_ccp1 != NULL)
        {
            $getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        }
        if($directorio->supre_ccp2 != NULL)
        {
            $getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();
        }*/

        //dd($getfolios);
        return view('layouts.pages.modsupre',compact('getsupre','getfolios','getremitente','getvalida','getelabora','directorio', 'unidadsel','unidadlist'));
    }

    public function solicitud_mod_guardar(Request $request)
    {
        //dd($request);
        $supre = new supre();
        $curso_validado = new tbl_curso();

        supre::where('id', '=', $request->id_supre)
        ->update(['status' => 'En_Proceso',
                  'unidad_capacitacion' => $request->unidad,
                  'no_memo' => $request->no_memo,
                  'fecha' => $request->fecha,
                  'fecha_status' => carbon::now()]);

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
            $folio->iva = $value['iva'];
            $folio->comentario = $value['comentario'];
            $clave = $value['clavecurso'];
            $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id')
                    ->WHERE('tbl_cursos.clave', '=', $clave)
                    ->FIRST();
            $importe = $value['importe']/1.16;
            $importe_hora = $importe / $hora->dura;
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
        $supre->fecha_status = carbon::now();
        $supre->fecha_rechazado = carbon::now();
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
        $supre->fecha_validacion = $request->fecha_val;
        $supre->fecha_status = carbon::now();
        $supre->save();

        supre_directorio::where('id', '=', $request->directorio_id)
        ->update(['val_firmante' => $request->id_firmante,
                  'val_ccp1' => $request->id_ccp1,
                  'val_ccp2' => $request->id_ccp2,
                  'val_ccp3' => $request->id_ccp3,
                  'val_ccp4' => $request->id_ccp4,]);

        folio::where('id_supre', '=', $request->id)
        ->update(['status' => 'Validado']);

        $id = $request->id;
        $directorio_id = $request->directorio_id;
        return view('layouts.pages.valsuprecheck', compact('id', 'directorio_id'));
    }

    public function valsupre_checkmod(Request $request){
        $data = supre::find($request->id);
        $directorio = supre_directorio::find($request->directorio_id);
        $getfirmante = directorio::WHERE('id', '=', $directorio->val_firmante)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->val_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->val_ccp2)->FIRST();
        $getccp3 = directorio::WHERE('id', '=', $directorio->val_ccp3)->FIRST();
        $getccp4 = directorio::WHERE('id', '=', $directorio->val_ccp4)->FIRST();

        return view('layouts.pages.valsupremod', compact('data', 'directorio','getremitente','getfirmante','getccp1','getccp2','getccp3','getccp4'));
    }

    public function delete($id)
    {
        supre_directorio::WHERE('id_supre', '=', $id)->DELETE();
        folio::where('id_supre', '=', $id)->delete();
        supre::where('id', '=', $id)->delete();

        return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Eliminada');
    }

    public function restartSupre($id)
    {
        $list = folio::SELECT('id_folios')->WHERE('id_supre', '=', $id)->GET();
        foreach($list as $item)
        {
            $idcontrato = contratos::SELECT('id_contrato')->WHERE('id_folios', '=', $item->id_folios)->FIRST();
            if($idcontrato != NULL)
            {
                contrato_directorio::WHERE('id_contrato', '=', $idcontrato->id_contrato)->DELETE();
                contratos::where('id_folios', '=', $item->id_folios)->DELETE();
            }
            $affecttbl_inscripcion = DB::table("folios")->WHERE('id_folios', $item->id_folios)->update(['status' => 'Rechazado']);
        }

        DB::table('tabla_supre')->WHERE('id', $id)->UPDATE(['status' => 'Rechazado', 'doc_validado' => '']);

        return redirect()->route('supre-inicio')
                    ->with('success','Suficiencia Presupuestal Reiniciada');
    }

    public function reporte_solicitados(Request $request)
    {
        //dd($request->all());
        $fecha_inicio = $request->fecha_inicio;
        $fecha_termino = $request->fecha_termino;
        $consulta1 = DB::table('tabla_supre')->SELECT('tbl_unidades.unidad',
        DB::raw('SUM(CASE WHEN tabla_supre.id != 0 THEN 1 ELSE 0 END) as supre_total'),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'En_Proceso' THEN 1 ELSE 0 END) as supre_proceso"),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'Validado' THEN 1 ELSE 0 END) as supre_validados"),
        DB::raw("SUM(CASE WHEN tabla_supre.status = 'Rechazado' THEN 1 ELSE 0 END) as supre_rechazados"),
        DB::raw("ARRAY(SELECT tabla_supre.fecha_rechazado FROM tabla_supre WHERE status = 'Rechazado'
                AND tabla_supre.unidad_capacitacion = tbl_unidades.unidad) as supre_fecha_rechazo"),
        DB::raw("ARRAY(SELECT tabla_supre.observacion FROM tabla_supre WHERE status = 'Rechazado'
                AND tabla_supre.unidad_capacitacion = tbl_unidades.unidad) as supre_observaciones"))
        ->join('tbl_unidades','tbl_unidades.unidad','=','tabla_supre.unidad_capacitacion');

        $consulta2 = DB::table('folios')->SELECT('tbl_unidades.unidad',
        DB::raw("SUM(CASE WHEN folios.status in ('Validando_Contrato','Contratado','Contrato_Rechazado') THEN 1
                ELSE 0 END) as contrato_total"),
        DB::raw("SUM(CASE WHEN folios.status = 'Validando_Contrato' THEN 1 ELSE 0 END) as contrato_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contratado' THEN 1 ELSE 0 END) as contrato_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Contrato_Rechazado' THEN 1 ELSE 0 END) as contrato_rechazados"),
        DB::raw("ARRAY(SELECT folios.fecha_rechazado FROM tabla_supre
                INNER JOIN folios ON folios.id_supre = tabla_supre.id
                WHERE folios.status = 'Contrato_Rechazado'
                AND tbl_unidades.unidad = tabla_supre.unidad_capacitacion) as contrato_fecha_rechazo"),
        DB::raw("ARRAY(SELECT contratos.observacion FROM folios
                INNER JOIN contratos ON contratos.id_folios = folios.id_folios
                WHERE folios.status = 'Contrato_Rechazado'
                AND contratos.unidad_capacitacion = tbl_unidades.unidad) as contrato_observaciones"),
        DB::raw("SUM(CASE WHEN folios.status in ('Verificando_Pago','Pago_Verificado','Finalizado','Pago_Rechazado')
                THEN 1 END) as pago_total"),
        DB::raw("SUM(CASE WHEN folios.status = 'Verificando_Pago' THEN 1 ELSE 0 END) as pago_proceso"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Verificado' THEN 1 ELSE 0 END) as pago_validados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Finalizado' THEN 1 ELSE 0 END) as pago_finalizados"),
        DB::raw("SUM(CASE WHEN folios.status = 'Pago_Rechazado' THEN 1 ELSE 0 END) as pago_rechazados"),
        DB::raw("ARRAY(SELECT folios.fecha_rechazado FROM tabla_supre
                INNER JOIN folios ON folios.id_supre = tabla_supre.id
                WHERE folios.status = 'Pago_Rechazado'
                AND tbl_unidades.unidad = tabla_supre.unidad_capacitacion) as pago_fecha_rechazo"),
        DB::raw("ARRAY(SELECT pagos.observacion FROM folios
                INNER JOIN contratos ON contratos.id_folios = folios.id_folios
                INNER JOIN pagos ON pagos.id_contrato = contratos.id_contrato
                WHERE folios.status = 'Pago_Rechazado'
                AND contratos.unidad_capacitacion = tbl_unidades.unidad) as pago_observaciones"))
        ->join('tabla_supre','tabla_supre.id', '=', 'folios.id_supre')
        ->join('tbl_unidades','tbl_unidades.unidad', '=', 'tabla_supre.unidad_capacitacion');

        if(isset($fecha_inicio)){
            if(isset($fecha_termino)){
                $consulta1 = $consulta1->where('tabla_supre.fecha','>=',$fecha_inicio)
                                    ->where('tabla_supre.fecha','<=',$fecha_termino);

                $consulta2 = $consulta2->where('tabla_supre.fecha','>=',$fecha_inicio)
                                    ->where('tabla_supre.fecha','<=',$fecha_termino);
            }else{
                return redirect()->route('reporte-solicitados')
                ->withErrors(sprintf('INGRESE UNA FECHA DE INICIO Y TERMINO'));
            }
        }

        $consulta1 = $consulta1->orderBy('tbl_unidades.unidad','asc')->groupBy('tbl_unidades.unidad')->GET();
        $consulta2 = $consulta2->orderBy('tbl_unidades.unidad','asc')->groupBy('tbl_unidades.unidad')->GET();

        $unidades = DB::table('tbl_unidades')->SELECT('unidad')->orderBy('unidad','asc')->GET();
        //dd($unidades);
        return view('layouts.pages.vstareportesolicitados',compact('consulta1','consulta2','fecha_inicio','fecha_termino','unidades'));
    }

    public function cancelFolio(Request $request)
    {
        $userName = Auth::user()->name;
//a
        $folio = folio::find($request->idf);
        $folio->observacion_cancelacion = $request->observaciones;
        $folio->cancelo = $userName;
        $folio->status = 'Cancelado';
        $folio->save();

        $idcontrato = contratos::SELECT('id_contrato')->WHERE('id_folios', '=', $request->idf)->FIRST();
        if($idcontrato != NULL)
            {
                contrato_directorio::WHERE('id_contrato', '=', $idcontrato->id_contrato)->DELETE();
                pago::WHERE('id_contrato', '=', $request->idf)->DELETE();
                contratos::where('id_folios', '=', $request->idf)->DELETE();
            }
        return redirect()->route('supre-inicio')
                    ->with('success','Folio de Suficiencia Presupuestal Cancelada');
    }

    protected function getcursostats(Request $request)
    {
        if (isset($request->valor)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $claveCurso = $request->valor;
            $Curso = new tbl_curso();
            $Cursos = $Curso->SELECT('tbl_cursos.ze','tbl_cursos.cp','tbl_cursos.dura', 'tbl_cursos.inicio', 'tbl_cursos.tipo_curso')
                                    ->WHERE('clave', '=', $claveCurso)->FIRST();

            if($Cursos != NULL)
            {
                $inicio = date("m-d-Y", strtotime($Cursos->inicio));
                $date1 = "2021-05-01";
                $date1 = date("m-d-Y", strtotime($date1));

                if ($date1 <= $inicio)
                {
                    $ze2 = 'ze2_2021 AS monto';
                    $ze3 = 'ze3_2021 AS monto';
                }
                else
                {
                    $ze2 = 'monto_hora_ze2 AS monto';
                    $ze3 = 'monto_hora_ze3 AS monto';
                }

                if ($Cursos->ze == 'II')
                {
                    $criterio = criterio_pago::SELECT($ze2)->WHERE('id', '=' , $Cursos->cp)->FIRST();
                }
                else
                {
                    $criterio = criterio_pago::SELECT($ze3)->WHERE('id', '=' , $Cursos->cp)->FIRST();
                }

                if($criterio != NULL)
                {
                    if($Cursos->tipo_curso == 'CERTIFICACION')
                    {
                        $total = $criterio->monto * 10;
                        //$aviso = TRUE;
                    }
                    else
                    {
                        $total = $criterio->monto * $Cursos->dura;
                    }
                }
                else
                {
                    $total = 'N/A';
                }
            }
            else
            {
                $total = 'N/A';
            }
            $json=json_encode($total);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }


        return $json;
    }

    protected function gettipocurso(Request $request)
    {
        $claveCurso = $request->valor;
        $Curso = new tbl_curso();
        $Cursos = $Curso->SELECT('tbl_cursos.tipo_curso')
                                ->WHERE('clave', '=', $claveCurso)->FIRST();

        if($Cursos != NULL)
        {
            switch ($Cursos->tipo_curso) {
                case 'CERTIFICACION':
                    $tipo = 'CERT';
                break;
                case 'NORMAL':
                    $tipo = 'NORMAL';
                break;
                default:
                    $tipo = 'ERROR';
                break;
            }
        }
        else
        {
            $tipo = 'ERROR';
        }

        $json=json_encode($tipo);
        return $json;
    }


    protected function getfoliostats(Request $request)
    {
        if (isset($request->valor))
        {
            $folio = folio::WHERE('folio_validacion', '=', $request->valor)->FIRST();
            if($folio == NULL)
            {
                $folio = 'N/A';
            }
        }
        else
        {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
            $json=json_encode($folio);


        return $json;
    }

    protected function getfoliostatsmodal(Request $request)
    {
        if (isset($request->valor))
        {
            $folio = folio::SELECT('id_folios', 'folio_validacion')
            ->WHERE('id_supre', '=', $request->valor)
            ->GET();
            if($folio == NULL)
            {
                $folio = 'N/A';
            }
        }
        else
        {
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }
            $json=json_encode($folio);


        return $json;
    }

    public function dar_permiso_folio(Request $request)
    {
        $folio = folio::find($request->folios);
        $folio->permiso_editar = TRUE;
        $folio->save();

        return redirect()->route('supre-inicio')
                    ->with('success','Permiso Otorgado');
    }


    public function doc_valsupre_upload(Request $request)
    {
        if ($request->hasFile('doc_validado')) {

            if($request->idinsmod != NULL)
            {
                $supre = supre::find($request->idinsmod);
                $doc = $request->file('doc_validado'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idinsmod, 'valsupre_firmado'); # invocamos el método
                $supre->doc_validado = $urldoc; # guardamos el path
            }
            else
            {
                $supre = supre::find($request->idinsmod2);
                $doc = $request->file('doc_validado'); # obtenemos el archivo
                $urldoc = $this->pdf_upload($doc, $request->idinsmod2, 'valsupre_firmado'); # invocamos el método
                $supre->doc_validado = $urldoc; # guardamos el path
            }

            $supre->save();
            return redirect()->route('supre-inicio')
                    ->with('success','Validación de Suficiencia Presupuestal Firmada ha sido cargada con Extio');
        }
    }

    public function cancelados_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareportecancelados', compact('unidades'));
    }

    public function planeacion_reporte()
    {
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();

        return view('layouts.pages.vstareporteplaneacion', compact('unidades'));
    }

    public function folio_edicion_especial($id)
    {
        $getdestino = null;
        $getremitente = null;
        $getvalida = null;
        $getelabora = null;
        $getccp1 = null;
        $getccp2 = null;

        $folio = folio::WHERE('id_folios', '=', $id)->FIRST();
        $supre = supre::WHERE('id', '=', $folio->id_supre)->FIRST();
        $clave = tbl_curso::SELECT('clave')->WHERE('id', '=', $folio->id_cursos)->FIRST();

        $directorio = supre_directorio::WHERE('id_supre', '=', $supre->id)->FIRST();
        $unidadsel = tbl_unidades::SELECT('unidad')->WHERE('unidad', '=', $supre->unidad_capacitacion)->FIRST();
        $unidadlist = tbl_unidades::SELECT('unidad')->WHERE('unidad', '!=', $supre->unidad_capacitacion)->GET();

        $getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        $getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        $getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();


        return view('layouts.pages.modespecialfolio',compact('supre','folio','clave','getdestino','getremitente','getvalida','getelabora','getccp1','getccp2','directorio', 'unidadsel','unidadlist'));
    }

    public function folio_edicion_especial_save(Request $request)
    {
        //dd($request->id_folio);
        //dd($request);
        $curso_validado = new tbl_curso();
        $clave = $request->addmore[0]['clavecurso'];
        $hora = $curso_validado->SELECT('tbl_cursos.dura','tbl_cursos.id')
                ->WHERE('tbl_cursos.clave', '=', $clave)
                ->FIRST();
        $importe = $request->addmore[0]['importe']/1.16;
        $importe_hora = $importe / $hora->dura;

        supre::where('id', '=', $request->id_supre)
        ->update(['unidad_capacitacion' => $request->unidad,
                  'no_memo' => $request->no_memo,
                  'fecha' => $request->fecha,
                  'fecha_status' => carbon::now()]);

        supre_directorio::where('id', '=', $request->id_directorio)
        ->update([//'supre_dest' => $request->id_destino,
                  'supre_rem' => $request->id_remitente,
                  'supre_valida' => $request->id_valida,
                  'supre_elabora' => $request->id_elabora,
                  //'supre_ccp1' => $request->id_ccp1,
                  //'supre_ccp2' => $request->id_ccp2,
                ]);

        folio::where('id_folios', '=', $request->id_folio)
        ->update(['folio_validacion' => $request->addmore[0]['folio'],
                  'iva' => $request->addmore[0]['iva'],
                  'comentario' => $request->addmore[0]['comentario'],
                  'importe_hora' => $importe_hora,
                  'importe_total' => $request->addmore[0]['importe'],
                  'id_supre' => $request->id_supre,
                  'id_cursos' => $hora->id,
                  'permiso_editar' => FALSE]);


        return redirect()->route('supre-inicio')
                        ->with('success','Solicitud de Suficiencia Presupuestal agregado');
    }

    public function planeacion_reportepdf(Request $request)
    {

        $filtrotipo = (isset($request->filtro) ? $request->filtro: 0);
        $idcurso = (isset($request->id_curso) ? $request->id_curso : 0);
        $unidad = (empty($request->unidad) ? $request->unidad : 0);
        $idInstructor = (isset($request->id_instructor)? $request->id_instructor : 0);
        $fecha1 = $request->fecha1;
        $fecha2 = $request->fecha2;

        # si el arreglo nos retorna un número mayor a cero
        $unidades = tbl_unidades::SELECT('unidad')->WHERE('id', '!=', '0')->GET();
        return view('layouts.pages.vstareporteplaneacion', compact('unidades', 'filtrotipo','idcurso','unidad','idInstructor','fecha1','fecha2'));

        // dd($data);

        // $pdf = PDF::loadView('layouts.pdfpages.reportesupres', compact('data','recursos','risr','riva','cantidad','iva'));
        // $pdf->setPaper('legal', 'Landscape');
        // return $pdf->Download('formato de control '. $request->fecha1 . ' - '. $request->fecha2 .'.pdf');

        // /**
        //  * Aquí se genera el documento en excel
        //  */
        // $cabecera = [
        //     'SEC. DE SOLIC.', 'MEMO. SOLICITADO', 'NO. DE SUFICIENCIA',
        //     'FECHA', 'INSTRUCTOR', 'UNIDAD/A.M DE CAP.', 'CURSO', 'CLAVE DEL GRUPO',
        //     'Z.E.', 'HSM', 'IVA 16%', 'PARTIDA/CONCEPTO', 'IMPORTE TOTAL FEDERAL',
        //     'IMPORTE TOTAL ESTATAL', 'RETENCIÓN ISR', 'RETENCIÓN IVA', 'MEMO PRESUPUESTA',
        //     'FECHA REGISTRO', 'OBSERVACIONES'
        // ];

        // $nombreLayout = "formato de control".$request->fecha1 . ' - '. $request->fecha2.".xlsx";
        // $titulo = "formato de control ".$request->fecha1 . ' - '. $request->fecha2;
        // if(count($data)>0){
        //     return Excel::download(new FormatoTReport($data,$head, $titulo), $nombreLayout);
        // }

    }

    public function supre_pdf($id){
        $supre = new supre();
        $folio = new folio();
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data_supre = $supre::WHERE('id', '=', $id)->FIRST();
        $uj= supre::SELECT('tabla_supre.fecha','folios.folio_validacion','folios.importe_hora','folios.iva','folios.importe_total',
                        'folios.comentario','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'tbl_cursos.curso AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.tipo_curso')
                    ->WHERE('id_supre', '=', $id )
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();
        $data_folio = $folio::WHERE('id_supre', '=', $id)->WHERE('status', '!=', 'Cancelado')->GET();   //dd($data_supre);
        $date = strtotime($data_supre->fecha);
        $D = date('d', $date);
        $MO = date('m',$date);
        $M = $this->monthToString(date('m',$date));//A
        $Y = date("Y",$date);

        $unidad = tbl_unidades::SELECT('tbl_unidades.unidad', 'tbl_unidades.cct','tbl_unidades.ubicacion')
                                ->WHERE('unidad', '=', $data_supre->unidad_capacitacion)
                                ->FIRST();
        $unidad->cct = substr($unidad->cct, 0, 4);

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        //$getdestino = directorio::WHERE('id', '=', $directorio->supre_dest)->FIRST();
        $getremitente = directorio::SELECT('directorio.nombre','directorio.apellidoPaterno','directorio.apellidoMaterno',
                                    'directorio.puesto','directorio.area_adscripcion_id','area_adscripcion.area')
                                    ->WHERE('directorio.id', '=', $directorio->supre_rem)
                                    ->LEFTJOIN('area_adscripcion', 'area_adscripcion.id', '=', 'directorio.area_adscripcion_id')
                                    ->FIRST();
        $getvalida = directorio::WHERE('id', '=', $directorio->supre_valida)->FIRST();
        $getelabora = directorio::WHERE('id', '=', $directorio->supre_elabora)->FIRST();
        //$getccp1 = directorio::WHERE('id', '=', $directorio->supre_ccp1)->FIRST();
        //$getccp2 = directorio::WHERE('id', '=', $directorio->supre_ccp2)->FIRST();

        $pdf = PDF::loadView('layouts.pdfpages.presupuestaria',compact('data_supre','data_folio','D','M','Y','getremitente','getvalida','getelabora','directorio','unidad','distintivo','uj'));
        return  $pdf->stream('medium.pdf');
    }

    protected function planeacion_reporte_canceladospdf(Request $request){
        $i = 0;
        set_time_limit(0);

        if ($request->filtro == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario','folios.cancelo',
                           'folios.observacion_cancelacion','folios.updated_at','instructores.nombre',
                           'instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.curso',
                           'tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                           ->WHERE('folios.status', '=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($request->filtro == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario','folios.cancelo',
                           'folios.observacion_cancelacion','instructores.nombre','instructores.apellidoPaterno',
                           'instructores.apellidoMaterno','tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze',
                           'tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $request->fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $request->fecha2)
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $request->unidad)
                           ->WHERE('folios.status', '=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }

        $pdf = PDF::loadView('layouts.pdfpages.reportefolioscancelados', compact('data'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $request->fecha1 . ' - '. $request->fecha2 .'.pdf');
    }

    public function tablasupre_pdf($id){
        $supre = new supre;
        $curso = new tbl_curso;
        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $data = supre::SELECT('tabla_supre.fecha','folios.folio_validacion','folios.importe_hora','folios.iva','folios.importe_total',
                        'folios.comentario','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'tbl_cursos.curso AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.tipo_curso')
                    ->WHERE('id_supre', '=', $id )
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();
        $data2 = supre::WHERE('id', '=', $id)->FIRST();

        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getremitente = directorio::SELECT('directorio.nombre','directorio.apellidoPaterno','directorio.apellidoMaterno',
                                    'directorio.puesto','directorio.area_adscripcion_id','area_adscripcion.area')
                                    ->WHERE('directorio.id', '=', $directorio->supre_rem)
                                    ->LEFTJOIN('area_adscripcion', 'area_adscripcion.id', '=', 'directorio.area_adscripcion_id')
                                    ->FIRST();

        $date = strtotime($data2->fecha);
        $D = date('d', $date);
        $M = $this->monthToString(date('m',$date));
        $Y = date("Y",$date);

        $datev = strtotime($data2->fecha_validacion);
        $Dv = date('d', $datev);
        $Mv = $this->monthToString(date('m',$datev));
        $Yv = date("Y",$datev);

        $pdf = PDF::loadView('layouts.pdfpages.solicitudsuficiencia', compact('data','data2','D','M','Y','Dv','Mv','Yv','getremitente','distintivo'));
        $pdf->setPaper('A4', 'Landscape');

        return $pdf->stream('download.pdf');

        return view('layouts.pdfpages.solicitudsuficiencia', compact('data','data2'));
    }

    public function valsupre_pdf($id){
        $supre = new supre;
        $curso = new tbl_curso;
        $recursos = array();
        $i = 0;
        $data = supre::SELECT('tabla_supre.fecha','folios.folio_validacion','folios.importe_hora','folios.iva','folios.importe_total',
                        'folios.comentario','instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno','tbl_cursos.unidad',
                        'cursos.nombre_curso AS curso_nombre','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre','tbl_cursos.mujer','tbl_cursos.tipo_curso')
                    ->WHERE('id_supre', '=', $id )
                    ->WHERE('folios.status', '!=', 'Cancelado')
                    ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                    ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                    ->LEFTJOIN('cursos','cursos.id','=','tbl_cursos.id_curso')
                    ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                    ->GET();
        $data2 = supre::WHERE('id', '=', $id)->FIRST(); //dd($data[0]->tipo_curso);

        $cadwell = folio::SELECT('id_cursos')->WHERE('id_supre', '=', $id)
            ->WHERE('folios.status', '!=', 'Cancelado')
            ->GET();
        foreach ($cadwell as $item)
        {
            $h = tbl_curso::SELECT('hombre')->WHERE('id', '=', $item->id_cursos)->FIRST();
            $m = tbl_curso::SELECT('mujer')->WHERE('id', '=', $item->id_cursos)->FIRST();
            $hm = $h->hombre+$m->mujer;
            //printf($item->id_cursos  . $h . ' + ' . $m . '=' . $hm . ' // ');
            if ($hm < 10)
            {
                $recursos[$i] = "Estatal";
            }
            else
            {
                $recursos[$i] = "Federal";
            }
            $i++;
        }

       // dd($recursos);


        $date = strtotime($data2->fecha);
        $D = date('d', $date);
        $M = $this->monthToString(date('m',$date));
        $Y = date("Y",$date);

        $datev = strtotime($data2->fecha_validacion);
        $Dv = date('d', $datev);
        $Mv = $this->monthToString(date('m',$datev));
        $Yv = date("Y",$datev);

        $distintivo = DB::table('tbl_instituto')->pluck('distintivo')->first();
        $directorio = supre_directorio::WHERE('id_supre', '=', $id)->FIRST();
        $getremitente = directorio::WHERE('id', '=', $directorio->supre_rem)->FIRST();
        $getfirmante = directorio::WHERE('id', '=', $directorio->val_firmante)->FIRST();
        //$getccp1 = directorio::WHERE('id', '=', $directorio->val_ccp1)->FIRST();
        //$getccp2 = directorio::WHERE('id', '=', $directorio->val_ccp2)->FIRST();
        //$getccp3 = directorio::WHERE('id', '=', $directorio->val_ccp3)->FIRST();
        $getccp4 = directorio::WHERE('id', '=', $directorio->val_ccp4)->FIRST();

        $pdf = PDF::loadView('layouts.pdfpages.valsupre', compact('data','data2','D','M','Y','Dv','Mv','Yv','getremitente','getfirmante','getccp4','recursos','distintivo'));
        $pdf->setPaper('A4', 'Landscape');
        return $pdf->stream('medium.pdf');

        return view('layouts.pdfpages.valsupre', compact('data','data2','D','M','Y','Dv','Mv','Yv','getremitente','getfirmante','getccp1','getccp2','getccp3','getccp4','recursos'));
    }

    protected function monthToString($month)
    {
        switch ($month)
        {
            case 1:
                return 'ENERO';
            break;

            case 2:
                return 'FEBRERO';
            break;

            case 3:
                return 'MARZO';
            break;

            case 4:
                return 'ABRIL';
            break;

            case 5:
                return 'MAYO';
            break;

            case 6:
                return 'JUNIO';
            break;

            case 7:
                return 'JULIO';
            break;

            case 8:
                return 'AGOSTO';
            break;

            case 9:
                return 'SEPTIEMBRE';
            break;

            case 10:
                return 'OCTUBRE';
            break;

            case 11:
                return 'NOVIEMBRE';
            break;

            case 12:
                return 'DICIEMBRE';
            break;
        }
    }

    protected function pdf_upload($pdf, $id, $nom)
    {
        # nuevo nombre del archivo
        $pdfFile = trim($nom."_".date('YmdHis')."_".$id.".pdf");
        $pdf->storeAs('/uploadFiles/supre/'.$id, $pdfFile); // guardamos el archivo en la carpeta storage
        $pdfUrl = Storage::url('/uploadFiles/supre/'.$id."/".$pdfFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $pdfUrl;
    }

    public function get_curso(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $curso = tbl_curso::orderby('curso','asc')->select('id','curso')->limit(5)->get();
            }else{
                $curso = tbl_curso::orderby('curso','asc')->select('id','curso')->where('curso', 'like', '%' .$search . '%')->limit(5)->get();
            }

            $response = array();
            foreach($curso as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->curso);
            }

            echo json_encode($response);
            exit;
        }
    }

    public function get_ins(Request $request){

        $search = $request->search;

        if (isset($search)) {
            # si la variable está inicializada
            if($search == ''){
                $instructor = instructor::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno')->limit(10)->get();
            }else{
                $instructor = instructor::orderby('nombre','asc')->select('id','nombre','apellidoPaterno','apellidoMaterno')->where('nombre', 'like', '%' .$search . '%')->limit(10)->get();
            }

            $response = array();
            foreach($instructor as $dir){
                $response[] = array("value"=>$dir->id,"label"=>$dir->nombre . " " .$dir->apellidoPaterno . " " . $dir->apellidoMaterno);
            }

            echo json_encode($response);
            exit;
        }
    }

    protected function numberFormat($numero)
    {
        $part = explode(".", $numero);
        $part[0] = number_format($part['0']);
        $cadwell = implode(".", $part);
        return ($cadwell);
    }

    /**
     * agregar métodos - generate_report_supre_pdf -
     */
    protected function generate_report_supre_pdf($filtrotipo, $idcurso, $unidad, $idInstructor, $fecha1, $fecha2){
        $i = 0;
        set_time_limit(0);

        if ($filtrotipo == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'curso')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->WHERE('tbl_cursos.id', '=', $idcurso)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'instructor')
        {
            $data = supre::SELECT('tabla_supre.no_memo','tabla_supre.fecha','tabla_supre.unidad_capacitacion',
                           'tabla_supre.folio_validacion','tabla_supre.fecha_validacion','folios.folio_validacion as suf',
                           'folios.importe_hora','folios.iva','folios.importe_total','folios.comentario',
                           'instructores.nombre','instructores.apellidoPaterno','instructores.apellidoMaterno',
                           'tbl_cursos.curso','tbl_cursos.clave','tbl_cursos.ze','tbl_cursos.dura','tbl_cursos.hombre',
                           'tbl_cursos.mujer')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('instructores.id', '=', $idInstructor)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }


        foreach($data as $cadwell)
        {
            $risr[$i] = $this->numberFormat( floatval(round($cadwell->importe_total * 0.10, 2)));
            $riva[$i] = $this->numberFormat( floatval(round($cadwell->importe_total * 0.1066, 2)));

            $iva[$i] = $this->numberFormat( floatval($cadwell->iva));
            $cantidad[$i] = $this->numberFormat( floatval($cadwell->importe_total));

            $hm = $cadwell->hombre+$cadwell->mujer;
            if ($hm < 10)
            {
                $recursos[$i] = "Estatal";
            }
            else
            {
                $recursos[$i] = "Federal";
            }
            $i++;
        }

        $pdf = PDF::loadView('layouts.pdfpages.reportesupres', compact('data','recursos','risr','riva','cantidad','iva'));
        $pdf->setPaper('legal', 'Landscape');
        return $pdf->Download('formato de control '. $fecha1 . ' - '. $fecha2 .'.pdf');
    }

    /**
     *
     */
    protected function generate_report_supre_xls($filtrotipo, $idcurso, $unidad, $idInstructor, $fecha1, $fecha2){
        $i = 0;
        set_time_limit(0);

        if ($filtrotipo == "general")
        {
            $data = supre::SELECT('tabla_supre.no_memo',
                    'folios.folio_validacion as suf',
                    'tabla_supre.fecha',
                    \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tabla_supre.unidad_capacitacion',
                    'tbl_cursos.curso',
                    'tbl_cursos.clave',
                    'tbl_cursos.ze',
                    'tbl_cursos.dura',
                    \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99') AS importe_hora"),
                    \DB::raw("TO_CHAR(folios.iva, '999,999.99') AS importe_iva_16"),
                    \DB::raw("'12101 Honorarios' AS partida_concepto"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_federal"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END AS importe_estatal"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2) AS retencion_isr"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2) AS retencion_iva"),
                    'tabla_supre.folio_validacion AS memo_validacion',
                    'tabla_supre.fecha_validacion AS fecha_registro',
                    'folios.comentario AS observaciones')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'curso')
        {
            $data = supre::SELECT('tabla_supre.no_memo',
                    'folios.folio_validacion as suf',
                    'tabla_supre.fecha',
                    \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tabla_supre.unidad_capacitacion',
                    'tbl_cursos.curso', 'tbl_cursos.clave',
                    'tbl_cursos.ze', 'tbl_cursos.dura',
                    \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2)"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2)"),
                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('tbl_cursos.id', '=', $idcurso)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'unidad')
        {
            $data = supre::SELECT('tabla_supre.no_memo', 'folios.folio_validacion as suf',
                    'tabla_supre.fecha', \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tabla_supre.unidad_capacitacion', 'tbl_cursos.curso', 'tbl_cursos.clave',
                    'tbl_cursos.ze', 'tbl_cursos.dura', \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') END"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2)"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2)"),
                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('tabla_supre.unidad_capacitacion', '=', $unidad)
                           ->WHERE('folios.status', '!=', 'Cancelado')
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }
        else if ($filtrotipo == 'instructor')
        {
            $data = supre::SELECT('tabla_supre.no_memo', 'folios.folio_validacion as suf',
                    'tabla_supre.fecha', \DB::raw('CONCAT(instructores.nombre, '."' '".' ,instructores."apellidoPaterno",'."' '".',instructores."apellidoMaterno")'),
                    'tabla_supre.unidad_capacitacion', 'tbl_cursos.curso', 'tbl_cursos.clave',
                    'tbl_cursos.ze', 'tbl_cursos.dura', \DB::raw("TO_CHAR(folios.importe_hora, '999,999.99')"),
                    \DB::raw("TO_CHAR(folios.iva, '999,999.99')"),
                    \DB::raw("'12101 Honorarios'"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) >= 10 THEN TO_CHAR(folios.importe_total, '999,999.99') ELSE 0 END"),
                    \DB::raw("CASE WHEN (tbl_cursos.hombre + tbl_cursos.mujer) < 10 THEN TO_CHAR(folios.importe_total, '999,999.99') ELSE 0 END"),
                    \DB::raw("ROUND(folios.importe_total * 0.10, 2)"),
                    \DB::raw("ROUND(folios.importe_total * 0.1066, 2)"),
                    'tabla_supre.folio_validacion',
                    'tabla_supre.fecha_validacion',
                    'folios.comentario')
                           ->whereDate('tabla_supre.fecha', '>=', $fecha1)
                           ->whereDate('tabla_supre.fecha', '<=', $fecha2)
                           ->WHERE('instructores.id', '=', $idInstructor)
                           ->LEFTJOIN('folios', 'folios.id_supre', '=', 'tabla_supre.id')
                           ->LEFTJOIN('tbl_cursos', 'tbl_cursos.id', '=', 'folios.id_cursos')
                           ->LEFTJOIN('instructores', 'instructores.id', '=', 'tbl_cursos.id_instructor')
                           ->GET();
        }

        $cabecera = [
            'MEMO. SOLICITADO', 'NO. DE SUFICIENCIA', 'FECHA',
            'INSTRUCTOR', 'UNIDAD/A.M DE CAP.', 'CURSO', 'CLAVE DEL GRUPO',
            'Z.E.', 'HSM', 'IMPORTE POR HORA', 'IVA 16%', 'PARTIDA/CONCEPTO', 'IMPORTE TOTAL FEDERAL',
            'IMPORTE TOTAL ESTATAL', 'RETENCIÓN ISR', 'RETENCIÓN IVA', 'MEMO PRESUPUESTA',
            'FECHA REGISTRO', 'OBSERVACIONES'
        ];

        $nombreLayout = "formato de control".$fecha1 . ' - '. $fecha2.".xlsx";
        $titulo = "formato de control ".$fecha1 . ' - '. $fecha2;
        if(count($data)>0){
            return Excel::download(new FormatoTReport($data,$cabecera, $titulo), $nombreLayout);
        }
    }
}
