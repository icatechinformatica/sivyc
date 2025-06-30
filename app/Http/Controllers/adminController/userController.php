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
    public function index(Request $request)
    {
        //
        $tipo='nombres';
        $busqueda=strtoupper($request->busquedaPersonal);
        $usuarios = User::busquedapor($tipo,$busqueda)->PAGINATE(20);
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
        $curpuser = User::where('curp', '=', $request->get('curpInput'))->first();

        if($user){
            return redirect()->back()->withErrors([
                sprintf('EL CORREO ELECTRÓNICO %s YA SE ENCUENTRA REGISTRADO EN EL SISTEMA', $request->get('emailInput'))
            ]);
        }
        if($curpuser){
            return redirect()->back()->withErrors([
                sprintf('LA CURP %s YA SE ENCUENTRA REGISTRADO EN EL SISTEMA', $request->get('curpInput'))
            ]);
        }
        //dd($user);
        if (!$user && !$curpuser) {
            # usuario no encontrado
            User::create([
                'name' => trim($request->get('nameInput')),
                'curp' => trim($request->get('curpInput')),
                'telefono' => trim($request->get('telInput')),
                'activo' => (bool) true,
                'email' => trim($request->get('emailInput')),
                'password' => Hash::make(trim($request->get('passwordInput'))),
                'puesto' => trim($request->get('puestoInput')),
                'unidad' => trim($request->get('capacitacionInput'))
            ]);
            // si funciona redireccionamos
            return redirect()->route('usuario_permisos.index')->with('success', 'NUEVO USUARIO AGREGADO!');
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
        $ubicaciones = Unidad::groupBy('ubicacion')->GET(['ubicacion']);
        $ubicacion = Unidad::Select('unidad','ubicacion')->Where('id',$usuario->unidad)->First();
        $unidades = Unidad::Select('id','unidad')->Where('ubicacion',$ubicacion->ubicacion)->Get();
        return view('layouts.pages_admin.users_profile', compact('usuario', 'ubicaciones', 'ubicacion', 'unidades'));
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
            // $usuarios = new User();

            if (!empty($request->input('inputPasswordUpdate'))) {
                # si no está vacio se agrega
                $arrayUser = [
                    'name' => trim($request->inputNameUpdate),
                    'curp' => trim($request->inputCurpUpdate),
                    'telefono' => trim($request->inputTelUpdate),
                    'password' => Hash::make(trim($request->get('inputPasswordUpdate'))),
                    'puesto' => trim($request->get('inputPuestoUpdate')),
                    'unidad' => (int)trim($request->get('inputCapacitacionUpdate'))
                ];
            } else {
                # si está vacio no se agrega al arreglo
                $arrayUser = [
                    'name' => trim($request->inputNameUpdate),
                    'curp' => trim($request->inputCurpUpdate),
                    'telefono' => trim($request->inputTelUpdate),
                    'puesto' => trim($request->get('inputPuestoUpdate')),
                    'unidad' => (int)$request->inputCapacitacionUpdate
                ];
            }


            User::WHERE('id', $idUsuario)->Update($arrayUser);
            // $usuario = User::WHERE('id', $idUsuario)->First();dd($usuario, $arrayUser);
            // dd($usuario);
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

    public function updateActivo(Request $request)
    {
        $userId = $request->input('user_id');
        $isActive = $request->input('is_active');

        $user = User::find($userId);
        if ($user) {
            $user->activo = $isActive;
            if($isActive) {
                $user->password = str_replace('BAJA','',$user->password);
            } else {
                $user->password = 'BAJA'.$user->password;
            }
            $user->save();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function gestorPermisosUsuarios($id)
    {
        $idUsuario = base64_decode($id);
        $usuario = User::findOrfail($idUsuario);
        $permisos = Permission::all();
        return view('layouts.pages_admin.gestor_usuario_permisos', compact('usuario', 'permisos', 'idUsuario'));
    }

    public function updatePermisosUsuario(Request $request, $id)
    {
        $idUsuario = base64_decode($id);
        $usuario = User::findOrFail($idUsuario);

        // Obtén los permisos enviados desde el formulario
        $permisosSeleccionados = $request->input('permisos', []);

        // Sincroniza los permisos (elimina los que no están y agrega los nuevos)
        $usuario->permissions()->sync($permisosSeleccionados);

        return redirect()->route('usuarios.permisos.index', ['id' => $id])
            ->with('success', 'Permisos actualizados correctamente.');
    }
}
