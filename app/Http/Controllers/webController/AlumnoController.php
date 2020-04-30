<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Alumnopre;
use App\Models\Municipio;
use App\Models\Estado;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
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
        $alumnos = new Alumnopre();
        $retrieveAlumnos = $alumnos->all();
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
        $municipio = new Municipio();
        $estado = new Estado();
        $municipios = $municipio->all();
        $estados = $estado->all();
        return view('layouts.pages.sid', compact('municipios', 'estados'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $curp = strtoupper($request->input('curp'));
        $alumnoPre = Alumnopre::WHERE('curp', '=', $curp)->GET();
        if ($alumnoPre->isEmpty()) {
            # si la consulta no está vacía hacemos la inserción
            $validator =  Validator::make($request->all(), [
                'nombre' => 'required',
                'apellidoPaterno' => 'required',
                'apellidoMaterno' => 'required',
                'sexo' => 'required',
                'curp' => 'required',
                'fecha_nacimiento' => 'required',
                'telefono' => 'required',
                'domicilio' => 'required',
                'colonia' => 'required',
                'cp' => 'required',
                'estado' => 'required',
                'municipio' => 'required',
                'estado_civil' => 'required',
                'discapacidad' => 'required',
            ]);
            if ($validator->fails()) {
                # devolvemos un error
                return redirect('/alumnos/sid')
                        ->withErrors($validator)
                        ->withInput();
            } else {

                $AlumnoPreseleccion = new Alumnopre;
                $AlumnoPreseleccion->nombre = $request->nombre;
                $AlumnoPreseleccion->apellidoPaterno = $request->apellidoPaterno;
                $AlumnoPreseleccion->apellidoMaterno = $request->apellidoMaterno;
                $AlumnoPreseleccion->sexo = $request->sexo;
                $AlumnoPreseleccion->curp = $request->curp;
                $AlumnoPreseleccion->fecha_nacimiento = $AlumnoPreseleccion->setFechaNacAttribute($request->fecha_nacimiento);
                $AlumnoPreseleccion->telefono = $request->telefono;
                $AlumnoPreseleccion->domicilio = $request->domicilio;
                $AlumnoPreseleccion->colonia = $request->colonia;
                $AlumnoPreseleccion->cp = $request->cp;
                $AlumnoPreseleccion->estado = $request->estado;
                $AlumnoPreseleccion->municipio = $request->municipio;
                $AlumnoPreseleccion->estado_civil = $request->estado_civil;
                $AlumnoPreseleccion->discapacidad = $request->discapacidad;

                $AlumnoPreseleccion->save();
                // redireccionamos con un mensaje de éxito
                return redirect('alumnos/indice')->with('success', 'Nuevo Alumno Agregado Exitosamente!');
            }

        } else {
            # por el contrario si no está vacía mandamos un mensaje al usuario
            #Mensaje
            $mensaje = "Lo sentimos, la curp ".$curp." asociada a este registro ya se encuentra en la base de datos.";
            return redirect('/alumnos/sid')->withErrors($mensaje);
        }
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
