<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Alumno;
use App\Models\api\AlumnosPre;
use Carbon\Carbon;
use App\Models\api\Unidad;

class AlumnoRegistradoController extends Controller
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
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id', 'alumnos_registro.ultimo_grado_estudios', 'alumnos_registro.empresa_trabaja',
        'alumnos_registro.antiguedad', 'alumnos_registro.direccion_empresa', 'alumnos_registro.medio_entero', 'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.grupo',
        'alumnos_registro.chk_acta_nacimiento', 'alumnos_registro.chk_curp', 'alumnos_registro.chk_comprobante_domicilio', 'alumnos_registro.chk_comprobante_domicilio', 'alumnos_registro.chk_fotografia',
        'alumnos_registro.chk_ine', 'alumnos_registro.chk_pasaporte_licencia', 'chk_comprobante_ultimo_grado', 'alumnos_registro.acta_nacimiento', 'alumnos_registro.curp',
        'alumnos_registro.comprobante_domicilio', 'alumnos_registro.fotografia', 'alumnos_registro.ine', 'alumnos_registro.pasaporte_licencia_manejo',
        'alumnos_registro.comprobante_ultimo_grado', 'alumnos_registro.chk_comprobante_calidad_migratoria', 'alumnos_registro.comprobante_calidad_migratoria', 'alumnos_registro.puesto_empresa', 'alumnos_registro.sistema_capacitacion_especificar',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad', 'alumnos_registro.id AS id_registro')
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->ORDERBY('id_registro', 'desc')
                ->GET();

        return response()->json($alumnos, 200);
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
        $alumnos_registrados = new Alumno();
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id', 'alumnos_registro.ultimo_grado_estudios', 'alumnos_registro.empresa_trabaja',
        'alumnos_registro.antiguedad', 'alumnos_registro.direccion_empresa', 'alumnos_registro.medio_entero', 'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.grupo',
        'alumnos_registro.chk_acta_nacimiento', 'alumnos_registro.chk_curp', 'alumnos_registro.chk_comprobante_domicilio', 'alumnos_registro.chk_comprobante_domicilio', 'alumnos_registro.chk_fotografia',
        'alumnos_registro.chk_ine', 'alumnos_registro.chk_pasaporte_licencia', 'chk_comprobante_ultimo_grado', 'alumnos_registro.acta_nacimiento', 'alumnos_registro.curp',
        'alumnos_registro.comprobante_domicilio', 'alumnos_registro.fotografia', 'alumnos_registro.ine', 'alumnos_registro.pasaporte_licencia_manejo',
        'alumnos_registro.comprobante_ultimo_grado', 'alumnos_registro.chk_comprobante_calidad_migratoria', 'alumnos_registro.comprobante_calidad_migratoria', 'alumnos_registro.puesto_empresa', 'alumnos_registro.sistema_capacitacion_especificar',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad')
                ->WHERE('alumnos_registro.id', '=', $id)
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->GET();

        return response()->json($alumnos, 200);
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
