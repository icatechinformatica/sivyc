<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use App\Models\Alumnopre;
use App\Models\Municipio;
use App\Models\Estado;
use App\Models\especialidad;
use App\Models\curso;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use PDF;
use Carbon\Carbon;

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

    protected function show($id)
    {
        $AlumnoMatricula = new  Alumnopre;
        $Especialidad = new especialidad;
        $especialidades = $Especialidad->all();
        $Alumno = $AlumnoMatricula->findOrfail($id);
        return view('layouts.pages.sid_general', compact('Alumno', 'especialidades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->alumno_id;
        $AlumnosPre = Alumnopre::findOrfail($id); // encontrar el registro
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('acta_nacimiento')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'acta_nacimiento' => 'mimes:pdf|max:2048'
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $acta_nacimiento = $request->file('acta_nacimiento'); # obtenemos el archivo
                $url_acta_nacimiento = $this->uploaded_file($acta_nacimiento, $id, 'acta_nacimiento'); #invocamos el método
                $chk_acta_nacimiento = true;
            }
        } else {
            $chk_acta_nacimiento = false;
            $url_acta_nacimiento = '';
        }
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('copia_curp')) {
            # llamamos el método
            $validator = Validator::make($request->all(), [
                'copia_curp' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $curp = $request->file('copia_curp'); # obtenemos el archivo
                $url_curp = $this->uploaded_file($curp, $id, 'curp'); #invocamos el método
                $chk_curp = true;
            }
        } else {
            $chk_curp = false;
            $url_curp = '';
        }
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('comprobante_domicilio')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'comprobante_domicilio' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $comprobante_domicilio = $request->file('comprobante_domicilio'); # obtenemos el archivo
                $url_comprobante_domicilio = $this->uploaded_file($comprobante_domicilio, $id, 'comprobante_domicilio'); #invocamos el método
                $chk_comprobante_domicilio = true;
            }
        } else {
            $url_comprobante_domicilio = '';
            $chk_comprobante_domicilio = false;
        }
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('fotografias')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'fotografias' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $fotografia = $request->file('fotografias'); # obtenemos el archivo
                $url_fotografia = $this->uploaded_file($fotografia, $id, 'fotografia'); #invocamos el método
                $chk_fotografia = true;
            }
        } else {
            $url_fotografia = '';
            $chk_fotografia = false;
        }
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('ine')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'ine' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $ine = $request->file('ine'); # obtenemos el archivo
                $url_ine = $this->uploaded_file($ine, $id, 'ine'); #invocamos el método
                $chk_ine = true;
            }
        } else {
            $chk_ine = false;
            $url_ine = '';
        }
        /**
          * checar si hay un documento para poder llamar el método
        */
        if ($request->hasFile('licencia_manejo')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'licencia_manejo' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $licencia_manejo = $request->file('licencia_manejo'); # obtenemos el archivo
                $url_licencia_manejo = $this->uploaded_file($licencia_manejo, $id, 'licencia_manejo'); #invocamos el método
                $chk_licencia_manejo = true;
            }
        } else {
            $chk_licencia_manejo = false;
            $url_licencia_manejo = '';
        }
        /**
         *
         */
        if ($request->hasFile('grado_estudios')) {
            # llamamos al método
            $validator = Validator::make($request->all(), [
                'grado_estudios' => 'mimes:pdf|max:2048',
            ]);
            if ($validator->fails()) {
                # code...
                return redirect('alumnos/sid-paso2/'.$id)
                        ->withErrors($validator);
            } else {
                $grado_estudios = $request->file('grado_estudios'); # obtenemos el archivo
                $url_grado_estudios = $this->uploaded_file($grado_estudios, $id, 'grado_estudios'); #invocamos el método
                $chk_ultimo_grado_estudios = true;
            }
        } else {
            $chk_ultimo_grado_estudios = false;
            $url_grado_estudios = '';
        }

        /**
         * obtener el año correcto
         */
        $date = Carbon::now();
        $anio = $date->format('Y');

        /**
         * funcion alumnos
         */
        $alumno = new Alumno([
            'no_control' => '231ABC',
            'id_especialidad' => $request->input('especialidad'),
            'id_curso' => $request->input('cursos_sid'),
            'horario' => $request->input('horario'),
            'grupo' => $request->input('grupo'),
            'ultimo_grado_estudios' => $request->input('grado_estudios'),
            'empresa_trabaja' => $request->input('empresa'),
            'antiguedad' => $request->input('antiguedad'),
            'direccion_empresa' => $request->input('direccion_empresa'),
            'medio_entero' => ($request->input('medio_entero') === 0) ? $request->input('medio_entero_especificar') : $request->input('medio_entero'),
            'sistema_capacitacion_especificar' => ($request->input('motivos_eleccion_sistema_capacitacion') === 0) ? $request->input('sistema_capacitacion_especificar') : $request->input('motivos_eleccion_sistema_capacitacion'),
            'chk_acta_nacimiento' => $chk_acta_nacimiento,
            'acta_nacimiento' => $url_acta_nacimiento,
            'chk_curp' => $chk_curp,
            'curp' => $url_curp,
            'chk_comprobante_domicilio' => $chk_comprobante_domicilio,
            'comprobante_domicilio' => $url_comprobante_domicilio,
            'chk_fotografia' => $chk_fotografia,
            'fotografia' => $url_fotografia,
            'chk_ine' => $chk_ine,
            'ine' => $url_ine,
            'chk_pasaporte_licencia' => $chk_licencia_manejo,
            'pasaporte_licencia_manejo' => $url_licencia_manejo,
            'chk_comprobante_ultimo_grado' => $chk_ultimo_grado_estudios,
            'comprobante_ultimo_grado' => $url_grado_estudios,
            'puesto_empresa' => $request->input('puesto_empresa'),
            'unidad' => Auth::user()->unidades()->first()->unidad
        ]);

        $AlumnosPre->alumnos()->save($alumno);

        dd('Listo');
    }

    protected function getcursos(Request $request)
    {
        if (isset($request->idEsp)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idEspecialidad = $request->idEsp;
            $Curso = new curso();

            $Cursos = $Curso->WHERE('id_especialidad', '=', $idEspecialidad)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($Cursos);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
    }

    protected function uploaded_file($file, $id, $name)
    {
        $tamanio = $file->getClientSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/alumnos/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/alumnos/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
