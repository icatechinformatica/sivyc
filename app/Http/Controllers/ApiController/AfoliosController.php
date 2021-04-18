<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Afolios;

class AfoliosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $afolios = new Afolios();
        $retrieveAfolios = $afolios->all();
        return response()->json($retrieveAfolios, 200);
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
            //implementación del código
            $afolio = new Afolios;
            $afolio->unidad = $request->unidad;
            $afolio->finicial = $request->finicial;
            $afolio->ffinal = $request->ffinal;
            $afolio->total = $request->total;
            $afolio->mod = $request->mod;
            $afolio->facta = $request->facta;
            $afolio->realizo = $request->realizo;

            $afolio->save();
            // redireccionamos con un mensaje de éxito
            return response()->json(['success' => 'Nuevo Afolio Agregado Exitosamente'], 200);
        } catch (Exception $th) {
            //throw $th;
            return response()->json(['error' => $th->getMessage()], 501);
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
        //
        try {
            //code...
            $Afolio= new Afolios();
            $Afolio->findOrfail($id)->update($request->all());
            return response()->json(['success' => 'Afolio actualizado exitosamente'], 200);
        } catch (Exception $e) {
            //throw $th;
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
