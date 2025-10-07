<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ModelPat\FechasPat;
use App\Models\ModelPat\Organismos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FechasController extends Controller
{
    public function __construct()
    {
        session_start();
    }
    /**
     * Envia la informacion de la tabla fechas_pat a la vista
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tipo = null)
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        //Ejercicio
        $sel_eje = $request->sel_ejercicio;
        $ejercicio = [];
        for ($i=2023; $i <= intval(date('Y')); $i++) {array_push($ejercicio, $i);}
        if ($sel_eje == null && !session()->has('eje_pat_fechas')) {
            session(['eje_pat_fechas' => date('Y')]);
        } elseif ($sel_eje != null) {
            session(['eje_pat_fechas' => $sel_eje]);
        }
        $anio = session('eje_pat_fechas');

        $mes_avance_get = $tipo;

        $data = FechasPat::select('fechas_pat.*', 'o.nombre', 'o.id_parent')
        ->Join('tbl_organismos as o', 'o.id', 'fechas_pat.id_org')
        ->where('periodo', '=', $anio)
        ->orderBy('fechas_pat.id', 'asc')
        ->paginate(18, ['fechas_pat.*']);

        ##Cosulta para deshacer validación
        $dptos_activos = DB::table('tbl_organismos as o')->select('o.id', 'o.nombre as nom_dpto')
        ->where('o.activo', '=', 'true')
        ->orderBy('o.id', 'asc')
        ->get();


        return view('vistas_pat.fechas_pat', compact('data', 'mes_avance_get', 'ejercicio', 'anio', 'dptos_activos', 'meses'));
    }

    /**
     * Realiza el guardado de fechas afectando a todos los organismos.
     */
    public function guardar(Request $request)
    {
        try {
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }
        //FECHAS FORMATEADAS

         $fechconvEmision = Carbon::parse($request->fecha1);
         $fechaconvLimite = Carbon::parse($request->fecha2);
         $fechaEmi = $fechconvEmision->format('d-m-Y');
         $fechaLim = $fechaconvLimite->format('d-m-Y');

         //Hacemos una consulta para hacer un update despues de todos los registros
         $registros = FechasPat::select('id', 'status_meta', 'status_avance')
         ->where('periodo', '=', session('eje_pat_fechas'))->get();
        //  dd($registros[0]->status_meta['proceso']);

        //SELECT META
        if ($request->select_opcion == 'meta') {
            for ($i=0; $i < count($registros); $i++) {
                $fechas = FechasPat::find($registros[$i]['id']);
                //Actualizamos fechas
                $valfechas = $fechas->fecha_meta;
                $valfechas['fechaemi'] = $fechaEmi;
                $valfechas['fechalimit'] = $fechaLim;
                $fechas->fecha_meta = $valfechas;
                // $fechas->fecha_meta = ['fechaemi'=> $fechaEmi, 'fechalimit'=> $fechaLim];


                $fechas->updated_at = date('Y-m-d');
                $fechas->iduser_updated = $id_user;
                //Actualizamos status para que ya se pueda ir registrando las metas
                if ($registros[$i]->status_meta['proceso'] == '1') {
                    $statusmeta = $fechas->status_meta; $statusmeta['proceso'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
                }else if($registros[$i]->status_meta['retornado'] == '1'){
                    $statusmeta = $fechas->status_meta; $statusmeta['retornado'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
                }else if($registros[$i]->status_meta['validado'] == '1'){
                    $statusmeta = $fechas->status_meta; $statusmeta['validado'] = '1'; $statusmeta['statusmeta'] = 'inactivo'; $fechas->status_meta = $statusmeta;
                }else{
                    $statusmeta = $fechas->status_meta; $statusmeta['captura'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
                }

                $fechas->save();
            }
        }

        //SELECT AVANCE
        if ($request->select_opcion == 'avance') {

            for ($i=0; $i < count($registros); $i++) {
                $fechas = FechasPat::find($registros[$i]['id']);

                //Validamos que si el el mes ya tiene una fecha y esta validada ya no se realiza cambios.
                if ($fechas->fechas_avance[$request->opciones_mes]['statusmes'] != 'autorizado') {
                    /** Agregamos fechas al mes */
                    $mes = $fechas->fechas_avance;
                    $mes[$request->opciones_mes]['fechafin'] = $fechaLim; $mes[$request->opciones_mes]['fechaemision'] = $fechaEmi;
                    $fechas->fechas_avance = $mes;

                    /** Actualizamos status para que ya se pueda ir registrando las metas */
                    if ($fechas->status_avance['proceso'] == '1') {
                        $statusava = $fechas->status_avance; $statusava['proceso'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                    }else if($fechas->status_avance['retornado'] == '1'){
                        $statusava = $fechas->status_avance; $statusava['retornado'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                    }else if($fechas->status_avance['autorizado'] == '1'){
                        $statusava = $fechas->status_avance; $statusava['autorizado'] = '1'; $statusava['statusavance'] = 'inactivo'; $fechas->status_avance = $statusava;
                    }else{
                        $statusava = $fechas->status_avance; $statusava['captura'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                    }
                    $fechas->updated_at = date('Y-m-d');
                    $fechas->iduser_updated = $id_user;
                }

                $fechas->save();



            }
        }
        return redirect()->route('pat.fechaspat.mostrar')->with('success', '¡Registro guardado exitosamente!');
    }

    /**
     * Consultamos fechas para poder modificarlas
     */
    public function consulfech(Request $request)
    {
        //CONTULTA A LA BD DE FECHAS
        $registros = FechasPat::select('id', 'fecha_meta', 'fechas_avance')
         ->where('id', '=', $request->id)
         ->where('periodo', '=', session('eje_pat_fechas'))->get();

        if ($request->tipo == 'meta') {
            //Modificar formato de fechas para enviarlos a la vista
            $fechconvEmision = Carbon::parse($registros[0]['fecha_meta']['fechaemi']);
            $fechaconvLimite = Carbon::parse($registros[0]['fecha_meta']['fechalimit']);
            $fechaEmi = $fechconvEmision->format('Y-m-d');
            $fechaLim = $fechaconvLimite->format('Y-m-d');
        }

        if ($request->tipo == 'avance') {
            //Modificar formato de fechas para enviarlos a la vista
            $fechconvEmision = Carbon::parse($registros[0]['fechas_avance'][$request->mes]['fechaemision']);
            $fechaconvLimite = Carbon::parse($registros[0]['fechas_avance'][$request->mes]['fechafin']);
            $fechaEmi = $fechconvEmision->format('Y-m-d');
            $fechaLim = $fechaconvLimite->format('Y-m-d');
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'fecha_emi' =>  $fechaEmi,
            'fecha_limit' =>  $fechaLim,
            'id' => $request->id,
            'tipo_reg' => $request->tipo,
            'mes' => $request->mes
        ]);



    }

    public function guardarfech(Request $request)
    {
        //el error esta en este codigo ya que asi comentado si ingresa al metodo
        //checar por favor gracias, ah y si se obtiene todas las variables sin problemas

        //Convertimos las fechas en otro formato
        $fechconvEmision = Carbon::parse($request->fechaEmi);
        $fechaconvLimite = Carbon::parse($request->fechaLimit);
        $fechaEmi = $fechconvEmision->format('d-m-Y');
        $fechaLim = $fechaconvLimite->format('d-m-Y');

        //Obtenemos los status para checar
        $status_fech_pat = FechasPat::select('status_meta', 'status_avance')->where('id', '=', $request->id)->first();

        if ($request->tipo == 'meta') {
            $fechas = FechasPat::find($request->id);
            //Actualizamos fechas
            $valfechas = $fechas->fecha_meta;
            $valfechas['fechaemi'] = $fechaEmi;
            $valfechas['fechalimit'] = $fechaLim;
            $fechas->fecha_meta = $valfechas;

            // $fechas->fecha_meta = ['fechaemi'=> $fechaEmi, 'fechalimit'=> $fechaLim];

            $fechas->updated_at = date('d-m-Y');
            $fechas->iduser_updated = Auth::user()->id;
            //Agregamos estos valores en status meta
            if ($fechas->status_meta['proceso'] == '1') {
                $statusmeta = $fechas->status_meta; $statusmeta['proceso'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
            }else if($fechas->status_meta['retornado'] == '1'){
                $statusmeta = $fechas->status_meta; $statusmeta['retornado'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
            }else if($fechas->status_meta['validado'] == '1'){
                $statusmeta = $fechas->status_meta; $statusmeta['validado'] = '1'; $statusmeta['statusmeta'] = 'inactivo'; $fechas->status_meta = $statusmeta;
            }else{
                $statusmeta = $fechas->status_meta; $statusmeta['captura'] = '1'; $statusmeta['statusmeta'] = 'activo'; $fechas->status_meta = $statusmeta;
            }
            $fechas->save();
        }

        if ($request->tipo == 'avance') {
            $fechas = FechasPat::find($request->id);

            if ($fechas->fechas_avance[$request->mes]['statusmes'] != 'autorizado') {
                $mes = $fechas->fechas_avance;
                $mes[$request->mes]['fechafin'] = $fechaLim;
                $mes[$request->mes]['fechaemision'] = $fechaEmi;
                $fechas->fechas_avance = $mes;

                //Agregamos estos valores en status avance
                if ($fechas->status_avance['proceso'] == '1') {
                    $statusava = $fechas->status_avance; $statusava['proceso'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                }else if($fechas->status_avance['retornado'] == '1'){
                    $statusava = $fechas->status_avance; $statusava['retornado'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                }else if($fechas->status_avance['autorizado'] == '1'){
                    $statusava = $fechas->status_avance; $statusava['autorizado'] = '1'; $statusava['statusavance'] = 'inactivo'; $fechas->status_avance = $statusava;
                }else{
                    $statusava = $fechas->status_avance; $statusava['captura'] = '1'; $statusava['statusavance'] = 'activo'; $fechas->status_avance = $statusava;
                }

                $fechas->updated_at = date('d-m-Y');
                $fechas->iduser_updated = Auth::user()->id;
            }

            $fechas->save();
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'datos' => $status_fech_pat
        ]);
    }

    ##DESHACER VALIDACIÓN
    public function return_valid(Request $request)
    {
        $ejercicio = $request->ejercicio;
        $departamento = $request->departamento;
        $fechaini = date("d-m-Y", strtotime($request->fechaini));
        $fechafin =  date("d-m-Y", strtotime($request->fechafin));
        $mes = $request->mes;
        $asunto = $request->asunto;

        $consulta = FechasPat::select('id', 'id_org', 'status_meta', 'fecha_meta', 'fechas_avance', 'status_avance')->where('id_org',$departamento)
        ->where('periodo',$ejercicio)->where('status','activo')->first();

        if ($consulta == null) {return response()->json(['status' => 500, 'mensaje' => 'Error en la consulta del organismo']);}

        ### Eliminar documento
        $eliminar_doc = function ($id, $nomdoc){
            $filePath = 'uploadFiles/pat/'.$id.'/'.$nomdoc;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        };

        ##Buscamos si hay algun avance o meta pendiente para no causar conflictos
        $avance_s = $consulta->status_avance['statusavance'];
        $meta_s = $consulta->status_meta['statusmeta'];

        if($meta_s == 'activo' || $avance_s == 'activo'){
            return response()->json([
                'status' => 100,
                'mensaje' => "ESTE ORGANISMO TIENE UNA TAREA ACTIVA EN ESTE MOMENTO,\nPOR LO TANTO NO PUEDE DESHACER LA VALIDACIÓN DEL ORGANISMO SELECCIONADO\n\nFAVOR DE COMUNICARSE CON EL GRUPO DE SOPORTE"
            ]);
        }

        ###Actualizar columna
        if($asunto == 'meta'){
            try {
                $eliminar_doc($consulta->id, $consulta->fecha_meta['nomdoc_firm']);
                $meta = FechasPat::find($consulta->id);
                $url = $meta->fecha_meta;
                $url2 = $meta->status_meta;
                $url['fechaemi'] = $fechaini;
                $url['fechalimit'] = $fechafin;
                $url['fecmetapdf'] = "";
                $url['fecmetasave'] = "";
                $url['fecmetvalid'] = "";
                $url['nomdoc_firm'] = "";
                $url['urldoc_firm'] = "";
                $url['fecmetretorno'] = "";
                $url['fecenvioplane_m'] = "";
                $meta->fecha_meta = $url;
                $url2['captura'] = "1";
                $url2['proceso'] = "0";
                $url2['validado'] = "0";
                $url2['retornado'] = "0";
                $url2['statusmeta'] = "activo";
                $meta->status_meta = $url2;
                $meta->updated_at = date("Y-m-d");
                $meta->iduser_updated = Auth::user()->id;
                $meta->save();
                $mensaje = "SE HA HABILITADO NUEVAMENTE LA META ANUAL DEL ORGANISMO";
                return response()->json(['status' => 200,'mensaje' => $mensaje]);
            } catch (\Throwable $th) {
                return response()->json(['status' => 500,'mensaje' => $th->getMessage()]);
            }

        }else if($asunto == 'avance'){
            try {
                $eliminar_doc($consulta->id, $consulta->fechas_avance[$mes]['nomdoc_firmav']);
                $avance = FechasPat::find($consulta->id);
                $url = $avance->fechas_avance;
                $url2 = $avance->status_avance;
                $url[$mes]['fechafin'] = $fechafin;
                $url[$mes]['fechasave'] = "";
                $url[$mes]['statusmes'] = "";
                $url[$mes]['fecavanpdf'] = "";
                $url[$mes]['fecavanvalid'] = "";
                $url[$mes]['fechaemision'] = $fechaini;
                $url[$mes]['fecavanreturn'] = "";
                $url[$mes]['nomdoc_firmav'] = "";
                $url[$mes]['urldoc_firmav'] = "";
                $url[$mes]['fecenvioplane_a'] = "";
                $avance->fechas_avance = $url;
                $url2['captura'] = "1";
                $url2['proceso'] = "0";
                $url2['autorizado'] = "0";
                $url2['retornado'] = "0";
                $url2['statusavance'] = "activo";
                $avance->status_avance = $url2;
                $avance->updated_at = date("Y-m-d");
                $avance->iduser_updated = Auth::user()->id;
                $avance->save();
                $mensaje = "SE HA HABILITADO NUEVAMENTE EL AVANCE MENSUAL DE ".strtoupper($mes)." DEL ORGANISMO";
                return response()->json(['status' => 200,'mensaje' => $mensaje]);
            } catch (\Throwable $th) {
                return response()->json(['status' => 500,'mensaje' => $th->getMessage()]);
            }
        }else{
            return response()->json(['status' => 200,'mensaje' => "VERIFICA LOS CAMPOS"]);
        }
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
