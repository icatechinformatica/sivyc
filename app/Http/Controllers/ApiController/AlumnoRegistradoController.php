<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AlumnoRegistradoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * Modificación de la consulta alumnos registro con query builder
         */
        $alumnos = DB::table('alumnos_registro')
                   ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                   ->JOIN('cursos', 'alumnos_registro.id_curso', '=', 'cursos.id')
                   ->SELECT(
                       'alumnos_registro.unidad',
                       'alumnos_registro.no_control',
                       'alumnos_pre.apellido_paterno',
                       'alumnos_pre.apellido_materno',
                       'alumnos_pre.nombre AS nombrealumno',
                       'alumnos_pre.curp AS curp_alumno',
                       'alumnos_pre.fecha_nacimiento',
                       'alumnos_pre.sexo',
                       'alumnos_pre.domicilio',
                       'alumnos_pre.colonia',
                       'alumnos_pre.municipio',
                       'alumnos_pre.estado_civil',
                       DB::raw("CONCAT('alumnos_pre.ultimo_grado_estudios','-', 'cursos.nombre_curso') AS ultimo_grado_estudios"),
                       'alumnos_pre.telefono',
                       'alumnos_pre.correo',
                       'alumnos_registro.id AS id_registro',
                       'alumnos_pre.discapacidad',
                       'alumnos_registro.etnia',
                       'alumnos_registro.indigena',
                       'alumnos_registro.migrante'
                   )
                   ->GROUPBY('alumnos_registro.unidad',
                   'alumnos_registro.no_control',
                   'alumnos_pre.apellido_paterno',
                   'alumnos_pre.apellido_materno',
                   'nombrealumno',
                   'curp_alumno',
                   'alumnos_pre.fecha_nacimiento',
                   'alumnos_pre.sexo',
                   'alumnos_pre.domicilio',
                   'alumnos_pre.colonia',
                   'alumnos_pre.municipio',
                   'alumnos_pre.estado_civil',
                   'alumnos_pre.ultimo_grado_estudios',
                   'alumnos_pre.telefono',
                   'alumnos_pre.correo',
                   'id_registro',
                   'alumnos_pre.discapacidad',
                   'alumnos_registro.etnia',
                   'alumnos_registro.indigena',
                   'alumnos_registro.migrante',
                   'cursos.nombre_curso')
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
        $alumnos = DB::table('alumnos_registro')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->JOIN('cursos', 'alumnos_registro.id_curso', '=', 'cursos.id')
                ->SELECT(
                    'alumnos_registro.unidad',
                    'alumnos_registro.no_control',
                    'alumnos_pre.apellido_paterno',
                    'alumnos_pre.apellido_materno',
                    'alumnos_pre.nombre AS nombrealumno',
                    'alumnos_pre.curp AS curp_alumno',
                    'alumnos_pre.fecha_nacimiento',
                    'alumnos_pre.sexo',
                    'alumnos_pre.domicilio',
                    'alumnos_pre.colonia',
                    'alumnos_pre.municipio',
                    'alumnos_pre.estado_civil',
                    DB::raw("CONCAT('alumnos_pre.ultimo_grado_estudios','-', 'cursos.nombre_curso') AS ultimo_grado_estudios"),
                    'alumnos_pre.telefono',
                    'alumnos_pre.correo',
                    'cursos.nombre_curso',
                    'cursos.id AS id_cursos'
                )
                ->WHERE('alumnos_registro.no_control', '=', $id)
                ->FIRST();

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
