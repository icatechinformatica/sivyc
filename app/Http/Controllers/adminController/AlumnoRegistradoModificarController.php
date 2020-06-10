<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;

class AlumnoRegistradoModificarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $alumnos_registrados = new Alumno();
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id AS id_registro',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad', 'alumnos_registro.cerrs',
        'alumnos_registro.etnia', 'alumnos_registro.id_pre AS preiscripcion')
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->ORDERBY('id_registro', 'desc')
                ->GET();

        return view('layouts.pages_admin.alumnos_registrados_modify', compact('alumnos'));
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
        $idPre = base64_decode($id);
        $id_preinscripcion = base64_encode($idPre);
        $alumnos_registrados = new Alumno();
        $alumnos_pre = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
        'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
        'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
        'alumnos_registro.etnia', 'alumnos_registro.fecha')
                            ->WHERE('alumnos_registro.id_pre', '=', $idPre)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->GET();
        return view('layouts.pages_admin.alumnos_edit_register', compact('alumnos_pre', 'id_preinscripcion'));
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
        $id_pre = base64_decode($id);
        $codigo_verificacion = 'ABC12$D%&7!';
        $codigo_verificacion_edit = trim($request->codigo_verificacion_edit);
        if (strcmp($codigo_verificacion, $codigo_verificacion_edit) === 0){
            // actualizamos los registros
            Alumno::whereIn('id_pre', $id_pre)->update(['estatus_modificacion' => true, 'no_control' => $request->numero_control_edit]);

            return redirect()->route('alumno_registrado.modificar.index')
            ->with('success', sprintf('ASPIRANTE MODIFICADO EXTIOSAMENTE!'));
        } else {
            return Redirect::back()->withErrors(['msg', 'EL CÓDIGO DE VERIFICACIÓN NO ES VÁLIDO']);
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
