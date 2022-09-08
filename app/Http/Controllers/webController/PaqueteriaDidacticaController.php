<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\curso;
use App\Models\ImgPaqueterias;
use App\Models\PaqueteriasDidacticas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaqueteriaDidacticaController extends Controller
{
    //
    public function index($idCurso)
    {
        $curso = new curso();
        $curso=$curso::SELECT('cursos.id','cursos.estado','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
            'cursos.costo','cursos.duracion','cursos.tipo_curso','cursos.documento_memo_validacion','cursos.documento_memo_actualizacion','cursos.documento_solicitud_autorizacion',
            'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
            'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
            'especialidades.nombre AS especialidad', 'cursos.id_especialidad',
            'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria', 'cursos.documento_memo_validacion',
            'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
            'cursos.rango_criterio_pago_minimo', 'rango_criterio_pago_maximo','cursos.observacion',
            'cursos.grupo_vulnerable', 'cursos.dependencia')
            ->WHERE('cursos.id', '=', $idCurso)
            ->WHERE('cursos.id', '=', $idCurso)
            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->first();
        $area = Area::find($curso->area);

        $cartaDescriptiva = [
            'nombrecurso' => $curso->nombre_curso,
            'entidadfederativa' => '',
            'cicloescolar' => '',
            'programaestrategico' => '',
            'modalidad' => $curso->modalidad,
            'tipo' => $curso->tipo_curso,
            'perfilidoneo' => $curso->perfil,
            'duracion' => $curso->horas,
            'formacionlaboral' => $area->formacion_profesional,
            'especialidad' => $curso->especialidad,
            'publico' => '',
            'aprendizajeesperado' => '',
            'criterio' => '',
            'ponderacion' => '',
            'objetivoespecifico' => '',
            'transversabilidad' => '',
            'contenidoTematico' => '',
            'observaciones' => '',
            'elementoapoyo' => '',
            'auxenseñanza' => '',
            'referencias' => '',
        ];

        DB::beginTransaction();
        try { //se guarda la informacion inicial de la paqueteria
            $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1]])->first();

            if (!isset($paqueteriasDidacticas)) {
                PaqueteriasDidacticas::create([
                    'id_curso' => $idCurso,
                    'carta_descriptiva' => json_encode($cartaDescriptiva),
                    'eval_alumno' => json_encode(''),
                    'estatus' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => null,
                    'id_user_created' => Auth::id(),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            throw $e;
            DB::rollback();
            return redirect()->route('curso-inicio')->with('error', 'HUBO UN ERROR INESPERADO!');
        }
        $cartaDescriptiva = [];
        $contenidoT = [];
        $evaluacionAlumno = [];
        if (isset($paqueteriasDidacticas)) {
            $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);
            $contenidoT = json_decode($cartaDescriptiva->contenidoTematico);
            $evaluacionAlumno = ($paqueteriasDidacticas->eval_alumno);
        }
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas', compact('idCurso', 'curso', 'area', 'paqueteriasDidacticas', 'cartaDescriptiva', 'contenidoT', 'evaluacionAlumno'));
    }

    public function store(Request $request, $idCurso)
    {



        $urlImagenes = [];
        $preguntas = ['instrucciones' => $request->instrucciones];

        $curso = new curso();
        $curso=$curso::SELECT('cursos.id','cursos.estado','cursos.nombre_curso','cursos.modalidad','cursos.horas','cursos.clasificacion',
            'cursos.costo','cursos.duracion','cursos.tipo_curso','cursos.documento_memo_validacion','cursos.documento_memo_actualizacion','cursos.documento_solicitud_autorizacion',
            'cursos.objetivo','cursos.perfil','cursos.solicitud_autorizacion','cursos.fecha_validacion','cursos.memo_validacion',
            'cursos.memo_actualizacion','cursos.fecha_actualizacion','cursos.unidad_amovil','cursos.descripcion','cursos.no_convenio',
            'especialidades.nombre AS especialidad', 'cursos.id_especialidad',
            'cursos.area', 'cursos.cambios_especialidad', 'cursos.nivel_estudio', 'cursos.categoria', 'cursos.documento_memo_validacion',
            'cursos.documento_memo_actualizacion', 'cursos.documento_solicitud_autorizacion',
            'cursos.rango_criterio_pago_minimo', 'rango_criterio_pago_maximo','cursos.observacion',
            'cursos.grupo_vulnerable', 'cursos.dependencia')
            ->WHERE('cursos.id', '=', $idCurso)
            ->WHERE('cursos.id', '=', $idCurso)
            ->LEFTJOIN('especialidades', 'especialidades.id', '=', 'cursos.id_especialidad')
            ->first();
        $area = Area::find($curso->area);
        $cartaDescriptiva = [
            'nombrecurso' => $curso->nombre_curso,
            'entidadfederativa' => $request->entidadfederativa,
            'cicloescolar' => $request->cicloescolar,
            'programaestrategico' => $request->programaestrategico,
            'modalidad' => $curso->modalidad,
            'tipo' => $request->tipo,
            'perfilidoneo' => $curso->perfil,
            'duracion' => $curso->horas,
            'formacionlaboral' => $area->formacion_profesional,
            'especialidad' => $curso->especialidad,
            'publico' => $request->publico,
            'aprendizajeesperado' => $request->aprendizajeesperado,
            'criterio' => $request->criterio,
            'ponderacion' => $request->ponderacion,
            'objetivoespecifico' => $request->objetivoespecifico,
            'transversabilidad' => $request->transversabilidad,
            'contenidoTematico' => $request->contenidoT,
            'observaciones' => $request->observaciones,
            'elementoapoyo' => $request->elementoapoyo,
            'auxenseñanza' => $request->auxenseñanza,
            'referencias' => $request->referencias,
        ];


        $i = 0;

        $contPreguntas = 0;

        $auxContPreguntas = $request->numPreguntas;

        while (true) { //ciclo para encontrar las preguntas del formulario
            $i++;
            if ($contPreguntas == $auxContPreguntas)
                break;

            $numPregunta = 'pregunta' . $i;
            $tipoPregunta = 'pregunta' . $i . '-tipo';
            $opcPregunta = 'pregunta' . $i . '-opc';
            $respuesta = 'pregunta' . $i . '-opc-answer';

            $contenidoT = 'pregunta' . $i . '-contenidoT';

            if($request->$numPregunta === null)
                return redirect()->route('paqueteriasDidacticas', $idCurso)->with('warning', 'NO SE PUEDEN GUARDAR LA PREGUNTAS VACIAS!');

            if ($request->$numPregunta != null || $request->numPreguntas == 1) {

                if ($request->$tipoPregunta == 'multiple') {
                    $tempPregunta = [
                        'descripcion' => $request->$numPregunta ?? 'N/A',
                        'tipo' => $request->$tipoPregunta ?? 'N/A',
                        'opciones' => $request->$opcPregunta,
                        'respuesta' => $request->$respuesta ?? 'N/A',
                        'contenidoTematico' => $request->$contenidoT ?? 'N/A',
                    ];
                } else {
                    $respuesta = 'pregunta' . $i . '-resp-abierta';
                    $tempPregunta = [
                        'descripcion' => $request->$numPregunta ?? 'N/A',
                        'tipo' => $request->$tipoPregunta ?? 'N/A',
                        'opciones' => $request->$opcPregunta,
                        'respuesta' => $request->$respuesta ?? 'N/A',
                        'contenidoTematico' => $request->$contenidoT ?? 'N/A',
                    ];
                }
                array_push($preguntas, $tempPregunta);
                $contPreguntas++;
            }
        }


        DB::beginTransaction();
        try {
            DB::table('paqueterias_didacticas')
                ->where('id_curso', $idCurso)
                ->update([
                    'carta_descriptiva' => json_encode($cartaDescriptiva),
                    'eval_alumno' => json_encode($preguntas),
                    'updated_at' => Carbon::now(),
                    'id_user_updated' => Auth::id()
                ]);
            DB::commit();
            return redirect()->route('curso-inicio')->with('success', 'SE HA GUARDADO LA PAQUETERIA DIDACTICA!');
        } catch (\Exception $e) {
            throw $e;
            DB::rollback();

            return redirect()->route('curso-inicio')->with('warnining', 'HUBO UN ERROR AL GUARDAR LA PAQUETERIA DIDACTICA!');
        }
    }



    public function buscadorEspecialidades(Request $request)
    {
        $especialidades = DB::table('especialidades')
            ->where('nombre', 'like', '%' . $request->especialidad . '%')
            ->get();
        return response()->json($especialidades);
    }


    public function DescargarPaqueteria($idCurso)
    {
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1]])->first();
        if(!isset($paqueteriasDidacticas)){
            
            return redirect()->back()->with('warning','No se puede generar pdf con la informacion actual');
        }
        // 
        $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);


        $cartaDescriptiva->ponderacion = json_decode($cartaDescriptiva->ponderacion);
        $cartaDescriptiva->contenidoTematico = json_decode($cartaDescriptiva->contenidoTematico);


        $curso = curso::toBase()->where('id', $idCurso)->first();

        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.cartaDescriptiva', compact('cartaDescriptiva'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('paqueteriaDidactica.pdf');
    }
    public function DescargarPaqueteriaEvalAlumno($idCurso)
    {
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1]])->first();
        if (!isset($paqueteriasDidacticas)) {
            return redirect()->back()->with('warning', 'No se puede generar pdf con la informacion actual');
        }
        $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);
        $evalAlumno = json_decode($paqueteriasDidacticas->eval_alumno);
        if (!isset($evalAlumno) || !isset($paqueteriasDidacticas)) {
            return redirect()->back()->with('warning', 'No se puede generar pdf con la informacion actual');
        }
        $curso = curso::toBase()->where('id', $idCurso)->first();
        $abecedario = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.eval_alumno_pdf', compact('evalAlumno', 'abecedario', 'curso', 'cartaDescriptiva'));

        return $pdf->stream('EvaluacionAlumno.pdf');
    }

    public function DescargarPaqueteriaEvalInstructor()
    {
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.evaInstructorCurso_pdf');
        return $pdf->stream('evaluacionInstructor');
    }
    public function DescargarManualDidactico($idCurso)
    {
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1]])->first();
        $carta_descriptiva = (json_decode($paqueteriasDidacticas->carta_descriptiva));
        $contenidos = json_decode($carta_descriptiva->contenidoTematico);
        
        // $info_manual_didactico = $contenidos->contenidoExtra;
        $info_manual_didactico = [];
        $replace = array(request()->getSchemeAndHttpHost().'/', '\\');
        foreach($contenidos as $manual){
           $manual->contenidoExtra = str_replace($replace, '', $manual->contenidoExtra);
        }

        $curso = curso::toBase()->where('id', $idCurso)->first();
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.manualDidactico_pdf', compact('curso', 'paqueteriasDidacticas','contenidos', 'carta_descriptiva'));
        return $pdf->stream('manualDidactico');
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '.' . $extension;

            $request->file('upload')->move(public_path('images/paqueterias'), $fileName);
            $url = asset('images/paqueterias/' . $fileName);
            @header('Content-type: text/html; charset=utf-8');
            return response()->json(['url' => $url]);
        }
    }
}
