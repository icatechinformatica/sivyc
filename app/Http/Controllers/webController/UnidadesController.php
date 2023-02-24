<?php
// Creado Por Orlando Chavez
namespace App\Http\Controllers\webController;

use App\Models\instructor;
use App\ProductoStock;
use App\Models\Unidad;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Redirect,Response;
use App\Models\InstructorPerfil;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Support\Facades\DB;

class UnidadesController extends Controller
{
    /**
     * Display a listing of the resource.com
     *
     * @return \Illuminate\Http\Response
     */
    // comentario para prueba
    public function index(Request $request) {

        $busqueda_unidad = $request->get('busquedaporUnidad');
        $tipoUnidad = $request->get('tipo_unidad');

        $data = Unidad::BusquedaUnidad($tipoUnidad, $busqueda_unidad)->WHERE('tbl_unidades.id', '!=', '0')
                        ->PAGINATE(25, ['tbl_unidades.id','tbl_unidades.unidad','tbl_unidades.cct',
                                        'tbl_unidades.dunidad','tbl_unidades.ubicacion']);

            //dd($data);
        return view('layouts.pages.vstaunidades', compact('data'));

    }

    public function editar($id) {

        $data = Unidad::WHERE('id', '=', $id)->FIRST();

        return view('layouts.pages.frmeditarunidad', compact('data'));
    }

    public function update(Request $request) {
        Unidad::where('id', '=', $request->idunidad)
        ->update(['unidad' => $request->unidad,
                  'cct' => $request->cct,
                  'dunidad' => $request->dunidad,
                  'dgeneral' => $request->dgeneral,
                  'plantel' => $request->plantel,
                  'academico' => $request->academico,
                  'vinculacion' => $request->vinculacion,
                  'dacademico' => $request->dacademico,
                  'pdgeneral' => $request->pdgeneral,
                  'pdacademico' => $request->pdacademico,
                  'pdunidad' => $request->pdunidad,
                  'pacademico' => $request->pacademico,
                  'pvinculacion' => $request->pvinculacion,
                  'jcyc' => $request->jcyc,
                  'pjcyc' => $request->pjcyc,
                  'ubicacion' => $request->ubicacion,
                  'direccion' => $request->direccion,
                  'telefono' => $request->telefono,
                  'correo' => $request->correo,
                  'coordenadas' => $request->coordenadas,
                  'codigo_postal' => $request->codigo_postal]);

        return redirect()->route('unidades.inicio')
               ->with('success','Modificaión de Unidad Guardada');
    }
}
