<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Rol;
use App\Models\PermisosRol;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $permisos = Permission::PAGINATE(3);
        return  view('layouts.pages_admin.permissions_roles', compact('permisos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return  view('layouts.pages_admin.permissions_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // permisos

        $permisos = $request->get('permisos');
        if (empty($permisos)) {
            # la lista está vacia - verdadero
            return redirect()->back()->withErrors("No se seleccionaron ningún elemento para lo")
            ->withInput();
        } else {

            $idRol = $request->get('idrole');
            $roles = Rol::findOrfail($idRol);
            // borrar los permisos de dicho rol
            $roles->permissions()->detach();
            foreach($request->get('permisos') as $arraPermisos)
            {
                $roles->permissions()->attach($arraPermisos);
            }

            return redirect()->route('gestor.permisos.roles', ['id' => base64_encode($idRol)])
            ->with('success', 'PERMISOS OTORGADOS CORRECTAMENTE!');
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
        //
        $idpermission = base64_decode($id);
        $permiso = Permission::findOrfail($idpermission);
        return view('layouts.pages_admin.permisos_editar', compact('permiso'));
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
        // modificacion de un permiso guardado
        if (isset($id)) {
            $idpermisos = base64_decode($id);
            $permisos = new Permission();

            # code...
            $arrayPermisos = [
                'name' => trim($request->permisoNameEdit),
                'slug' => trim($request->permisoSlugEdit),
                'description' => trim($request->permisoDescripcionEdit),
            ];

            $permisos->WHERE('id', $idpermisos)->UPDATE($arrayPermisos);
            return redirect()->route('permisos.index')
                    ->with('success', 'PERMISO ACTUALIZADO EXTIOSAMENTE!');
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

    public function permiso_rol(){
        $rol = Rol::PAGINATE(5, ['id', 'name', 'slug', 'description']);
        // $permisos = Permission::PAGINATE(5);
        return  view('layouts.pages_admin.permiso_rol', compact('rol'));
    }

    public function gestorPermisosRoles($id){
        $idRol = base64_decode($id);
        $permisos = Permission::all();
        $roles = Rol::findOrfail($idRol);
        return view('layouts.pages_admin.gestor_rol_permisos', compact('roles', 'permisos', 'idRol'));
    }

    public function storePermission(Request $request){
        $validator =  Validator::make($request->all(), [
            'permisoName' => 'required',
            'permisoSlug' => 'required',
        ]);
        if ($validator->fails()) {
            # devolvemos un error
            return redirect()->back()->withErrors($validator)
                    ->withInput();
        } else {
            // guardar registro en la base de datos
            $permisoRegistro = new Permission;
            $permisoRegistro->name = trim($request->get('permisoName'));
            $permisoRegistro->slug = trim($request->get('permisoSlug'));
            if (!empty($request->get('permisoDescripcion'))) {
                # si no está vacio se le asigna a la variable...
                $permisoRegistro->description = trim($request->get('permisoDescripcion'));
            }
            $permisoRegistro->save();

            // redireccionamos con un mensaje de éxito
            return redirect()->route('permisos.index')
            ->with('success', 'PERMISO AGREGADO CORRECTAMENTE!');
        }
    }
}
