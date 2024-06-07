<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Excel\xls;
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

        $especialidades = $this->data($request);    
        $busqueda = $request->get('busqueda');
        return  view('layouts.pages.vstainicioEspecialidades', compact('especialidades','busqueda'));
    }
    
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
        $areas = DB::table('area')->where('activo', true)->get();

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

    private function data($request, $opt = null){
        $especialidades = especialidad::Busqueda($request->get('busqueda'), $request->get('busqueda'))
            ->leftjoin('area', 'especialidades.id_areas', '=', 'area.id')
            ->leftjoin('users', 'especialidades.iduser_created', '=', 'users.id')
            ->leftjoin('users  AS usuarios', 'especialidades.iduser_updated', '=', 'usuarios.id')            
            ->orderByDesc('especialidades.id');
        if($opt == "xls")
            $especialidades = $especialidades->select('especialidades.clave', 'especialidades.nombre', 'area.formacion_profesional', 'especialidades.prefijo', 'especialidades.activo', DB::raw("TO_CHAR(especialidades.updated_at, 'DD/MM/YYYY')"))->get();
        else
            $especialidades = $especialidades->select('especialidades.*', 'area.formacion_profesional AS nameArea', 'users.name AS nameCreated', 'usuarios.name AS nameUpdated')
            ->paginate(15, ['id', 'clave', 'nombre', 'created_at', 'updated_at', 'nameArea', 'nameCreated', 'nameUpdated', 'activo', 'prefijo']);
        return $especialidades;

    }


    public function xls(Request $request){ 
        $data = $this->data($request, 'xls');
       // dd($data);
        if($data){
            $title = "CATALOGO DE ESPECIALIDADES";
            $name = "CAT_ESPECIALIDADES_".date('Ymd').".xlsx";                    
            $head = ['CLAVE','ESPECIALIDAD','CAMPO_FORMACION','PREFIJO','ACTIVO','ACTUALIZADO'];
            return Excel::download(new xls($data,$head, $title), $name);            
        }else return "NO SE ENCONTRO DATOS QUE MOSTRAR.";
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
