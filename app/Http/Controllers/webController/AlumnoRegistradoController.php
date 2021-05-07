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
use Illuminate\Support\Facades\Log;
// reference the Dompdf namespace
use PDF;

class AlumnoRegistradoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $buscar = $request->get('busquedapor');

        $tipo = $request->get('tipo_busqueda');

        $alumnos = Alumno::busqueda($tipo, $buscar)
                ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                ->ORDERBY('id_registro', 'desc')
                ->PAGINATE(25, [
                    'alumnos_pre.nombre', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno',
                    'alumnos_registro.no_control', 'alumnos_registro.id AS id_registro',
                    'cursos.nombre_curso', 'alumnos_pre.es_cereso'
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
        $alumnos = $alumnos_registrados->SELECT('alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
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
    public function edit($id) {
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
        $alumnos = Alumno::WHERE('alumnos_registro.id', '=', $id_alumno_registro)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                    ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                    ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                    ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                    ->FIRST([
                        'alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                        'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo', 'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio',
                        'alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
                        'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
                        'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad',
                        'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
                        'alumnos_registro.etnia', 'alumnos_registro.fecha', 'alumnos_registro.id_especialidad', 'alumnos_registro.id_curso'
                    ]);

        $fecha_nac = explode("-", $alumnos->fecha_nacimiento);
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
        //
        $array_solicitud = [
            'id_especialidad' => $request->input('especialidad_sid_mod'),
            'id_curso' => $request->input('curso_sid_mod'),
            'horario' => trim($request->input('horario_mod')),
            'grupo' => trim($request->input('grupo_mod')),
            'tipo_curso' => trim($request->input('tipo_curso_mod'))
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

        $alumnos = Alumno::WHERE('alumnos_registro.id', '=', $noControl)
                            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                            ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->FIRST(['alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                            'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo','alumnos_pre.chk_acta_nacimiento','alumnos_pre.chk_curp','alumnos_pre.chk_comprobante_domicilio','alumnos_pre.chk_fotografia',
                            'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio','alumnos_pre.fotografia', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio','alumnos_pre.chk_ine','alumnos_pre.chk_pasaporte_licencia',
                            'alumnos_pre.chk_comprobante_ultimo_grado','alumnos_pre.chk_comprobante_calidad_migratoria','alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
                            'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
                            'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad','alumnos_registro.id',
                            'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
                            'alumnos_registro.etnia', 'alumnos_registro.fecha', 'alumnos_pre.medio_entero', 'alumnos_pre.sistema_capacitacion_especificar', 'alumnos_registro.realizo', 'cursos.costo']);
        $edad = Carbon::parse($alumnos->fecha_nacimiento)->age;
        $date = carbon::now()->toDateString();
        set_time_limit(300);

        // Descomentar este pathimg si se trabajara con el archivo de forma local
        // $pathimg = substr($alumnos[0]->fotografia,22);

        // Comentar este pathimg si se trabajara con el archivo de forma local
        $pathimg = substr($alumnos->fotografia ,33);

        $pdf = PDF::loadView('layouts.pdfpages.registroalumno', compact('alumnos', 'edad','date','pathimg'));
        // (Optional) Setup the paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('documento_sid_'.$alumnos->no_control.'.pdf');

        //return view('layouts.pdfpages.registroalumno', compact('alumnos','edad','date'));
    }

    protected function getDocumentoCerrsSid($nocontrol) {
        $noControl = base64_decode($nocontrol);

        $alumnos = Alumno::WHERE('alumnos_registro.id', $noControl)
                    ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'alumnos_registro.id_especialidad')
                    ->LEFTJOIN('cursos', 'cursos.id', '=', 'alumnos_registro.id_curso')
                            ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                            ->LEFTJOIN('tbl_unidades', 'alumnos_registro.unidad', '=', 'tbl_unidades.cct')
                            ->FIRST(['alumnos_pre.nombre AS nombrealumno', 'alumnos_pre.apellido_paterno', 'alumnos_pre.apellido_materno', 'alumnos_pre.correo', 'alumnos_pre.telefono',
                            'alumnos_pre.curp AS curp_alumno', 'alumnos_pre.sexo','alumnos_pre.chk_acta_nacimiento','alumnos_pre.chk_curp','alumnos_pre.chk_comprobante_domicilio','alumnos_pre.chk_fotografia',
                            'alumnos_pre.fecha_nacimiento', 'alumnos_pre.domicilio','alumnos_pre.fotografia', 'alumnos_pre.colonia', 'alumnos_pre.cp', 'alumnos_pre.municipio','alumnos_pre.chk_ine','alumnos_pre.chk_pasaporte_licencia',
                            'alumnos_pre.chk_comprobante_ultimo_grado','alumnos_pre.chk_comprobante_calidad_migratoria','alumnos_pre.estado', 'alumnos_pre.estado_civil', 'alumnos_pre.discapacidad', 'alumnos_registro.no_control', 'alumnos_registro.id',
                            'alumnos_registro.horario', 'alumnos_registro.grupo', 'alumnos_registro.tipo_curso', 'alumnos_pre.empresa_trabaja', 'alumnos_pre.puesto_empresa', 'alumnos_pre.antiguedad',
                            'alumnos_pre.direccion_empresa', 'alumnos_registro.unidad','alumnos_registro.id',
                            'cursos.nombre_curso', 'especialidades.nombre AS especialidad', 'tbl_unidades.unidad AS unidades', 'alumnos_registro.cerrs',
                            'alumnos_registro.etnia', 'alumnos_registro.fecha', 'alumnos_pre.medio_entero', 'alumnos_pre.sistema_capacitacion_especificar', 'alumnos_registro.realizo', 'cursos.costo',
                            'alumnos_pre.nacionalidad', 'alumnos_pre.es_cereso', 'alumnos_pre.nombre_cerss', 'alumnos_pre.direccion_cerss',
                            'alumnos_pre.titular_cerss', 'alumnos_pre.chk_ficha_cerss', 'alumnos_pre.numero_expediente']);

        $edad = Carbon::parse($alumnos->fecha_nacimiento)->age;
        $date = carbon::now()->toDateString();
        // DOMPDF según el tipo de documento a imprimir o la cantidad puede ser muy exigente así que aumentamos la memoria disponible
        //ini_set("memory_limit", "128MB");
        set_time_limit(300);

        // Descomentar este pathimg si se trabajara con el archivo de forma local
        // $pathimg = substr($alumnos[0]->fotografia,22);

        // Comentar este pathimg si se trabajara con el archivo de forma local
        $pathimg = substr($alumnos->fotografia ,33);

        return PDF::loadView('layouts.pdfpages.registroalumno_cerss', compact('alumnos', 'edad','date','pathimg'))
                ->setPaper('A4', 'portrait')->download('documento_sid_cerrs'.$alumnos->no_control.'.pdf');
    }
}
