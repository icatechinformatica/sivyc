<?php

namespace App\Http\Controllers\Grupo;

use App\Models\Grupo;
use App\Utilities\MyUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GrupoController extends Controller
{
    public $activar;
    public $id_user;
    public $data;
    public function index(Request $request)
    {
        $valor_buscar = $request->valor_buscar;
        $ejercicio = $request->ejercicio;
        $activar = $this->activar;
        $anios = MyUtility::ejercicios();
        $parameters = $request->all();
        if (!isset($parameters['ejercicio'])) $ejercicio = $parameters['ejercicio'] = date('Y');
        $data = DB::table('alumnos_registro as ar')
            ->select('ar.folio_grupo', 'ar.turnado', 'c.nombre_curso as curso', 'ar.unidad', 'id_instructor')
            ->join('cursos as c', 'ar.id_curso', '=', 'c.id');

        if (preg_match('/^2B-\d{6}$/', $valor_buscar)) {
            $data->where('ar.folio_grupo', 'like', '%' . $valor_buscar . '%');
            $parameters['ejercicio'] = $ejercicio = null;
        } else {
            $data->whereYear('ar.inicio', '=', $ejercicio)
                ->where(function ($query) use ($valor_buscar) {
                    $query->where('ar.folio_grupo', 'like', '%' . $valor_buscar . '%')
                        ->orWhere('c.nombre_curso', 'like', '%' . $valor_buscar . '%');
                });
        }

        // if ($this->data['slug'] == 'vinculadores_administrativo') {
        //     $data->where('ar.iduser_created', $this->id_user);
        // }

        // if (!empty($_SESSION['unidades'])) {
        //     $data->whereIn('ar.unidad', $_SESSION['unidades']);
        // }

        $data = $data->whereNotNull('ar.folio_grupo')
            ->groupBy('ar.folio_grupo', 'ar.turnado', 'c.nombre_curso', 'ar.unidad', 'id_instructor')
            ->orderBy('ar.folio_grupo', 'DESC')
            ->paginate(15);
        return view('grupos.index', compact('data', 'activar', 'anios', 'parameters'));
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
