<?php

namespace App\Http\Controllers\PatController;

use App\Http\Controllers\Controller;
use App\Models\ModelPat\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $id_user = Auth::user()->id;
        } catch (\Throwable $th) {
            //throw $th;
            return redirect('/login');
        }
        $texto = ucwords($request->get('busqueda_unidad'));
        $data = UnidadMedida::Busqueda($texto)
            ->select('unidades_medida.*')
            ->orderByDesc('unidades_medida.id')
            ->paginate(15, ['unidades_medida.*']);

        return view('vistas_pat.um_pat', compact('data'));
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
        $unidadm = new UnidadMedida;
        $unidadm['numero'] = trim($request->input('numero_unidad'));
        $unidadm['unidadm'] = trim($request->input('nombre_unidad'));
        $unidadm['tipo_unidadm'] = trim($request->input('tipo_unidad'));
        $unidadm['status'] = 'activo';
        $unidadm['iduser_created'] =  Auth::user()->id;
        $unidadm['created_at'] = date('Y-m-d');
        $unidadm->save();

        return redirect()->route('pat.unidadesmedida.mostrar')->with('success', '¡Registro guardado exitosamente!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $data = UnidadMedida::Busqueda('')
        // ->select('unidades_medida.*')
        // ->orderByDesc('unidades_medida.id')
        // ->paginate(15, ['unidades_medida.*']);

        $unidad = UnidadMedida::WHERE('id', '=', $request->id)->FIRST();

        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'datos' => $unidad,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

        $unidadesm = UnidadMedida::find($id);
        $unidadesm->numero = $request->input('numero_unidad_edit');
        $unidadesm->unidadm = $request->input('nombre_unidad_edit');
        $unidadesm->unidadm = $request->input('tipo_unidad_edit');
        $unidadesm->iduser_updated = Auth::user()->id;
        $unidadesm->updated_at = date('Y-m-d');
        $unidadesm->save();

        return redirect()->route('pat.unidadesmedida.mostrar')->with('success', '¡Registro actualizado exitosamente!');
    }

    public function status(Request $request)
    {
        $id = (int)$request->id;
        $unidadesm = UnidadMedida::find($id);
        $unidadesm->status = $request->status;
        $unidadesm->iduser_updated = Auth::user()->id;
        $unidadesm->updated_at = date('Y-m-d');
        $unidadesm->save();
        return response()->json([
            'status' => 200,
            'mensaje' => 'se realizo exitosamente',
            'id' => $id,
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
        // UnidadMedida::destroy($id);
        // return redirect()->route('pat.unidadesmedida.mostrar')->with('success', '¡Registro eliminado exitosamente!');
    }
}
