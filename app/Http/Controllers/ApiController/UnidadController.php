<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Unidad;

class UnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Unidad= new Unidad();
        $unidades = $Unidad->all();
        return response()->json($unidades, 200);
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
        try {
            # enviar o generar codigo que si funciona
            $Unidad = new Unidad();
            $Unidad->unidad = $request->nombrecompreto;
            $Unidad->cct = $request->id_categoria;
            $Unidad->dunidad = $request->dunidad;
            $Unidad->dgeneral = $request->dgeneral;
            $Unidad->plantel = $request->plantel;
            $Unidad->academico = $request->academico;
            $Unidad->vinculacion = $request->vinculacion;
            $Unidad->dacademico = $request->dacademico;
            $Unidad->pdgeneral = $request->pdgeneral;
            $Unidad->pdacademico = $request->pdacademico;
            $Unidad->pdunidad = $request->pdunidad;
            $Unidad->pacademico = $request->pacademico;
            $Unidad->pvinculacion = $request->pvinculacion;
            $Unidad->save();

            return response()->json(['success' => 'Curso se cargo exitosamente en la base de datos'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
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
        // actualizando
        try {
            $Unidad = new Unidad();
            $Unidad->whereId($id)->update($request->all());
            return response()->json(['success' => 'Unidad actualizado exitosamente'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
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
