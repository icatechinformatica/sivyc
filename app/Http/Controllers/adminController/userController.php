<?php

namespace App\Http\Controllers\adminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Permission;
use App\Models\Rol;
use App\Models\Unidad;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $usuarios = User::PAGINATE(20);
        return view('layouts.pages_admin.users_permisions', compact('usuarios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ubicacion = Unidad::groupBy('ubicacion')->GET(['ubicacion']);
        // crear formulario usuario
        return view('layouts.pages_admin.users_create', compact('ubicacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //checar que no exista un usario con el correo electrónico que se piensa introducir
        $user = User::where('email', '=', $request->get('emailInput'))->first();
        //dd($user);
        if (!$user) {
            # usuario no encontrado
            User::create([
                'name' => trim($request->get('nameInput')),
                'email' => trim($request->get('emailInput')),
                'password' => Hash::make(trim($request->get('passwordInput'))),
                'puesto' => trim($request->get('puestoInput')),
                'unidad' => trim($request->get('capacitacionInput'))
            ]);
            // si funciona redireccionamos
            return redirect()->route('usuario_permisos.index')->with('success', 'NUEVO USUARIO AGREGADO!');
        } else {
            # usuario encontrado
            return redirect()->back()->withErrors([sprintf('EL CORREO ELECTRÓNICO %s A ESTA CUENTA YA SE ENCUENTRA EN LA BASE DE DATOS', $request->get('emailInput'))]);
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
        $idUsuario = base64_decode($id);
        $roles = Rol::all();
        $usuario = User::findOrfail($idUsuario);
        return view('layouts.pages_admin.users_permissions_profile', compact('usuario', 'roles'));
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
        $iduser = base64_decode($id);
        $usuario = User::findOrfail($iduser);
        $ubicacion = Unidad::groupBy('ubicacion')->GET(['ubicacion']);
        return view('layouts.pages_admin.users_profile', compact('usuario', 'ubicacion'));
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
        // modificacion de un recurso guardado
        if (isset($id)) {
            $idUsuario = base64_decode($id);
            $usuarios = new User();

            if (!empty($request->input('inputPasswordUpdate'))) {
                # si no está vacio se agrega
                $arrayUser = [
                    'name' => trim($request->inputNameUpdate),
                    'password' => Hash::make(trim($request->get('inputPasswordUpdate'))),
                    'puesto' => trim($request->get('inputPuestoUpdate')),
                    'unidad' => trim($request->get('inputCapacitacionUpdate'))
                ];
            } else {
                # si está vacio no se agrega al arreglo
                $arrayUser = [
                    'name' => trim($request->inputNameUpdate),
                    'slug' => trim($request->rolSlugUpdate),
                    'puesto' => trim($request->get('inputPuestoUpdate')),
                    'unidad' => trim($request->get('inputCapacitacionUpdate'))
                ];
            }

            $usuarios->WHERE('id', $idUsuario)->UPDATE($arrayUser);
            return redirect()->route('usuario_permisos.index')
                    ->with('success', 'USUARIO ACTUALIZADO EXTIOSAMENTE!');
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

    public function updateRol(Request $request, $id){
        // roles usuarios
        $idUsuario = base64_decode($id);
        $usuario = User::findOrfail($idUsuario);
        // borrar los permisos de dicho rol
        $usuario->roles()->detach();
        $idrol = $request->get('inputRol');
        $usuario->roles()->attach($idrol);

        return redirect()->route('usuario_permisos.index')
            ->with('success', 'USUARIO VINCULADO A ROL CORRECTAMENTE!');
    }
}
