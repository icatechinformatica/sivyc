<?php

namespace App\Http\Controllers\webController;
use App\Http\Controllers\Controller;
use App\Models\api\Curso;
use App\Models\Area;
use App\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AreasController extends Controller
{
    public function index(Request $request) {

        $areas = Area::Busqueda($request->get('busqueda'), $request->get('busqueda_aspirantepor'))
            ->leftjoin('users', 'area.iduser_created', '=', 'users.id')
            ->leftjoin('users  AS usuarios', 'area.iduser_updated', '=', 'usuarios.id')
            ->select('area.*', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->orderByDesc('area.id')
            ->paginate(15, ['area.*', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated']);

        /* $areas = DB::table('area')
            ->leftjoin('users', 'area.iduser_created', '=', 'users.id')
            ->leftjoin('users  AS usuarios', 'area.iduser_updated', '=', 'usuarios.id')
            ->select('area.*', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->orderByDesc('area.id')
            ->paginate(10); */
            // ->get();

        return view('layouts.pages.vstainicioareas', compact('areas'));
    }

    public function create() {
        return view('layouts.pages.vstaformarea');
    }

    public function save(Request $request) {
        $area = new Area();
        $date = new DateTime();

        $area->formacion_profesional = $request->nombre;
        $area->created_at = $date;
        $area->iduser_created = Auth::user()->id;

        $area->activo = $request->status;

        $area->save();
        return redirect()->route('areas.inicio')->with('success', 'Área guardada');
    }

    public function update($id) {
        $area = Area::where('id', '=', $id)->first();

        return view('layouts.pages.frmareaupdate', compact('area'));
    }

    public function update_save(Request $request) {
        // dd($request->id);
        $area = Area::find($request->idarea);
        $date = new DateTime();

        $area->formacion_profesional = $request->nombre;
        $area->updated_at = $date;
        $area->iduser_updated = Auth::user()->id;
        $area->activo = $request->status;

        $area->save();

        return redirect()->route('areas.inicio')->with('success', 'Área actualizada');
    }

    public function destroy($id) {
        Area::destroy($id);

        return redirect()->route('areas.inicio')->with('success', 'Área eliminada');
    }

}
