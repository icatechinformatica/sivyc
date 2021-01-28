<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\api\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use DateTime;

class EspecialidadesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $especialidades = especialidad::Busqueda($request->get('busqueda'), $request->get('busqueda_aspirantepor'))
            ->leftjoin('area', 'especialidades.id_areas', '=', 'area.id')
            ->leftjoin('users', 'especialidades.iduser_created', '=', 'users.id')
            ->leftjoin('users  AS usuarios', 'especialidades.iduser_updated', '=', 'usuarios.id')
            ->select('especialidades.*', 'area.formacion_profesional AS nameArea', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->orderByDesc('especialidades.id')
            ->paginate(15, ['id', 'clave', 'nombre', 'created_at', 'updated_at', 'nameArea', 'nameCreated', 'nameUpdated', 'activo', 'prefijo']);

        /*$especialidades = DB::table('especialidades')
            ->leftjoin('area', 'especialidades.id_areas', '=', 'area.id')
            ->leftjoin('users', 'especialidades.iduser_created', '=', 'users.id')
            ->leftjoin('users  AS usuarios', 'especialidades.iduser_updated', '=', 'usuarios.id')
            ->select('especialidades.*', 'area.formacion_profesional AS nameArea', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->orderByDesc('especialidades.id')
            ->paginate(10);
        ->get(); */

        return  view('layouts.pages.vstainicioEspecialidades', compact('especialidades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = DB::table('area')->get();

        return view('layouts.pages.vstaformespecialidades', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $especialidad = new Especialidad();
        $date = new DateTime();

        $especialidad->clave = $request->clave;
        $especialidad->nombre = $request->nombre;
        $especialidad->created_at = $date;
        $especialidad->id_areas = $request->area;
        $especialidad->iduser_created = Auth::user()->id;
        $especialidad->activo = $request->status;
        $especialidad->prefijo = $request->prefijo;

        $especialidad->save();

        return redirect()->route('especialidades.inicio')->with('success', 'insertado');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $especialidad = Especialidad::where('id', '=', $id)->first();
        $areas = DB::table('area')->get();

        return view('layouts.pages.frmespecialidadupdate', compact('especialidad', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $especialidad = Especialidad::find($id);
        $date = new DateTime();

        $especialidad->clave = $request->clave;
        $especialidad->nombre = $request->nombre;
        $especialidad->updated_at = $date;
        $especialidad->id_areas = $request->area;
        $especialidad->iduser_updated = Auth::user()->id;
        $especialidad->activo = $request->status;
        $especialidad->prefijo = $request->prefijo;

        $especialidad->save();

        return redirect()->route('especialidades.inicio');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Especialidad::destroy($id);
        // return redirect()->route('especialidades.inicio');
    }
}
