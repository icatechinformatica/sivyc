<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\Procedimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        //Obtenemos el id en el parametro
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


        // Obtenermos el area del usuario
        $area = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->Join('users as u', 'u.id_orgarea', 'o.id')
        ->where('u.id_orgarea', $area)->first();

        //funcion
        $data = Procedimientos::select('fun_proc')
        ->where('id', '=', $id)
        ->orderByDesc('funciones_proced.id')->first();

        //procedimientos
        $data2 = Procedimientos::select('id','fun_proc')
        ->where('id_parent', '=', $id)
        ->orderByDesc('funciones_proced.id')->get();


        //Obtenemos los parametros
        return view('vistas_pat.proced_pat', compact('data', 'data2' ,'area', 'org', 'id'));
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
    public function store(Request $request, $id)
    {
        try {
            $id_organismo = Auth::user()->id_organismo;
            $id_area = Auth::user()->id_orgarea;
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        $procedimientos = new Procedimientos;
        $procedimientos['id_parent'] = $id; //este id es de la funcion en el cual se relaciona
        $procedimientos['id_org'] = $id_organismo;
        $procedimientos['fun_proc'] =  trim($request->input('nom_proced'));
        $procedimientos['activo'] = 'true';
        $procedimientos['created_at'] = date('Y-m-d');
        $procedimientos['iduser_created'] = $id_user;
        $procedimientos['iduser_updated'] = $id_user;
        $procedimientos['id_area'] = $id_area;
        $procedimientos->save();

        return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro guardado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($idedi, $id)
    {
        //Obtenemos el id en el parametro
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


        // Obtenermos el area del usuario
        $area = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->Join('users as u', 'u.id_orgarea', 'o.id')
        ->where('u.id_orgarea', $area)->first();

        //funcion
        $data = Procedimientos::select('fun_proc')
        ->where('id', '=', $id)->first();

        //procedimientos
        $data2 = Procedimientos::select('id','fun_proc')
        ->where('id_parent', '=', $id)
        ->orderByDesc('funciones_proced.id')->get();

        //buscar el procedimiento a editar
        $dataedit = Procedimientos::select('id','fun_proc')
        ->where('id', '=', $idedi)->first();

        return view('vistas_pat.proced_pat', compact('data', 'data2' ,'area', 'org', 'id', 'dataedit'));
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
    public function update(Request $request, $idedi, $id)
    {

        $funciones = Procedimientos::find($idedi);
        $funciones->fun_proc =  trim($request->input('nom_proced_edit'));
        $funciones->updated_at = date('Y-m-d');
        $funciones->iduser_updated = Auth::user()->id;
        $funciones->save();

        return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idd, $id)
    {
        Procedimientos::destroy($idd);
        return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro eliminado exitosamente!');
    }
}
