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

        // dump($curso );
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas', compact('idCurso', 'curso'));
    }

    public function store(Request $request, $idCurso)
    {
        //
        $paqueteriasDidacticas = new PaqueteriasDidacticas();
        $preguntas = [];

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
        
        // dd($diff);
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
            

            if($request->$numPregunta != null){
                $tempPregunta = [
                    'descripcion' => $request->$numPregunta,
                    'tipo' => $request->$tipoPregunta,
                    'opciones' => $request->$opcPregunta,
                    'respuesta' => $request->$respuesta
                ];
                array_push($preguntas, $tempPregunta);
                $contPreguntas++;
            }
        }
        

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
        $paqueteriasDidacticas = PaqueteriasDidacticas::toBase()->where('id_curso', $idCurso)->first();
        $cartaDescriptiva = json_decode($paqueteriasDidacticas->carta_descriptiva);
        $evalAlumno = json_decode($paqueteriasDidacticas->eval_alumno);
        // dd($paqueteriasDidacticas, $cartaDescriptiva, $evalAlumno);
        $curso = curso::toBase()->where('id', $idCurso)->first();
        $pdf = \PDF::loadView('layouts.pages.paqueteriasDidacticas.pdf.cartaDescriptiva', compact('cartaDescriptiva', 'evalAlumno', 'curso'));

        return $pdf->stream('paqueteriaDidactica.pdf');    
    }
}
