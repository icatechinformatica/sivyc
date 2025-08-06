<?php

namespace App\Http\Controllers\Grupo;

use App\Models\Grupo;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Grupo\GrupoService;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{

    protected $obtenerGruposService;

    public function __construct(GrupoService $obtenerGruposService)
    {
        $this->obtenerGruposService = $obtenerGruposService;
    }

    public function index(Request $request)
    {

        try {
            $registrosPorPagina = $request->get('per_page', 15);
            $busqueda = $request->get('busqueda');
            $grupos = $this->obtenerGruposService->obtenerGrupos($registrosPorPagina, $busqueda);
            return view('grupos.index', compact('grupos'));
        } catch (\Exception $e) {
            Log::error('Error al obtener grupos: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los grupos'], 500);
        }
    }

    public function create()
    {
        return view('grupos.create');
    }

    public function store()
    {
        dd('Registrando grupo...');
    }

    public function asignarAlumnos()
    {
        return view('grupos.asignar_alumnos');
    }
}
