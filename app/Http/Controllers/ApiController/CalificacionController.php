<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Calificacion;

class CalificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Calificacion= new Calificacion();
        $Calificaciones = $Calificacion->all();
        return response()->json($Calificaciones, 200);
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
        // Enviar o generar codigo que si funciona
        try {
            $Calificacion = new Calificacion();
            $Calificacion->unidad = $request->unidad;
            $Calificacion->matricula = $request->matricula;
            $Calificacion->acreditado = $request->acreditado;
            $Calificacion->noacreditado = $request->noacreditado;
            $Calificacion->idcurso = $request->idcurso;
            $Calificacion->idgrupo = $request->idgrupo;
            $Calificacion->area = $request->area;
            $Calificacion->espe = $request->espe;
            $Calificacion->curso = $request->curso;
            $Calificacion->mod = $request->mod;
            $Calificacion->instructor = $request->instructor;
            $Calificacion->inicio = $request->inicio;
            $Calificacion->termino = $request->termino;
            $Calificacion->hini = $request->hini;
            $Calificacion->hfin = $request->hfin;
            $Calificacion->dura = $request->dura;
            $Calificacion->ciclo = $request->ciclo;
            $Calificacion->periodo = $request->periodo;
            $Calificacion->calificacion = $request->calificacion;
            $Calificacion->alumno = $request->alumno;
            $Calificacion->valido = $request->valido;
            $Calificacion->realizo = $request->realizo;
            $Calificacion->save();

            return response()->json(['success' => 'El Calificacion se cargo exitosamente en la base de datos'], 200);
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
        // actualiando
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
    public function update(Request $request, $idcurso, $matricula)
    {
        //
        // actualizando
        try {
            $solicitud_request = $request->all();
            $Calificacion = new Calificacion();
            $Calificacion->WHERE([
                ['idcurso', '=', $idcurso],
                ['matricula', '=', $matricula],
            ])->update($request->all());
            return response()->json(['success' => 'Calificacion Modificada Exitosamente!'], 200);
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
