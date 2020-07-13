<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlumnosSice;

class alumnosRegistroSiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $busqueda_curp = $request->get('busqueda_curp');
        $alumnosRegistradosSice = AlumnosSice::busquedacurp($busqueda_curp)->PAGINATE(30, [
            'id', 'no_control', 'curp', 'estado_modificado'
        ]);
        return view('layouts.pages_admin.registro_alumnos_sice', compact('alumnosRegistradosSice'));
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
        $idRegistrado = base64_decode($id);
        $alumnoRegistrado = AlumnosSice::findOrfail($idRegistrado);
        return view('layouts.pages_admin.alumno_registrado_sice_detalle', compact('alumnoRegistrado'));
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
        $id_registro_sice = base64_decode($id);
        $numero_control = trim($request->numero_control_edit);
        if (!empty(trim($numero_control))){
            // actualizamos los registros
            AlumnosSice::WHERE('id', $id_registro_sice)->UPDATE(['no_control' => $numero_control, 'estado_modificado' => true,]);

            return redirect()->route('alumnos_registrados_sice.inicio')
            ->with('success', 'ASPIRANTE MODIFICADO EXTIOSAMENTE!');
        } else {
            return redirect()->back()->withErrors(['msg', 'EL NÚMERO DE CONTROL ESTÁ VACIO']);
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
