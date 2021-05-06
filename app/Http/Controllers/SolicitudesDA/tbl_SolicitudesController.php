<?php

namespace App\Http\Controllers\SolicitudesDA;
use App\Http\Controllers\Controller;
use App\Models\tbl_Solicitudes;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class tbl_SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->num_solicitud != null) {
            $curso = null;
            $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $request->num_solicitud)
                ->leftJoin('tbl_cursos', 'tbl_solicitudes.id_curso', '=', 'tbl_cursos.id')
                ->select('tbl_solicitudes.*','tbl_solicitudes.id as id_solicitud', 'tbl_cursos.*')
                ->get();
            return view('layouts.pages.solicitudesDA.vtaModificacionCurso', compact('solicitud', 'curso'));
        } else {
            $solicitud = null;
            $curso = DB::table('tbl_cursos')->where('tbl_cursos.clave', '=', $request->busqueda_curso)->get();
            return view('layouts.pages.solicitudesDA.vtaModificacionCurso', compact('curso', 'solicitud'));
        }  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        try {
            $solicitudes = new tbl_Solicitudes();
            $solicitudes->tipo_solicitud = 'Modificación de curso';
        
            $solicitudes->num_solicitud = $request->num_solicitud;
            $solicitudes->fecha_solicitud = $request->fecha_solicitud;
            $solicitudes->id_curso = $request->id_curso;
            $solicitudes->opcion_solicitud = $request->opcion_solicitud;
            $solicitudes->obs_solicitud = $request->obs_solicitud;
            $solicitudes->iduser_created = Auth::user()->id;

            $solicitudes->status = 'INICIADO';
            $solicitudes->turnado = 'UNIDAD';
            $solicitudes->save();
        } catch (QueryException $ex) {
            return 'duplicado';
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
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
        tbl_Solicitudes::destroy($id);
        return redirect()->route('solicitudesDA.inicio');
    }

    public function tablaSolicitud_pdf($id) {
        $solicitud = DB::table('tbl_solicitudes')->where('num_solicitud', '=', $id)
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

        $pdf = PDF::loadView('layouts.pdfpages.solicitudModificarCurso', compact('solicitud', 'para'));
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
        $archivo_solicitud = $request->file('archivo_solicitud'); # obtenemos el archivo
        $url_archivo_solicitud = $this->uploaded_file($archivo_solicitud, $request->idSolicitud, 'archivo_solicitud'); #invocamos el método
        tbl_Solicitudes::where('num_solicitud', '=', $request->num_solicitud)
            ->update([
                'archivo_solicitud' => $url_archivo_solicitud,
                'status' => 'TURNADO',
                'turnado' => 'DTA'
            ]);
        
        return redirect()->route('solicitudesDA.inicio')->with('success', 'SOLICITUD ENVIADA EXITOSAMENTE');
    }

    protected function uploaded_file($file, $id, $name) {
        $tamanio = $file->getSize(); #obtener el tamaño del archivo del cliente
        $extensionFile = $file->getClientOriginalExtension(); // extension de la imagen
        # nuevo nombre del archivo
        $documentFile = trim($name."".date('YmdHis')."".$id.".".$extensionFile);
        $file->storeAs('/uploadFiles/solicitud_modificacion_curso/'.$id, $documentFile); // guardamos el archivo en la carpeta storage
        $documentUrl = Storage::url('/uploadFiles/solicitud_modificacion_curso/'.$id."/".$documentFile); // obtenemos la url donde se encuentra el archivo almacenado en el servidor.
        return $documentUrl;
    }
}
