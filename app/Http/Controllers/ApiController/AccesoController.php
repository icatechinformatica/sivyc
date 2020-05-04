<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Acceso;

class AccesoController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Acceso= new Acceso();
        $accesos = $Acceso->all();
        return response()->json($accesos, 200);
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
            $Acceso = new Acceso();
            $Acceso->nombrecompleto = $request->nombrecompleto;
            $Acceso->id_categoria = $request->id_categoria;
            $Acceso->numeroenlace = $request->numeroenlace;
            $Acceso->contrasena = $request->contrasena;
            $Acceso->usuario = $request->usuario;
            $Acceso->correo = $request->correo;
            $Acceso->unidad = $request->unidad;
            $Acceso->puesto = $request->puesto;
            $Acceso->save();

            return response()->json(['success' => 'El Acceso se cargo exitosamente en la base de datos'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
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
            $Accesos= new Acceso();
            $Accesos->whereId($id)->update($request->all());
            return response()->json(['success' => 'Curso actualizado exitosamente'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
    }
}
