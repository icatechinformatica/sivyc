<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\curso;
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

        $curso = curso::toBase()->where('id', $idCurso)->first();
        $paqueterias = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1]])->first();
        // dump($curso );
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas', compact('idCurso', 'curso', 'paqueterias'));
    }

    public function store(Request $request, $idCurso)
    {
        //
        // dd($request->contenidoT);
        
        $paqueteriasDidacticas = new PaqueteriasDidacticas();
        $preguntas = ['instrucciones'=>$request->instrucciones];

        $cartaDescriptiva = [
            'nombrecurso' => $request->nombrecurso,
            'entidadfederativa' => $request->entidadfederativa,
            'cicloescolar' => $request->cicloescolar,
            'programaestrategico' => $request->programaestrategico,
            'modalidad' => $request->modalidad,
            'tipo' => $request->tipo,
            'perfilidoneo' => $request->perfilidoneo,
            'duracion' => $request->duracion,
            'formacionlaboral' => $request->formacionlaboral,
            'especialidad' => $request->especialidad,
            'publico' => $request->publico,
            'aprendizajeesperado' => $request->aprendizajeesperado,
            'criterio' => $request->criterio,
            'ponderacion' => $request->ponderacion,
            'objetivoespecifico' => $request->objetivoespecifico,
            'transversabilidad' => $request->transversabilidad,
            'contenidoTematico' => $request->contenidoT,
            'observaciones' => $request->observaciones,
            'recursosDidacticos' => $request->recursosD,
        ];
        
        // dd($cartaDescriptiva);
        // dump (array_count_values($request->toArray()));
        $i = 0;
        $contPreguntas = 0;
        
        $auxContPreguntas = $request->numPreguntas;
        
        while(true) {
            $i++;
            if($contPreguntas == $auxContPreguntas )
                break;
            
            $numPregunta = 'pregunta'.$i;
            $tipoPregunta = 'pregunta'.$i.'-tipo';
            $opcPregunta = 'pregunta'.$i.'-opc';
            $respuesta = 'pregunta'.$i.'-opc-answer';
            $contenidoT = 'pregunta'.$i.'-contenidoT';
            

            if($request->$numPregunta != null){
                $tempPregunta = [
                    'descripcion' => $request->$numPregunta,
                    'tipo' => $request->$tipoPregunta,
                    'opciones' => $request->$opcPregunta,
                    'respuesta' => $request->$respuesta,
                    'contenidoTematico' => $request->$contenidoT,
                ];
                array_push($preguntas, $tempPregunta);
                $contPreguntas++;
            }
            
        }

        // dd($preguntas);
        

        DB::beginTransaction();
        try {
            PaqueteriasDidacticas::create([
                'id_curso' => $idCurso,
                'carta_descriptiva' => json_encode($cartaDescriptiva),
                'eval_alumno' => json_encode($preguntas),
                'estatus' => 1,
                'created_at' => Carbon::now(),
                'id_user_created' => Auth::id(),
                // 'idUsuarioUMod' => Auth::id()
            ]);
            DB::commit();
            return redirect()->route('curso-inicio')->with('success', 'SE HA GUARDADO LA PAQUETERIA DIDACTICA!');
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->route('curso-inicio')->with('error', 'HUBO UN ERROR AL GUARDAR LA PAQUETERIA DIDACTICA!');
        }
        
    }

    public function buscadorEspecialidades(Request $request)
    {
        $especialidades = DB::table('especialidades')
            ->where('nombre', 'like', '%' . $request->especialidad .'%')
            ->get();
        return response()->json($especialidades);
    }


    public function DescargarPaqueteria($idCurso){
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1] ])->first();
        
        // dd($paqueteriasDidacticas);
        $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);
        $cartaDescriptiva->ponderacion = json_decode($cartaDescriptiva->ponderacion);
        $cartaDescriptiva->contenidoTematico = json_decode($cartaDescriptiva->contenidoTematico);
        $cartaDescriptiva->recursosDidacticos = json_decode($cartaDescriptiva->recursosDidacticos);
        
        $evalAlumno = json_decode($paqueteriasDidacticas->eval_alumno);
        // dd($evalAlumno);
        $curso = curso::toBase()->where('id', $idCurso)->first();
        $abecedario = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        
        // dd($evalAlumno);
        // dd($cartaDescriptiva, $evalAlumno, $curso);
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.cartaDescriptiva', compact('cartaDescriptiva', 'evalAlumno', 'abecedario', 'curso'));
        
        return $pdf->stream('paqueteriaDidactica.pdf');    
    }
    public function DescargarPaqueteriaEvalAlumno($idCurso){
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where([['id_curso', $idCurso], ['estatus', 1] ])->first();
        $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);
        $cartaDescriptiva->ponderacion = json_decode($cartaDescriptiva->ponderacion);
        $cartaDescriptiva->contenidoTematico = json_decode($cartaDescriptiva->contenidoTematico);
        $cartaDescriptiva->recursosDidacticos = json_decode($cartaDescriptiva->recursosDidacticos);
        
        $evalAlumno = json_decode($paqueteriasDidacticas->eval_alumno);
        // dd($evalAlumno);
        $curso = curso::toBase()->where('id', $idCurso)->first();
        $abecedario = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        
        // dd($evalAlumno);
        // dd($cartaDescriptiva, $evalAlumno, $curso);
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.eval_alumno_pdf', compact('cartaDescriptiva', 'evalAlumno', 'abecedario', 'curso'));
        
        return $pdf->stream('paqueteriaDidactica.pdf');    
    }
}
