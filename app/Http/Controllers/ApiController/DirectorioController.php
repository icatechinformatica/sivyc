<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Directorio;

class DirectorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Directorio = new Directorio();
        $directorios = $Directorio->all();
        return response()->json($directorios, 200);
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
            //cargando elementos para guardar registros
            $directorio = new Directorio();
            $directorio->id = $request->id;
            $directorio->nombre = $request->nombre;
            $directorio->apellidoPaterno = $request->apellidoPaterno;
            $directorio->apellidoMaterno = $request->apellidoMaterno;
            $directorio->puesto = $request->puesto;
            $directorio->numero_enlace = $request->numero_enlace;
            $directorio->categoria = $request->categoria;

            $directorio->save();

            return response()->json(['success' => 'Agregado al directorio exitosamente!'], 200);
        } catch (Exception $e) {
            //throw $th;
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
