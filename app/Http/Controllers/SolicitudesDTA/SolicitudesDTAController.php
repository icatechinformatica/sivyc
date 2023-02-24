<?php

namespace App\Http\Controllers\SolicitudesDTA;

use App\Http\Controllers\Controller;
use App\Models\tbl_curso;
use App\Models\tbl_Solicitudes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Agenda;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class SolicitudesDTAController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $request->num_solicitud)
                ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
                ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_solicitudes.status as statusSoli', 'tbl_cursos.*')
                ->get();
        return view('layouts.pages.solicitudesDTA.ModificacionCurso', compact('solicitud'));
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
    public function show($id) {
        $curso = DB::table('tbl_solicitudes')->where('tbl_solicitudes.id', '=', $id)
                ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
                ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_cursos.*')
                ->get();
        return view('layouts.pages.solicitudesDTA.showCursoSolicitud', compact('curso'));
    }


    public function showModify($id) {
        $curso = DB::table('tbl_solicitudes')->where('tbl_solicitudes.id', '=', $id)
                ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
                ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_cursos.*')
                ->get();
        $instructores = DB::table('instructores')->get();
        return view('layouts.pages.solicitudesDTA.showCursoSolicitudModify', compact('curso', 'instructores'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        Agenda::destroy($id);
        return response()->json($id);
    }

    public function cancelar(Request $request) {
        tbl_curso::where('id', '=', $request->idCurso)
            ->update([
                'status' => 'CANCELADO',
                'status_curso' => 'CANCELADO'
            ]);
        tbl_Solicitudes::where('id', '=', $request->id_solicitud)
            ->update([
                'num_respuesta' => $request->num_respuesta,
                'fecha_respuesta' => $request->fecha_respuesta,
                'obs_respuesta' => $request->observaciones,
                'status' => 'ATENDIDO'
            ]);
            
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $request->numSolicitud)
            ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
            ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_solicitudes.status as statusSoli', 'tbl_cursos.*')
            ->get();
        
        return view('layouts.pages.solicitudesDTA.ModificacionCurso', compact('solicitud'))->with('success', 'CURSO CANCELADO EXITOSAMENTE');
    }

    public function noProcede(Request $request) {
        tbl_Solicitudes::where('id', '=', $request->id_solicitudNo)
            ->update([
                'num_respuesta' => $request->num_respuestaNo,
                'fecha_respuesta' => $request->fecha_respuestaNo,
                'obs_respuesta' => $request->observacionesNo,
                'status' => 'NO PROCEDE'
            ]);
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $request->numSolicitudNo)
            ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
            ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_solicitudes.status as statusSoli', 'tbl_cursos.*')
            ->get();
        
        return view('layouts.pages.solicitudesDTA.ModificacionCurso', compact('solicitud'))->with('success', 'SE HA REGISTRADO QUE EL CURSO NO PROCEDE EXITOSAMENTE');
    }

    public function saveCambios(Request $request) {
        tbl_Solicitudes::where('id', '=', $request->id_solicitud)
            ->update([
                'num_respuesta' => $request->num_respuesta,
                'fecha_respuesta' => $request->fecha_respuesta,
                'obs_respuesta' => $request->observacionesRes,
                'status' => 'ATENDIDO'
            ]);
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $request->numSolicitud)
            ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
            ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_solicitudes.status as statusSoli', 'tbl_cursos.*')
            ->get();
        
        return view('layouts.pages.solicitudesDTA.ModificacionCurso', compact('solicitud'))->with('success', 'CURSO MODIFICADO EXITOSAMENTE');
    }

    public function tablaSolicitud_pdf($numSolicitud) {
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $numSolicitud)
            ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
            ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_cursos.*')
            ->get();

        // obtener nombre de para
        $para = DB::table('directorio')
            ->join('area_adscripcion', function($join) {
                $join->on('directorio.area_adscripcion_id', '=', 'area_adscripcion.id')
                ->where('area_adscripcion.id', '=', 16);// cambiarlo por id
            })->select('directorio.*')->get();

        $usuarioUnidad = Auth::user()->unidad;

        $pdf = PDF::loadView('layouts.pdfpages.solicitudModificarCursoRespuesta', compact('solicitud', 'para'));
        $pdf->setPaper('A4', 'Landscape');

        return $pdf->stream('download.pdf');
    }

    public function storeSolicitud(Request $request) {
        $solicitud = new tbl_Solicitudes();
        $getSolicitud = $solicitud->where('id', '=', $request->idSolicitud)->get();

        if (!is_null($getSolicitud[0]->archivo_solicitud)) {
            $docSolicitud = explode("/", $getSolicitud[0]->archivo_solicitud, 5);
            if (Storage::exists($docSolicitud[4])) {
                # checamos si hay un documento de ser así procedemos a eliminarlo
                Storage::delete($docSolicitud[4]);
            }
        }
        $archivo_solicitud = $request->file('archivo_respuesta'); # obtenemos el archivo
        $url_archivo_solicitud = $this->uploaded_file($archivo_solicitud, $request->idSolicitud, 'archivo_respuesta'); #invocamos el método
        tbl_Solicitudes::where('num_solicitud', '=', $request->num_solicitud)
            ->update([
                'archivo_respuesta' => $url_archivo_solicitud
                // 'status' => 'TURNADO',
                // 'turnado' => 'DTA'
            ]);
        
        return redirect()->route('solicitudesDTA.inicio')->with('success', 'RESPUESTA ENVIADA EXITOSAMENTE');
    }

    protected function uploaded_file($file, $id, $name) {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."".date('YmdHis')."".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/respuesta_modificacion_curso/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/respuesta_modificacion_curso/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }

    public function storeEvents(Request $request) {
        $isEquals = false;
        $isEquals2 = false;
        $data['events'] = Agenda::where('id_instructor', '=', $request->id_instructor)->get();
        // return response()->json($data['events']);
                // ->where('start', '<=',  $request->start)
                // ->where('end', '>=', $request->start)->get();
        $fechaInicio = Carbon::parse($request->start)->format('d-m-Y');
        $fechaTermino = Carbon::parse($request->end)->format('d-m-Y');
        foreach($data['events'] as $evento) {
            if ($fechaInicio >= Carbon::parse($evento->start)->format('d-m-Y') && $fechaInicio <= Carbon::parse($evento->end)->format('d-m-Y')) {
                if (Carbon::parse($request->start)->format('H:i') >= Carbon::parse($evento->start)->format('H:i')
                    && Carbon::parse($request->start)->format('H:i') <= Carbon::parse($evento->end)->format('H:i')) {
                    $isEquals = true;
                }
            }
            // 
            if ($fechaTermino >= Carbon::parse($evento->start)->format('d-m-Y') && $fechaTermino <= Carbon::parse($evento->end)->format('d-m-Y')) {
                if (Carbon::parse($request->end)->format('H:i') >= Carbon::parse($evento->start)->format('H:i')
                    && Carbon::parse($request->end)->format('H:i') <= Carbon::parse($evento->end)->format('H:i')) {
                    $isEquals2 = true;
                }
            }
        }

        if ($isEquals) {
            return 'iguales';
        } else if ($isEquals2) {
            return 'iguales2';
        } else {
            try {
                // $idCurso = 9;
                $idGrupo = 1;
                $titulo = 'curso 9';

                $agenda = new Agenda();
                $agenda->title = $titulo;
                $agenda->start = $request->start;
                $agenda->end = $request->end;
                $agenda->textColor = $request->textColor;
                $agenda->observaciones = $request->observaciones;
                $agenda->id_curso = $request->idCurso;
                $agenda->id_instructor = $request->id_instructor;
                $agenda->id_grupo = $idGrupo;
                $agenda->iduser_created = Auth::user()->id;
                $agenda->save();

                if ($request->isEquals == 'false') { //cambio de instructor, eliminamos los registros del antiguo instructor para este curso
                    DB::table('agenda')->where('id_instructor', '=', $request->idIstructor)
                                        ->where('id_curso', '=', $request->idCurso)->delete();
                    tbl_curso::where('id', '=', $request->idCurso) // actualizamos en el curso de la tabla tbl_cursos
                            ->update(['id_instructor'=> $request->id_instructor]);
                }

            } catch(QueryException $ex) {
                return 'duplicado'; 
            }
        }
    }

    public function showEvents($id) {
        $data['agenda'] =  Agenda::where('id_instructor', '=', $id)->get();
        return response()->json($data['agenda']);
    }

    public function updateEvents(Request $request, $id) {

        $isEquals = false;
        $isEquals2 = false;
        $data['events'] = Agenda::where('id_instructor', '=', $request->id_instructor)->get();
                // ->whereNotIn('id', [$id])
                // ->where('start', '<=',  $request->start)
                // ->where('end', '>=', $request->start)->get();

        $fechaInicio = Carbon::parse($request->start)->format('d-m-Y');
        $fechaTermino = Carbon::parse($request->end)->format('d-m-Y');
        foreach($data['events'] as $evento) {
            if ($fechaInicio >= Carbon::parse($evento->start)->format('d-m-Y') && $fechaInicio <= Carbon::parse($evento->end)->format('d-m-Y')) {
                if (Carbon::parse($request->start)->format('H:i') >= Carbon::parse($evento->start)->format('H:i')
                    && Carbon::parse($request->start)->format('H:i') <= Carbon::parse($evento->end)->format('H:i')) {
                    if ($evento->id != $id) {
                        $isEquals = true;
                    }
                }
            }
            // 
            if ($fechaTermino >= Carbon::parse($evento->start)->format('d-m-Y') && $fechaTermino <= Carbon::parse($evento->end)->format('d-m-Y')) {
                if (Carbon::parse($request->end)->format('H:i') >= Carbon::parse($evento->start)->format('H:i')
                    && Carbon::parse($request->end)->format('H:i') <= Carbon::parse($evento->end)->format('H:i')) {
                    if ($evento->id != $id) {
                        $isEquals2 = true;
                    }
                }
            }
        }

        if ($isEquals) {
            return 'iguales';
        } if ($isEquals2) {
            return 'iguales2';
        } else {
            try {
                // $idCurso = 5;
                $idGrupo = 1;
                $titulo = 'curso 1';
    
                $agenda = Agenda::find($id);
    
                $agenda->title = $titulo;
                $agenda->start = $request->start;
                $agenda->end = $request->end;
                $agenda->textColor = $request->textColor;
                $agenda->observaciones = $request->observaciones;
                $agenda->id_curso = $request->idCurso;
                $agenda->id_instructor = $request->id_instructor;
                $agenda->id_grupo = $idGrupo;
                $agenda->iduser_updated = Auth::user()->id;
    
                $agenda->save();
            } catch(QueryException $ex) {
                return 'duplicado'; 
            }
        }
    }
}
