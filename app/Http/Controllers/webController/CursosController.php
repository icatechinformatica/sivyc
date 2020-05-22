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
use App\Models\Area;

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
        $area = new Area();
        $areas = $area->all();
        // mostramos el formulario de cursos
        return view('layouts.pages.frmcursos', compact('especialidades', 'areas'));
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



            $cursos = new curso;
            $cursos->nombre_curso = $request->nombrecurso;
            $cursos->modalidad = $request->modalidad;
            $cursos->horas = $request->horas;
            $cursos->clasificacion = $request->clasificacion;
            $cursos->costo = $request->costo;
            $cursos->duracion = $request->duracion;
            $cursos->objetivo = $request->objetivo;
            $cursos->perfil = $request->perfil;
            $cursos->fecha_validacion = $cursos->setFechaAttribute($request->fecha_validacion);
            $cursos->fecha_actualizacion = $cursos->setFechaAttribute($request->fecha_actualizacion);
            $cursos->descripcion = $request->descripcion;
            $cursos->no_convenio = $request->no_convenio;
            $cursos->id_especialidad = $request->especialidadCurso;
            $cursos->unidad_amovil = $request->unidad_accion_movil;
            $cursos->area = $request->areaCursos;
            $cursos->solicitud_autorizacion = (isset($request->solicitud_autorizacion)) ? $request->solicitud_autorizacion : false;
            $cursos->memo_actualizacion = $request->memo_actualizacion;
            $cursos->memo_validacion = $request->memo_validacion;
            $cursos->save();

            # ==================================
            # Aquí tenemos el id recién guardado
            # ==================================
            $cursosId = $cursos->id;

            // validamos si hay archivos
            if ($request->hasFile('documento_solicitud_autorizacion')) {
                # Carga el archivo y obtener la url
                $documento_solicitud_autorizacion = $request->file('documento_solicitud_autorizacion'); # obtenemos el archivo
                $url_solicitud_autorizacion = $this->uploaded_file($documento_solicitud_autorizacion, $cursosId, 'documento_solicitud_autorizacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoUpdate = curso::find($cursosId);
                $cursoUpdate->documento_solicitud_autorizacion = $url_solicitud_autorizacion;
                $cursoUpdate->save();
            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_validacion')) {
                # Carga el archivo y obtener la url
                $documento_memo_validacion = $request->file('documento_memo_validacion'); # obtenemos el archivo
                $url_memo_validacion = $this->uploaded_file($documento_memo_validacion, $cursosId, 'documento_memo_validacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoUp = curso::find($cursosId);
                $cursoUp->documento_memo_validacion = $url_memo_validacion;
                $cursoUp->save();
            }

            // validamos el siguiente archivo
            if ($request->hasFile('documento_memo_actualizacion')) {
                # Carga el archivo y obtener la url
                $documento_memo_actualizacion = $request->file('documento_memo_actualizacion'); # obtenemos el archivo
                $url_memo_actualizacion = $this->uploaded_file($documento_memo_actualizacion, $cursosId, 'documento_memo_actualizacion'); #invocamos el método
                // guardamos en la base de datos
                $cursoU = curso::find($cursosId);
                $cursoU->documento_memo_actualizacion = $url_memo_actualizacion;
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

    protected function get_by_area($idAreas)
    {
        if (isset($idAreas)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idAreas = $idAreas;
            $Especialidad = new especialidad();

            $Especialidades = $Especialidad->WHERE('id_areas', '=', $idAreas)->GET();

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($Especialidades);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id de Especialidad para filtar'));
        }

        return $json;
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
        $file->storeAs('/uploadFiles/cursos/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/cursos/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
