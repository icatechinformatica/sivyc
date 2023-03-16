<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\Funciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper;
use App\Models\ModelPat\Procedimientos;

class FuncionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Obtenemos los id org, area del usuario quien ingresa
        try {
            $organismo = Auth::user()->id_organismo;
            $area = Auth::user()->id_orgarea;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        //Obtenemos el organismo del usuario
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->Join('users as u', 'u.id_organismo', 'o.id')
        ->where('u.id_organismo', $organismo)->first();
        //$orgj= json_decode( json_encode($org), true);


        // Obtenermos el area del usuario
        $area_org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->Join('users as u', 'u.id_orgarea', 'o.id')
        ->where('u.id_orgarea', $area)->first();
        //$areaj= json_decode( json_encode($area_org), true);


        $data = Funciones::Busqueda($request->get('busqueda_funcion'))
            ->select('funciones_proced.*')
            ->where('id_parent', '=', 0)
            ->where('id_org', '=', $organismo)
            ->where('id_area', '=', $area)
            ->orderByDesc('funciones_proced.id')
            ->paginate(15, ['funciones_proced.*']);

        return view('vistas_pat.funciones_pat', compact('data','area_org', 'org'));
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
        //Obtener el id del user
        try {
            $id_organismo = Auth::user()->id_organismo;
            $id_area = Auth::user()->id_orgarea;
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        $funciones = new Funciones;
        $funciones['id_parent'] = 0;
        $funciones['id_org'] = $id_organismo;
        $funciones['fun_proc'] =  trim($request->input('nom_funcion'));
        $funciones['activo'] = 'true';
        $funciones['created_at'] = date('Y-m-d');
        $funciones['iduser_created'] = $id_user;
        $funciones['iduser_updated'] = $id_user;
        $funciones['id_area'] = $id_area;
        $funciones->save();

        return redirect()->route('pat.funciones.mostrar')->with('success', '¡Registro guardado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Obtenemos los id org, area del usuario quien ingresa
        try {
            $organismo = Auth::user()->id_organismo;
            $area = Auth::user()->id_orgarea;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

         //Obtenemos el organismo del usuario
         $org = DB::table('tbl_organismos as o')->select('o.id' ,'nombre')
         ->Join('users as u', 'u.id_organismo', 'o.id')
         ->where('u.id_organismo', $organismo)->first();
         //$orgj= json_decode( json_encode($org), true);


         // Obtenermos el area del usuario
         $area_org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
         ->Join('users as u', 'u.id_orgarea', 'o.id')
         ->where('u.id_orgarea', $area)->first();
         //$areaj= json_decode( json_encode($area_org), true);

        $data = Funciones::Busqueda('')
        ->select('funciones_proced.*')
        ->where('id_parent', '=', 0)
        ->where('id_org', '=', $organismo)
        ->where('id_area', '=', $area)
        ->orderByDesc('funciones_proced.id')
        ->paginate(15, ['funciones_proced.*']);

        $funcion_desc = Funciones::WHERE('id', '=', $id)->FIRST();

        return view('vistas_pat.funciones_pat', compact('data','area_org', 'org', 'funcion_desc'));

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
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        $funciones = Funciones::find($id);
        $funciones->fun_proc =  trim($request->input('nom_funcion_edit'));
        $funciones->updated_at = date('Y-m-d');
        $funciones->iduser_updated = $id_user;
        $funciones->save();

        return redirect()->route('pat.funciones.mostrar')->with('success', '¡Registro actualizado exitosamente!');
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
        $procedimientos = Procedimientos::select('id')
        ->where('id_parent', '=', $id)->get();

        //Eliminamos los proced viculados
        for ($i=0; $i < count($procedimientos); $i++) {
            Procedimientos::destroy($procedimientos[$i]['id']);
        }

        Funciones::destroy($id);
        return redirect()->route('pat.funciones.mostrar')->with('success', '¡Registro eliminado exitosamente!');

    }
}
