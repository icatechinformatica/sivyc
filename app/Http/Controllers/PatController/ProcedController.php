<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\Procedimientos;
use App\Models\ModelPat\RegistrosProced;
use App\Models\ModelPat\UnidadMedida;
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
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

         //Obtenemos el area del usuario
         $area = DB::table('tbl_organismos as o')->select('o.id', 'nombre', 'id_parent')
         ->Join('users as u', 'u.id_organismo', 'o.id')
         ->where('u.id_organismo', $organismo)->first();

         // Obtenermos el organismo del usuario
         $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
         ->where('o.id', $area->id_parent)->first();

        //funcion
        $data = Procedimientos::select('fun_proc')
        ->where('id', '=', $id)->first();

        //procedimientos
        $data2 = Procedimientos::select('funciones_proced.id', 'fun_proc', 'activo', 'u.unidadm')
        ->join('unidades_medida as u', 'u.id', 'id_unidadm')
        ->where('id_parent', '=', $id)
        ->where(DB::raw("date_part('year' , funciones_proced.created_at )"), '=', date('Y'))
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
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        $unidad_medida = $request->input('text_buscar_unidadm');
        //Buscamos el id de la unidad de medida para agregar en el campo de procedimientos
        $id_unidadm = UnidadMedida::select('id')->where('unidadm', '=', $unidad_medida)->first();
        // dd($id_unidadm);
        //validar si la unidad de medida existe
        if ($id_unidadm != null) {

            $procedimientos = new Procedimientos;
            $procedimientos['id_parent'] = $id; //este id es de la funcion en el cual se relaciona
            $procedimientos['id_org'] = $id_organismo;
            $procedimientos['fun_proc'] =  trim($request->input('nuevoReg'));
            $procedimientos['activo'] = 'true';
            $procedimientos['id_unidadm'] = $id_unidadm->id; // id de unidad de medida
            $procedimientos['created_at'] = date('Y-m-d');
            $procedimientos['iduser_created'] = $id_user;
            $procedimientos['iduser_updated'] = $id_user;
            $procedimientos->save();

            $nuevoRegistro = [
                'meta' => 0,
                'avance' => 0,
                'fechavasave' => '',
                'fechmetasave' => '',
                'expdesviaciones' => '',
            ];

            $id_insertado = $procedimientos->id;
            $metas_avances = new RegistrosProced;
            $metas_avances['id_proced'] = $id_insertado;
            $metas_avances['ejercicio'] = date('Y');
            $metas_avances['total'] = 0;
            $metas_avances['observaciones'] = ''; //planeacion
            $metas_avances['observmeta'] = '';
            $metas_avances->enero = $nuevoRegistro;
            $metas_avances->febrero = $nuevoRegistro;
            $metas_avances->marzo = $nuevoRegistro;
            $metas_avances->abril = $nuevoRegistro;
            $metas_avances->mayo = $nuevoRegistro;
            $metas_avances->junio = $nuevoRegistro;
            $metas_avances->julio = $nuevoRegistro;
            $metas_avances->agosto = $nuevoRegistro;
            $metas_avances->septiembre = $nuevoRegistro;
            $metas_avances->octubre = $nuevoRegistro;
            $metas_avances->noviembre = $nuevoRegistro;
            $metas_avances->diciembre = $nuevoRegistro;
            $metas_avances['created_at'] = date('d-m-Y');
            // $metas_avances['updated_at'] = date('d-m-Y');
            $metas_avances['iduser_created'] = $id_user;
            // $metas_avances['iduser_updated'] = $id_user;
            $metas_avances->save();

            return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro guardado exitosamente!');
        }

        return redirect()->route('pat.proced.mostrar', compact('id'))->with('danger', '¡Error al registrar, verifique si existe la unidad de medida ingresada!');
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
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }

        //Obtenemos el area del usuario
        $area = DB::table('tbl_organismos as o')->select('o.id', 'nombre', 'id_parent')
        ->Join('users as u', 'u.id_organismo', 'o.id')
        ->where('u.id_organismo', $organismo)->first();

        // Obtenermos el organismo del usuario
        $org = DB::table('tbl_organismos as o')->select('o.id', 'nombre')
        ->where('o.id', $area->id_parent)->first();

        //funcion
        $data = Procedimientos::select('fun_proc')
        ->where('id', '=', $id)->first();

        //procedimientos
        $data2 = Procedimientos::select('funciones_proced.id', 'fun_proc', 'activo', 'u.unidadm')
        ->join('unidades_medida as u', 'u.id', 'id_unidadm')
        ->where('id_parent', '=', $id)
        ->where(DB::raw("date_part('year' , funciones_proced.created_at )"), '=', date('Y'))
        ->orderByDesc('funciones_proced.id')->get();

        //buscar el procedimiento a editar
        $dataedit = Procedimientos::select('funciones_proced.id','fun_proc','id_unidadm', 'u.unidadm')
        ->join('unidades_medida as u', 'u.id', 'id_unidadm')
        ->where('funciones_proced.id', '=', $idedi)->first();


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
        //Verificamos si la unidad esta en la bd para poder guardar
        $id_unidadm_bd = UnidadMedida::select('id')->where('unidadm', '=', $request->input('um_upd'))->first();

        if($id_unidadm_bd != null){
            $proced = Procedimientos::find($idedi);
            $proced->fun_proc =  trim($request->input('nom_proced_edit'));
            $proced->id_unidadm = $id_unidadm_bd->id;
            $proced->updated_at = date('Y-m-d');
            $proced->iduser_updated = Auth::user()->id;
            $proced->save();

            return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro actualizado exitosamente!');
        }

        return redirect()->route('pat.proced.mostrar', compact('id'))->with('danger', '¡Error al registrar, verifique si existe la unidad de medida ingresada!');
    }

    public function status(Request $request)
    {
        $id = (int)$request->id;
        $proced = Procedimientos::find($id);
        $proced->activo = $request->status;
        $proced->iduser_updated = Auth::user()->id;
        $proced->updated_at = date('Y-m-d');
        $proced->save();
        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'status' => $request->status,
            'id' => $request->id,
        ]);

    }

    public function autocomplete(Request $request)
    {
        $search = $request->search;
        // $tipoCurso = $request->tipoCurso;

        if (isset($search) && $search != '') {
            $data = UnidadMedida::select('unidadm')
                ->where('unidadm', 'ilike', '%'.$search.'%')
                ->limit(7)->get();
        }
        $response = array();
        foreach ($data as $value) {
            $response[] = array('label' => $value->unidadm);
        }
        return json_encode($response);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($idd, $id)
    {
        //Procedimientos::destroy($idd);
        //return redirect()->route('pat.proced.mostrar', compact('id'))->with('success', '¡Registro eliminado exitosamente!');
    }
}
