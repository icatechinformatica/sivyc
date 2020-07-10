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
        $alumnos = Alumno::LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.unidad')
                ->ORDERBY('id_registro', 'desc')
                ->GET([
                    'alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                    'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
                    'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control',
                    'alumnos_registro.id', 'alumnos_pre.ultimo_grado_estudios', 'alumnos_pre.empresa_trabaja',
                    'alumnos_pre.antiguedad', 'alumnos_pre.direccion_empresa', 'alumnos_pre.medio_entero', 'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.grupo',
                    'alumnos_pre.chk_acta_nacimiento', 'alumnos_pre.chk_curp', 'alumnos_pre.chk_comprobante_domicilio',
                    'alumnos_pre.chk_comprobante_domicilio', 'alumnos_pre.chk_fotografia',
                    'alumnos_pre.chk_ine', 'alumnos_pre.chk_pasaporte_licencia', 'alumnos_pre.chk_comprobante_ultimo_grado',
                    'alumnos_pre.acta_nacimiento', 'alumnos_pre.curp',
                    'alumnos_pre.comprobante_domicilio', 'alumnos_pre.fotografia', 'alumnos_pre.ine', 'alumnos_pre.pasaporte_licencia_manejo',
                    'alumnos_pre.comprobante_ultimo_grado', 'alumnos_pre.chk_comprobante_calidad_migratoria',
                    'alumnos_pre.comprobante_calidad_migratoria', 'alumnos_pre.puesto_empresa', 'alumnos_pre.sistema_capacitacion_especificar',
                    'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.cct', 'alumnos_registro.id AS id_registro',
                    'alumnos_registro.unidad', 'alumnos_registro.etnia', 'alumnos_registro.indigena', 'alumnos_registro.migrante'
                ]);

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
        $alumnos = Alumno::WHERE('alumnos_registro.no_control', '=', $id)
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.unidad')
                ->FIRST([
                    'alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                    'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
                    'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control',
                    'alumnos_registro.id', 'alumnos_pre.ultimo_grado_estudios', 'alumnos_pre.empresa_trabaja',
                    'alumnos_pre.antiguedad', 'alumnos_pre.direccion_empresa', 'alumnos_pre.medio_entero', 'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.grupo',
                    'alumnos_pre.chk_acta_nacimiento', 'alumnos_pre.chk_curp', 'alumnos_pre.chk_comprobante_domicilio',
                    'alumnos_pre.chk_comprobante_domicilio', 'alumnos_pre.chk_fotografia',
                    'alumnos_pre.chk_ine', 'alumnos_pre.chk_pasaporte_licencia', 'alumnos_pre.chk_comprobante_ultimo_grado',
                    'alumnos_pre.acta_nacimiento', 'alumnos_pre.curp',
                    'alumnos_pre.comprobante_domicilio', 'alumnos_pre.fotografia', 'alumnos_pre.ine', 'alumnos_pre.pasaporte_licencia_manejo',
                    'alumnos_pre.comprobante_ultimo_grado', 'alumnos_pre.chk_comprobante_calidad_migratoria',
                    'alumnos_pre.comprobante_calidad_migratoria', 'alumnos_pre.puesto_empresa', 'alumnos_pre.sistema_capacitacion_especificar',
                    'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.cct', 'alumnos_registro.id AS id_registro', 'alumnos_registro.cerrs',
                    'alumnos_registro.etnia', 'alumnos_registro.unidad', 'alumnos_registro.etnia', 'alumnos_registro.indigena', 'alumnos_registro.migrante'
                ]);

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
