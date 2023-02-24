<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personal;
use App\Models\OrganoAdministrativo;
use App\Models\AreaAdscripcion;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tipoPersonal = $request->get('tipo_busqueda_personal');
        $busquedaPersonal = $request->get('busquedaPersonal');
        //
        $directorio = Personal::busqueda($tipoPersonal, $busquedaPersonal)->orderBy('apellidoPaterno')->PAGINATE(25);
        return view('layouts.pages_admin.personal_index', compact('directorio'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $organo = new OrganoAdministrativo;
        $oA = $organo->all();
        return view('layouts.pages_admin.personal', compact('oA'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator =  Validator::make($request->all(), [
            'inputnumeroControl' => 'required',
            'inputNombre' => 'required',
            'inputPuesto' => 'required',
            'inputCategoria' => 'required',
            'inputOrganoAdministrativo' => 'required',
            'inputAreaAdscripcion' => 'required',
            'inputCurp' => 'required',
            'inputEmail' => 'required',
        ]);
        if ($validator->fails()) {
            # devolvemos un error
            return redirect()->back()->withErrors($validator)
                    ->withInput();
        } else {
            // guardar registro en la base de datos
            $personal = new Personal;
            $personal->numero_enlace = trim($request->inputnumeroControl);
            $personal->nombre = trim($request->inputNombre);
            $personal->apellidoPaterno = trim($request->inputApellidoPaterno);
            $personal->apellidoMaterno = trim($request->inputApellidoMaterno);
            $personal->puesto = trim($request->inputPuesto);
            $personal->categoria = trim($request->inputCategoria);
            $personal->area_adscripcion_id = trim($request->inputAreaAdscripcion);
            $personal->curp = trim($request->inputCurp);
            $personal->email = trim($request->inputEmail);
            $personal->activo = TRUE;
            // guardar registro
            $personal->save();

            // redireccionamos con un mensaje de éxito
            return redirect()->route('personal.index')
            ->with('success', 'PERSONAL AGREGADO CORRECTAMENTE!');
        }

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
        // editar el directorio
        $idDirectorio = base64_decode($id);
        $organo = new OrganoAdministrativo;
        $oAdministrativo = $organo->all();
        $personalEditar = new Personal();
        $aAdscripcion = new AreaAdscripcion();
        $adscripcion = $aAdscripcion->all();
        $directorioPersonal = $personalEditar->WHERE('directorio.id', '=', $idDirectorio)
                            ->LEFTJOIN('area_adscripcion', 'directorio.area_adscripcion_id', '=', 'area_adscripcion.id')
                            ->LEFTJOIN('organo_administrativo', 'area_adscripcion.organo_id', '=', 'organo_administrativo.id')
                            ->FIRST(['directorio.id', 'directorio.nombre', 'directorio.apellidoPaterno', 'directorio.apellidoMaterno', 'directorio.puesto',
                            'directorio.categoria', 'directorio.numero_enlace','directorio.activo','directorio.curp','directorio.email', 'area_adscripcion.area', 'area_adscripcion.id AS idadscripcion', 'organo_administrativo.organo',
                            'organo_administrativo.id AS idOrgano',
                            'directorio.activo']);

        return view('layouts.pages_admin.personal_edit', compact('directorioPersonal', 'oAdministrativo', 'adscripcion'));

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
        // actualizar registros de directorio
        $idDirectorio = base64_decode($id);
        $numero_enlace = trim($request->inputNumeroEnlaceUpdate);
        $activos = ($request->activos !== null) ? false : true;

        $array_personal_update = [
            'numero_enlace' => $numero_enlace,
            'nombre' => trim($request->inputNameUpdate),
            'apellidoPaterno' => trim($request->inputPaternoUpdate),
            'apellidoMaterno' => trim($request->inputMaternoUpdate),
            'categoria' => trim($request->inputCategoria),
            'puesto' => trim($request->inputPuestoUpdate),
            'activo' => $activos,
            'area_adscripcion_id' => trim($request->inputAdscripcionUpdate)
        ];

        if (!empty(trim($numero_enlace))){
            // actualizamos los registros
            Personal::WHERE('id', $idDirectorio)->UPDATE($array_personal_update);

            return redirect()->route('personal.index')
            ->with('success', sprintf('PERSONAL CON EL NÚMERO DE ENLACE  %s  ACTUALIZADO EXTIOSAMENTE!', $numero_enlace));
        } else {
            return redirect()->back()->withErrors(['msg', sprintf('EL NÚMERO DE ENLACE %s ESTÁ VACIO', $numero_enlace)]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    protected function getAdscripcion($id){
        if (isset($id)){
            /*Aquí si hace falta habrá que incluir la clase municipios con include*/
            $idOrgano=$id;
            $adscripcion = new AreaAdscripcion();
            $areaAdscripcion = $adscripcion->WHERE('organo_id', $idOrgano)->GET(['id', 'area']);

            /*Usamos un nuevo método que habremos creado en la clase municipio: getByDepartamento*/
            $json=json_encode($areaAdscripcion);
        }else{
            $json=json_encode(array('error'=>'No se recibió un valor de id del Organo para filtar'));
        }

        return $json;
    }
}
