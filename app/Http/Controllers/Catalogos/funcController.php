<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Models\Catalogos\Funcionarios;
use App\Models\ModelPat\Organismos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class funcController extends Controller
{
    public function index(Request $request) {
        $busqueda = '';
        $action = $request->input('accion');
        // dd($action);
        if ($action == 'limpiar') {
            return redirect()->route('catalogos.funcionarios.inicio');
        }

        if(!empty($request->input('busqueda'))){
            $busqueda = $request->input('busqueda');
        }
        $data_func = Funcionarios::Busqueda($busqueda)
        ->select('tbl_funcionarios.*')
        ->orderBy('id', 'desc')
        ->paginate(10, ['tbl_funcionarios.*']);

        //Obtener los organismos
        $list_org = Organismos::select('id', 'nombre')->orderBy('id', 'asc')->pluck('nombre', 'id')->toArray();
        $list_cargos = DB::table('tbl_cargos')->select('id', 'cargo')->orderBy('id', 'asc')->pluck('cargo', 'id')->toArray();

        return view('catalogos.frm_funcionarios', compact('data_func','busqueda','list_org','list_cargos'));
    }

    public function guardar(Request $request) {
        // dd($request->all());
        try {
            $id_registro = $request->input('id_registro');
            if (!empty($id_registro)) {
                $result = DB::table('tbl_funcionarios')
                    ->where('id', $id_registro)
                    ->update([
                        'id_org' => $request->input('org'),
                        'titular' => $request->input('titular') == 'titular_si' ? true : false,
                        'nombre' => $request->input('nombre'),
                        'cargo' => $request->input('cargo'),
                        'adscripcion' => $request->input('adscripcion'),
                        'direccion' => $request->input('direc'),
                        'telefono' => $request->input('telefono'),
                        'correo' => $request->input('email'),
                        'correo_institucional' => $request->input('email2'),
                        'curp' => $request->input('curp'),
                        'titulo' => $request->input('titulo'),
                        'activo' => $request->input('status') == 'activo' ? 'true' : 'false',
                        'id_cargo' => $request->input('sel_cargo'),
                        'iduser_created' => Auth::user()->id,
                        'created_at' => date('Y-m-d')
                    ]);
            } else {
                // Si el id_registro no existe, realiza un insert
                $result = DB::table('tbl_funcionarios')->insert([
                    'id_org' => $request->input('org'),
                    'titular' => $request->input('titular') == 'titular_si' ? true : false,
                    'nombre' => $request->input('nombre'),
                    'cargo' => $request->input('cargo'),
                    'adscripcion' => $request->input('adscripcion'),
                    'direccion' => $request->input('direc'),
                    'telefono' => $request->input('telefono'),
                    'correo' => $request->input('email'),
                    'correo_institucional' => $request->input('email2'),
                    'curp' => $request->input('curp'),
                    'titulo' => $request->input('titulo'),
                    'activo' => $request->input('status') == 'activo' ? 'true' : 'false',
                    'id_cargo' => $request->input('sel_cargo'),
                    'iduser_created' => Auth::user()->id,
                    'created_at' => date('Y-m-d')
                ]);
            }
        } catch (\Throwable $th) {
            return redirect()->route('catalogos.funcionarios.inicio')->with('message', 'Error: ' . $th->getMessage());
        }

        if ($result == 1 && !empty($id_registro)) $message = 'Datos actualizados con exito!!';
        else if ($result == 1 && empty($id_registro)) $message = 'Datos registrados con exito!!';
        else $message = 'Error en el guardado.';

        return redirect()->route('catalogos.funcionarios.inicio')->with('message', $message);

    }

    public function obtener_datos(Request $request){
        $id_registro = $request->input('id_registro');

        if(!empty($id_registro)){
            ##Realizar la busqueda y enviarla por json a la vista
        }

        return response()->json([
            'status' => 200,
            'mensaje' => 'El id de registro es:  '.$id_registro
        ]);
    }


}
