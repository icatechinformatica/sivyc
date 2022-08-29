<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\api\Curso;
use App\Models\api\Calificacion;
use App\Models\api\Inscripcion;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $Curso= new Curso();
        $cursos = $Curso->all();
        return response()->json($cursos, 200);
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
       /* try {
            

                # enviar o generar codigo que si funciona
                $Curso= new Curso();
                $Curso->id = $request->id;
                $Curso->cct = $request->cct;
                $Curso->unidad = $request->unidad;
                $Curso->nombre = $request->nombre;
                $Curso->curp = $request->curp;
                $Curso->rfc = $request->rfc;
                $Curso->clave = $request->clave;
                $Curso->mvalida = $request->mvalida;
                $Curso->mod = $request->mod;
                $Curso->area = $request->area;
                $Curso->espe = $request->espe;
                $Curso->curso = $request->curso;
                $Curso->inicio = $request->inicio;
                $Curso->termino = $request->termino;
                $Curso->dia = $request->dia;
                $Curso->dura = $request->dura;
                $Curso->hini = $request->hini;
                $Curso->hfin = $request->hfin;
                $Curso->horas = $request->horas;
                $Curso->ciclo = $request->ciclo;
                $Curso->plantel = $request->plantel;
                $Curso->depen = $request->depen;
                $Curso->muni = $request->muni;
                $Curso->sector = $request->sector;
                $Curso->programa = $request->programa;
                $Curso->nota = $request->nota;
                $Curso->munidad = $request->munidad;
                $Curso->efisico = $request->efisico;
                $Curso->cespecifico = $request->cespecifico;
                $Curso->mpaqueteria = $request->mpaqueteria;
                $Curso->mexoneracion = $request->mexoneracion;
                $Curso->hombre = $request->hombre;
                $Curso->mujer = $request->mujer;
                $Curso->tipo = $request->tipo;
                $Curso->fcespe = $request->fcespe;
                $Curso->cgeneral = $request->cgeneral;
                $Curso->fcgen = $request->fcgen;
                $Curso->opcion = $request->opcion;
                $Curso->motivo = $request->motivo;
                $Curso->cp = $request->cp;
                $Curso->ze = $request->ze;
                $Curso->id_curso = $request->id_curso;
                $Curso->id_instructor = $request->id_instructor;
                $Curso->modinstructor = $request->modinstructor;
                $Curso->nmunidad = $request->nmunidad;
                $Curso->nmacademico = $request->nmacademico;
                $Curso->observaciones = $request->observaciones;
                $Curso->status = $request->status;
                $Curso->realizo = $request->realizo;
                $Curso->valido = $request->valido;
                $Curso->arc = $request->arc;
                $Curso->tcapacitacion = $request->tcapacitacion;
                $Curso->fecha_apertura = $request->fecha_apertura;
                $Curso->fecha_modificacion = $request->fecha_modificacion;
                $Curso->costo = $request->costo;
                $Curso->motivo_correccion = $request->motivo_correccion;
                $Curso->status_curso = $request->status_curso;
                $Curso->save();

                #==================================
                # Aquí tenemos el id recién guardado
                #==================================
                $cursosId = $Curso->id;

                // validamos si hay archivos
                if ($request->hasFile('pdf_curso')) {
                    # Carga el archivo y obtener la url
                    $documento_pdf_curso = $request->file('pdf_curso'); # obtenemos el archivo
                    $url_documento_pdf_curso = $this->uploaded_file($documento_solicitud_autorizacion, $cursosId, 'pdf_curso'); #invocamos el método
                    // guardamos en la base de datos
                    $cursoUpdate = Curso::findOrfail($cursosId);
                    $cursoUpdate->pdf_curso = $url_documento_pdf_curso;
                    $cursoUpdate->save();
                }

                return response()->json(['success' => 'Curso se cargo exitosamente en la base de datos'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }*/
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
        // nueva edicion en el controlador del api
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
        // actualizando
        try {
            //return response()->json($request->all(), 200);
            //exit;
            //$Cursos= new Curso();
            //$cursosArray =

                DB::table('tbl_cursos')->where('id', $id)->update(
                [
                /*'cct' => trim($request->cct),
                'unidad' => trim($request->unidad),
                'nombre' => trim($request->nombre),
                'curp' => trim($request->curp),
                'rfc' => trim($request->rfc),*/
                'clave' => trim($request->clave),
                'mvalida' => trim($request->mvalida),
                /*'mod' => trim($request->mod),
                'area' => trim($request->area),
                'espe' => trim($request->espe),
                'curso' => trim($request->curso),
                'inicio' => trim($request->inicio),
                'termino' => trim($request->termino),
                'dia' => trim($request->dia),
                'dura' => trim($request->dura),
                'hini' => trim($request->hini),
                'hfin' => trim($request->hfin),
                'horas' => trim($request->horas),
                'ciclo' => trim($request->ciclo),
                'plantel' => trim($request->plantel),
                'depen' => trim($request->depen),
                'muni' => trim($request->muni),
                'sector' => trim($request->sector),
                'programa' => trim($request->programa),
                'nota' => trim($request->nota),
                'munidad' => trim($request->munidad),
                'efisico' => trim($request->efisico),
                'cespecifico' => trim($request->cespecifico),
                'mpaqueteria' => trim($request->mpaqueteria),
                'mexoneracion' => trim($request->mexoneracion),
                'hombre' => trim($request->hombre),
                'mujer' => trim($request->mujer),
                'tipo' => trim($request->tipo),
                'fcespe' => trim($request->fcespe),
                'cgeneral' => trim($request->cgeneral),
                'fcgen' => trim($request->fcgen),
                'opcion' => trim($request->opcion),
                'motivo' => trim($request->motivo),
                'cp' => trim($request->cp),
                'ze' => trim($request->ze),
                'id_curso' => trim($request->id_curso),
                'id_instructor' => trim($request->id_instructor),
                'modinstructor' => trim($request->modinstructor),
                'nmunidad' => trim($request->nmunidad),*/
                'nmacademico' => trim($request->nmacademico),
               // 'observaciones' => trim($request->observaciones),
               // 'status' => trim($request->status),
               // 'realizo' => trim($request->realizo),
                'valido' => trim($request->valido),
               // 'arc' => trim($request->arc),
              // 'tcapacitacion' => trim($request->tcapacitacion),
               'fecha_apertura' => $request->fecha_apertura,
                'fecha_modificacion' => $request->fecha_modificacion,
               // 'costo' => trim($request->costo),
               // 'motivo_correccion' => trim($request->motivo_correccion),
                'status_curso' => trim($request->status_curso)
            ]);
            //$Cursos->WHERE('id', $id)->update($cursosArray);

            // validamos si hay archivos
            if ($request->hasFile('pdf_curso')) {
                // obtenemos el valor de pdf_curso
                $cursos_pdf = new Curso();
                $cursoPdf = $cursos_pdf->WHERE('id', '=' , $id)->GET();
                // checamos que no sea nulo
                if (!is_null($cursoPdf[0]->pdf_curso)) {
                    # si no está nulo
                    $docPdfCurso = explode("/",$cursoPdf[0]->pdf_curso, 5);
                    //dd($docPdfCurso[4]);
                    //dd(Storage::exists($docPdfCurso[4]));
                    if (Storage::exists($docPdfCurso[4])) {
                        # checamos si hay un documento de ser así procedemos a eliminarlo
                        Storage::delete($docPdfCurso[4]);
                    }
                }

                # Carga el archivo y obtener la url
                $pdf_curso = $request->file('pdf_curso'); # obtenemos el archivo
                $url_pdf_curso = $this->uploaded_file($pdf_curso, $id, 'pdf_curso_update'); #invocamos el método
                // guardamos en la base de datos
                $cursoUpdate = Curso::findOrfail($id);
                $cursoUpdate->update([
                    'pdf_curso' => $url_pdf_curso
                ]);
            }

            return response()->json(['success' => 'Curso actualizado exitosamente'], 200);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
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

    public function updateCursosCalificaciones(Request $request, $id){
        try {
            //modificaciones
            $Cursos= new Curso();
            $Cursos->whereId($id)->update($request->all());
            // parte de calificaciones
            $Calificacion = new Calificacion();
            $Calificacion->WHERE('idcurso', $id)->update([
                'instructor' => $request->instructor,
                'idgrupo' => $request->idgrupo,
                'espe' => $request->espe,
                'curso' => $request->curso,
                'mod' => $request->mod,
                'inicio' => $request->inicio,
                'termino' => $request->termino,
                'hini' => $request->hini,
                'hfin' => $request->hfin,
                'dura' => $request->dura
            ]);
            // parte de inscripciones
            $Inscripcion = new Inscripcion();
            $Inscripcion->WHERE('id_curso', $id)->update([
                'instructor' => $request->instructor,
                'curso' => $request->curso,
                'inicio' => $request->inicio,
                'termino' => $request->termino,
                'hinicio' => $request->hinicio,
                'hfin' => $request->hfin,
                'munidad' => $request->munidad
            ]);
            return response()->json(['success' => 'Curso actualizado exitosamente'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 501);
        }
    }

    /***
     * funcion para subir un archivo al servidor
     */
    protected function uploaded_file($file, $id, $name){
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."_".date('YmdHis')."_".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/cursosvalidados/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/cursosvalidados/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
