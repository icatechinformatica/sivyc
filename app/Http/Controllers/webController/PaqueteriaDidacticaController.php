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
        dd($request->toArray());

        DB::beginTransaction();
        try {
            PaqueteriasDidacticas::create([
                'id_curso' => $idCurso,
                'carta_descriptiva' => json_encode($request->infoCursoTecnico),
                'eval_alumno' => json_encode($request->evaluacionAlumno),
                'estatus' => 1,
                'created_at' => Carbon::now(),
                'id_user_created' => Auth::id(),
                // 'idUsuarioUMod' => Auth::id()
            ]);
            DB::commit();
            session(['message' => 'El registro se ha guardado']);
            session(['alert' => 'alert-success']);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            session(['message' => 'Algo salió mal intente nuevamente']);
            session(['alert' => 'alert-danger']);
            return $e;
        }
    }

    public function buscadorEspecialidades(Request $request)
    {


        $especialidades = DB::table('especialidades')
            ->where('nombre', 'like', '%' . $request->especialidad .'%')
            ->get();
        return response()->json($especialidades);
    }
}
