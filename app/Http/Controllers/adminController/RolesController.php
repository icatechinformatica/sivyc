<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rol;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rol = Rol::PAGINATE(15, ['id', 'name', 'slug', 'description']);
        //
        return view('layouts.pages_admin.roles_indice', compact('rol'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('layouts.pages_admin.roles_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validar
        $validator =  Validator::make($request->all(), [
            'rolName' => 'required',
            'rolSlug' => 'required',
        ]);
        if ($validator->fails()) {
            # devolvemos un error
            return redirect()->back()->withErrors($validator)
                    ->withInput();
        } else {
            // guardar registro en la base de datos
            $rolRegistro = new Rol;
            $rolRegistro->name = trim($request->get('rolName'));
            $rolRegistro->slug = trim($request->get('rolSlug'));
            $rolRegistro->description = trim($request->get('rolDescripcion'));
            $rolRegistro->save();

            // redireccionamos con un mensaje de éxito
            return redirect()->route('roles.index')
            ->with('success', 'ROL AGREGADO CORRECTAMENTE!');
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
        $idRol = base64_decode($id);
        $rol = Rol::findOrfail($idRol);
        return view('layouts.pages_admin.rol_profile', compact('rol'));
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
        // modificacion de un recurso guardado
        if (isset($id)) {
            $idroles = base64_decode($id);
            $roles = new Rol();

            # code...
            $arrayRol = [
                'name' => trim($request->rolNameUpdate),
                'slug' => trim($request->rolSlugUpdate),
                'description' => trim($request->rolDescripcionUpdate),
            ];

            $roles->WHERE('id', $idroles)->UPDATE($arrayRol);
            return redirect()->route('roles.index')
                    ->with('success', 'ROL ACTUALIZADO EXTIOSAMENTE!');
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
}
