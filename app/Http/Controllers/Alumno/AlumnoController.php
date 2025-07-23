<?php

namespace App\Http\Controllers\Alumno;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Alumno\AlumnoConsultaService;

class AlumnoController extends Controller
{
    protected $alumnoConsultaService;

    public function __construct(AlumnoConsultaService $alumnoConsultaService)
    {
        $this->alumnoConsultaService = $alumnoConsultaService;
    }

    public function index(Request $request)
    {
        try {

            $registrosPorPagina = $request->get('per_page', 15);
            $busqueda = $request->get('busqueda');
            $alumnos = $this->alumnoConsultaService->obtenerAlumnos($registrosPorPagina, $busqueda);
            return view('alumnos.index', compact('alumnos'));

        } catch (\Exception $e) {
            Log::error('Error en AlumnoController@index: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los alumnos'], 500);
        }
    }
}
