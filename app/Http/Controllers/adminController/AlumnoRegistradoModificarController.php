<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\DB;
use App\Models\Inscripcion;
use App\Models\Folios;
use App\Models\Calificacion;

class AlumnoRegistradoModificarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tipo_alumno_registrado = $request->get('tipo_busqueda_por_alumno_registrado');
        $busqueda_alumno_registrado = $request->get('busquedaporAlumnoRegistrado');
        //
        $alumnos = Alumno::busqueda($tipo_alumno_registrado, $busqueda_alumno_registrado)
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->ORDERBY('id_registro', 'desc')
                ->PAGINATE(15, [
                    'alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                    'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
                    'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id AS id_registro',
                    'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad', 'alumnos_registro.cerrs',
                    'alumnos_registro.etnia', 'alumnos_registro.id_pre AS preiscripcion', 'alumnos_registro.estatus_modificacion'
                ]);

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
        $alumnos_pre = $alumnos_registrados->WHERE('alumnos_registro.id_pre', '=', $idPre)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->GET(['alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                            'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
                            'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
                            'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
                            'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad',
                            'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
                            'alumnos_registro.etnia', 'alumnos_registro.fecha']);
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
        $numero_control = trim($request->numero_control_edit);
        if (strcmp($codigo_verificacion, $codigo_verificacion_edit) === 0){
            /**
             *
             *   MODIFICACIÓN DE REGISTROS PARA LAS SIGUIENTES TABLAS
             *   tbl_inscripcion, tbl_calificaciones, tbl_folios
             *
             *
            */
            $numero_de_control = Alumno::WHERE('id_pre', '=', $id_pre)->FIRST(['no_control']);
            Inscripcion::WHERE('matricula', $numero_de_control->no_control)->UPDATE(['matricula' => $numero_control]);
            // modificacion de folio
            Folios::WHERE('matricula', $numero_de_control->no_control)->UPDATE(['matricula' => $numero_control]);
            // modificacion de calificaciones
            Calificacion::WHERE('matricula', $numero_de_control->no_control)->UPDATE(['matricula' => $numero_control]);
            // actualizamos los registros
            Alumno::WHERE('id_pre', '=', $id_pre)->UPDATE(['estatus_modificacion' => true, 'no_control' => $numero_control]);

            return redirect()->route('alumno_registrado.modificar.index')
            ->with('success', 'ASPIRANTE MODIFICADO EXTIOSAMENTE!');
        } else {
            return redirect()->back()->withErrors(['msg', 'EL CÓDIGO DE VERIFICACIÓN NO ES VÁLIDO']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $id_pre)
    {
        //

        if (isset($id)){
            $ids = base64_decode($id);
            Alumno::WHERE('id', $ids)->delete();
            $json=json_encode($ids);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    public function indexConsecutivo(Request $request){
        $tipo = 'no_control';

        $consecutivos_unidad = Alumno::busqueda($tipo, $request->get('busquedaConsecutivo'))->WHERE('unidad', $request->get('unidades_ubicacion'))
        ->orderBy(DB::raw('(SUBSTRING(no_control, length(no_control)-3, length(no_control)))'), 'ASC')
        ->GET([
            'no_control',
            DB::raw('(SUBSTRING(no_control FROM 1 FOR 2)) anio '),
            'numero_solicitud',
            DB::raw('(SUBSTRING(no_control, length(no_control)-3, length(no_control))) consecutivo '),
            'id',
            'unidad',
            'id_pre'
        ]);

        return view('layouts.pages_admin.consecutivos_registrados', compact('consecutivos_unidad'));
    }

    public function indexUnidad(){
        $tblUnidades = tbl_unidades::SELECT('ubicacion')->GROUPBY('ubicacion')->GET(['ubicacion']);
        return view('layouts.pages_admin.accion_movil', compact('tblUnidades'));
    }

}
