<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Alumnopre;
use Illuminate\Support\Facades\Input;
use PDF;

class AlumnoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $alumnos = new Alumno();
        $retrieveAlumnos = $alumnos->SELECT('alumnos_registro.no_control', 'alumnos_registro.fecha', 'alumnos_registro.numero_solicitud',
                                    'alumnos_pre.curp', 'alumnos_pre.nombre', 'alumnos_pre.apellidoPaterno', 'alumnos_pre.apellidoMaterno',
                                    'alumnos_pre.correo', 'alumnos_pre.telefono')
                                   ->LEFTJOIN('alumnos_pre', 'alumnos_pre.id', '=', 'alumnos_registro.id_pre')
                                   ->GET();
        $contador = $retrieveAlumnos->count();
        return view('layouts.pages.vstaalumnos', compact('retrieveAlumnos', 'contador'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('layouts.pages.frminscripcion1');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // vamos a guardar los registros
        $validateData = $request->validate([
            'nombre' => 'required',
            'telefono' => 'required',
            'curso' => 'required',
            'horario' => 'required',
            'especialidad_que_desea_inscribirse' => 'required',
            'modo_entero_del_sistema' => 'required',
            'motivos_eleccion_sistema_capacitacion' => 'required',
            'correo' => 'required'
        ]);

        $alumno = new Alumno([
            'no_control' => '231ABC',
            'domicilio' => $request->input('domicilio'),
            'numero_solicitud' => '123',
            'fecha' => $request->input('fecha_nacimiento'),
            'fecha_nacimiento' => $request->input('fecha_nacimiento'),
            'curp' => $request->input('curp'),
            'colonia' => $request->input('colonia'),
            'codigo_postal' => $request->input('codigo_postal'),
            'municipio' => $request->input('municipio'),
            'estado' => $request->input('estado'),
            'estado_civil' => $request->input('estado_civil'),
            'discapacidad_presente' => $request->input('discapacidad_presente'),
            'ultimo_grado_estudios' => $request->input('ultimo_grado_estudios'),
            'empresa_trabaja' => $request->input('empresa_trabaja'),
            'antiguedad' => $request->input('antiguedad') ,
            'direccion_empresa' => $request->input('direccion_empresa'),
            'sexo' => $request->input('generoaspirante'),
            'discapacidad_presente' => '',
            'etnia' => ''
        ]);

        //dd($alumno);

        $AlumnosPre = Alumnopre::create($validateData);
        $AlumnosPre->alumnos()->save($alumno);

        return redirect('/alumnos')->with('success', 'Nuevo Alumno Agregado Exitosamente!');
    }
    /**
     * formulario número 2
     */
    protected function createpaso2sid()
    {
        return view('layouts.pages.frminscripcion2');
    }

    public function pdf_registro()
    {
        $pdf = PDF::loadView('layouts.pdfpages.registroalumno');

        return $pdf->stream('registro.pdf');
    }
}
