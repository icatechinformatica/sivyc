<?php
/**
 * Elaborado por Daniel Méndez Cruz v.1.0
 */
namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\curso;
use App\Models\especialidad;
class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = curso::SELECT('cursos.id', 'cursos.nombre_curso', 'cursos.modalidad', 'cursos.horas', 'cursos.horas', 'cursos.clasificacion', 'cursos.costo',
        'cursos.duracion', 'cursos.objetivo', 'cursos.perfil', 'cursos.solicitud_autorizacion',
        'cursos.fecha_validacion', 'cursos.memo_validacion', 'cursos.memo_actualizacion', 'cursos.fecha_actualizacion', 'cursos.unidad_amovil', 'especialidades.nombre')
        ->WHERE('cursos.id', '!=', '0')
        ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')->GET();
        return view('layouts.pages.vstacursosinicio',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $especialidad = new especialidad();
        $especialidades = $especialidad->all();
        // mostramos el formulario de cursos
        return view('layouts.pages.frmcursos', compact('especialidades'));
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
        try {
            //validación de archivos
            $validator = Validator::make($request->all(), [
                'solicitud_autorizacion' => 'required|mimes:pdf|max:2048',
                'memo_validacion' => 'required|mimes:pdf|max:2048',
                'memo_actualizacion' => 'required|mimes:pdf|max:2048'
            ]);

            if ($validador->fails()) {
                return redirect()->route('frm-cursos')
                            ->withErrors($validador)
                            ->withInput();
            }


            $cursos = new curso();
            $cursos->especialidad = $request->especialidad;
            $cursos->nombre_curso = $request->nombre_curso;
            $cursos->modalidad = $request->modalidad;
            $cursos->horas = $request->horas;
            $cursos->clasificacion = $request->clasificacion;
            $cursos->costo = $request->costo;
            $cursos->duracion = $request->duracion;
            $cursos->objetivo = $request->objetivo;
            $cursos->perfil = $request->perfil;
            $cursos->fecha_validacion = $request->fecha_validacion;
            $cursos->fecha_actualizacion = $request->fecha_actualizacion;
            $cursos->descripcion = $request->descripcion;
            $cursos->no_convenio = $request->no_convenio;
            $cursos->id_especialidad = $request->id_especialidad;
            $cursos->save();

            # ==================================
            # Aquí tenemos el id recién guardado
            # ==================================
            $cursosId = $cursos->id;

            // validamos si hay archivos
            if ($request->hasFile('solicitud_autorizacion')) {
                # Carga el archivo y obtener la url
                $solicitud_autorizacion = $request->file('solicitud_autorizacion'); # obtenemos el archivo
                $url_solicitud_autorizacion = $this->uploaded_file($solicitud_autorizacion, $cursosId, 'solicitud_autorizacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoUpdate = curso::find($cursosId);
                $cursoUpdate->solicitud_autorizacion = $url_solicitud_autorizacion;
                $cursoUpdate->save();
            }

            // validamos el siguiente archivo
            if ($request->hasFile('memo_validacion')) {
                # Carga el archivo y obtener la url
                $memo_validacion = $request->file('memo_validacion'); # obtenemos el archivo
                $url_memo_validacion = $this->uploaded_file($memo_validacion, $cursosId, 'memo_validacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoUp = curso::find($cursosId);
                $cursoUp->memo_validacion = $url_memo_validacion;
                $cursoUp->save();
            }

            // validamos el siguiente archivo
            if ($request->hasFile('memo_actualizacion')) {
                # Carga el archivo y obtener la url
                $memo_actualizacion = $request->file('memo_actualizacion'); # obtenemos el archivo
                $url_memo_actualizacion = $this->uploaded_file($memo_actualizacion, $cursosId, 'memo_actualizacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoU = curso::find($cursosId);
                $cursoU->memo_actualizacion = $url_memo_actualizacion;
                $cursoU->save();
            }

            return redirect()->route('curso-inicio')->with('success', 'Nuevo Curso Agregado!');
        } catch (Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }
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
