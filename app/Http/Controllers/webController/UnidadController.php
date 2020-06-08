<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unidad;

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

    protected function ubicacion($ubicacion)
    {
        if (isset($ubicacion)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $Ubicacion = $ubicacion;
            $Unidades = new Unidad();

            $TblUnidades = $Unidades->WHERE('ubicacion', '=', $ubicacion)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($TblUnidades);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de ubicacion de Especialidad para filtar'));
        }

        return $json;
    }
}
