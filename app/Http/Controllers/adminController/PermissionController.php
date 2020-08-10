<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Rol;

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
        //
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
        //
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
        return view('layouts.pages_admin.gestor_rol_permisos', compact('roles', 'permisos'));
    }
}
