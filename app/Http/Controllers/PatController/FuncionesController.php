<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\Funciones;
use App\Models\ModelPat\Organismos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;
use App\Models\ModelPat\Procedimientos;

class FuncionesController extends Controller
{

    public $globalOrganismo;

    public function __construct()
    {
        // Aquí puedes inicializar otras propiedades del controlador si es necesario
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $idorg = null)
    {
        //Obtenemos los id org, area del usuario quien ingresa
        try {
            if ($idorg != null) {
                // $organismo = $idorg;
                $organismo = $idorg;
            }else if($request->get('id_orgbus') != ''){
                $organismo = $request->get('id_orgbus');
            }
            else{
                $json_org = Auth::user()->id_organismos_json;
                $array_org = json_decode($json_org, true);
                $organismo = $array_org[0];
            }
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }
        //Obtenermos la variable desde el contructor
        // $organismo = $this->globalOrganismo;

        $list_org = Organismos::select('id', 'nombre')
        ->where('activo', '=', 'true')
        ->orderBy('id', 'asc')->get();

        //Obtenemos el area del usuario
        $area_org = DB::table('tbl_organismos')->select('id', 'nombre', 'id_parent')
        ->where('id', $organismo)->first();

        // Obtenermos el organismo del usuario
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->where('o.id', $area_org->id_parent)->first();

        //Consulta de funciones cuando id_parent = 0
        $data = Funciones::Busqueda($request->get('busqueda_funcion'))
            ->select('funciones_proced.*')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            // ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
            ->orderByDesc('funciones_proced.id')
            ->paginate(20, ['funciones_proced.*']);

        return view('vistas_pat.funciones_pat', compact('data','area_org', 'org', 'list_org', 'organismo'));
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
     * Crea nuevas funciones para el PAT
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id_organismo = $request->input('id_org');
        $id_user = Auth::user()->id;

        $funciones = new Funciones;
        $funciones['id_parent'] = 0;
        $funciones['id_org'] = $id_organismo;
        $funciones['fun_proc'] =  trim($request->input('nom_funcion'));
        $funciones['activo'] = 'true';
        $funciones['created_at'] = date('Y-m-d');
        $funciones['iduser_created'] = $id_user;
        $funciones['iduser_updated'] = $id_user;
        $funciones->save();

        // return redirect()->route('pat.funciones.mostrar')->with('success', '¡Registro guardado exitosamente!');
        return redirect()->route('pat.funciones.mostrar', ['idorg' => $id_organismo])->with('success', '¡Registro guardado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $idorg)
    {
        $organismo =  $idorg;

        $list_org = Organismos::select('id', 'nombre')
        ->where('activo', '=', 'true')
        ->orderBy('id', 'asc')->get();


        //Obtenemos el area del usuario
        $area_org = DB::table('tbl_organismos')->select('id', 'nombre', 'id_parent')
        ->where('id', $organismo)->first();
        //$orgj= json_decode( json_encode($org), true);

        // Obtenermos el organismo del usuario
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->where('o.id', $area_org->id_parent)->first();
        //$areaj= json_decode( json_encode($area_org->id_parent), true);


        $data = Funciones::Busqueda('')
        ->select('funciones_proced.*')
        ->where('id_parent', '=', 0)
        ->where('id_org', '=', $organismo)
        ->where(DB::raw("date_part('year' , created_at )"), '=', '2023')
        ->orderByDesc('funciones_proced.id')
        ->paginate(15, ['funciones_proced.*']);

        $funcion_desc = Funciones::WHERE('id', '=', $id)->FIRST();

        return view('vistas_pat.funciones_pat', compact('data','area_org', 'org', 'funcion_desc', 'list_org', 'organismo'));

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
        //Obtener el id del user
        try {
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }
        $organismo = $request->input('idorgupd');
        $id_user = Auth::user()->id;


        $funciones = Funciones::find($id);
        $funciones->fun_proc =  trim($request->input('nom_funcion_edit'));
        $funciones->updated_at = date('Y-m-d');
        $funciones->iduser_updated = $id_user;
        $funciones->save();

        return redirect()->route('pat.funciones.mostrar', ['idorg' => $organismo])->with('success', '¡Registro actualizado exitosamente!');
    }

    public function status(Request $request)
    {
        $id = (int)$request->id;
        $funciones = Funciones::find($id);
        $funciones->activo = $request->status;
        $funciones->iduser_updated = Auth::user()->id;
        $funciones->updated_at = date('Y-m-d');
        $funciones->save();
        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'status' => $request->status,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Hacer como un ciclo para que vaya eliminando los procedimientos
        // $procedimientos = Procedimientos::select('id')
        // ->where('id_parent', '=', $id)->get();

        //Eliminamos los proced viculados
        // for ($i=0; $i < count($procedimientos); $i++) {
        //     Procedimientos::destroy($procedimientos[$i]['id']);
        // }

        // Funciones::destroy($id);
        // return redirect()->route('pat.funciones.mostrar')->with('success', '¡Registro eliminado exitosamente!');

    }
}
