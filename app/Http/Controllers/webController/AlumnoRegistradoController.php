<?php

namespace App\Http\Controllers\webController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Alumnopre;
use Carbon\Carbon;
use App\Models\Unidad;
use App\Models\Municipio;
use App\Models\Estado;
use App\Models\especialidad;
use App\Models\curso;
// reference the Dompdf namespace
use PDF;

class AlumnoRegistradoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alumnos = Alumno::LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->ORDERBY('id_registro', 'desc')
                ->GET([
                    'alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno',
                    'alumnos_registro.no_control', 'alumnos_registro.id AS id_registro',
                    'cursos.nombre_curso',
                ]);

        return view('layouts.pages.alumnos_registrados', compact('alumnos'));

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
        $idmatricula = base64_decode($id);
        $alumnos_registrados = new Alumno();
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
        'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
        'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
        'alumnos_registro.etnia', 'alumnos_registro.fecha')
                            ->WHERE('alumnos_registro.id', '=', $idmatricula)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->GET();

        return view('layouts.pages.alumno_registrado', compact('alumnos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Especialidad = new especialidad;
        $especialidades = $Especialidad->all();
        $municipio = new Municipio();
        $estado = new Estado();
        $municipios = $municipio->all();
        $estados = $estado->all();
        $curso = new curso();
        $cursos = $curso->all();
        //
        $id_alumno_registro = base64_decode($id);
        $alumnos_registrados = new Alumno();
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
        'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
        'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
        'alumnos_registro.etnia', 'alumnos_registro.fecha', 'alumnos_registro.id_especialidad', 'alumnos_registro.id_curso')
                            ->WHERE('alumnos_registro.id', '=', $id_alumno_registro)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->GET();

        $fecha_nac = explode("-", $alumnos[0]->fecha_nacimiento);
        $anio_nac = $fecha_nac[0];
        $mes_nac = $fecha_nac[1];
        $dia_nac = $fecha_nac[2];



        return view('layouts.pages.alumno_registro_modificar', compact('alumnos', 'especialidades', 'municipios', 'estados', 'dia_nac', 'mes_nac', 'anio_nac', 'cursos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idregistrado)
    {
        $Alumno = new Alumno();
        $usuario = Auth::user()->name;
        //
        $array_solicitud = [
            'id_especialidad' => $request->input('especialidad_sid_mod'),
            'id_curso' => $request->input('curso_sid_mod'),
            'horario' => trim($request->input('horario_mod')),
            'grupo' => trim($request->input('grupo_mod')),
            'tipo_curso' => trim($request->input('tipo_curso_mod')),
            'realizo' => trim($usuario),
            'cerrs' => $request->input('cerrs_mod')
        ];
        $alumnoId = base64_decode($idregistrado);

        $Alumno->WHERE('id', '=', $alumnoId)->UPDATE($array_solicitud);

        $noControl = $request->no_control_update;
        return redirect()->route('alumnos.inscritos')
            ->with('success', sprintf('ASPIRANTE %s  MODIFICADO EXTIOSAMENTE!', $noControl));
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

    protected function getDocumentoSid($nocontrol)
    {
        $noControl = base64_decode($nocontrol);
        $alumnos_registrados = new Alumno();
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo','alumnos_pre.chk_acta_nacimiento','alumnos_pre.chk_curp','alumnos_pre.chk_comprobante_domicilio','alumnos_pre.chk_fotografia',
        'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio','alumnos_pre.fotografia', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio','alumnos_pre.chk_ine','alumnos_pre.chk_pasaporte_licencia',
        'alumnos_pre.chk_comprobante_ultimo_grado','alumnos_pre.chk_comprobante_calidad_migratoria','alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
        'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
        'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad','alumnos_registro.id',
        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
        'alumnos_registro.etnia', 'alumnos_registro.fecha', 'alumnos_pre.medio_entero', 'alumnos_pre.sistema_capacitacion_especificar', 'alumnos_pre.realizo', 'cursos.costo')
                            ->WHERE('alumnos_registro.no_control', '=', $noControl)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->GET();
        $edad = Carbon::parse($alumnos[0]->fecha_nacimiento)->age;
        $date = carbon::now()->toDateString();
        set_time_limit(300);

        // Descomentar este pathimg si se trabajara con el archivo de forma local
        // $pathimg = substr($alumnos[0]->fotografia,22);

        // Comentar este pathimg si se trabajara con el archivo de forma local
        $pathimg = substr($alumnos[0]->fotografia ,33);

        $pdf = PDF::loadView('layouts.pdfpages.registroalumno', compact('alumnos', 'edad','date','pathimg'));
        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('documento_sid_'.$noControl.'.pdf');

        //return view('layouts.pdfpages.registroalumno', compact('alumnos','edad','date'));
    }
}
