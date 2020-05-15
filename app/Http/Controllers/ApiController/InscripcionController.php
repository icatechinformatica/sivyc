<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Models\api\Inscripcion;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $inscripcion = new Inscripcion();
        $inscripciones = $inscripcion->all();
        return response()->json($inscripciones, 200);
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
            $Inscripcion = new Inscripcion();
            $Inscripcion->unidad = $request->unidad;
            $Inscripcion->matricula = $request->matricula;
            $Inscripcion->nombre = $request->nombre;
            $Inscripcion->id_curso = $request->id_curso;
            $Inscripcion->curso = $request->curso;
            $Inscripcion->instructor = $request->instructor;
            $Inscripcion->inicio = $request->inicio;
            $Inscripcion->termino = $request->termino;
            $Inscripcion->hinicio = $request->hinicio;
            $Inscripcion->hfin = $request->hfin;
            $Inscripcion->tinscripcion = $request->tinscripcion;
            $Inscripcion->abrinscri = $request->abrinscri;
            $Inscripcion->hini2 = $request->hini2;
            $Inscripcion->hfin2 = $request->hfin2;
            $Inscripcion->munidad = $request->munidad;
            $Inscripcion->costo = $request->costo;
            $Inscripcion->save();

            return response()->json(['success' => 'Se ha generado una inscripcion exitosamente'], 200);
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
        //
        // actualizar
        try {
            //code...
            $Inscripcion = new Inscripcion();
            $Inscripcion->whereId($id)->update($request->all());
            return response()->json(['success' => 'Inscripcion actualizada'], 200);
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
