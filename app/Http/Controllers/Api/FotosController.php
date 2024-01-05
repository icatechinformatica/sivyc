<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\DB;
use Exception;

class FotosController extends Controller
{
    // public function savefot (Request $request){
    //     $clave = $request->clave;
    //     if($clave){
    //         try {

    //             $curso = tbl_curso::where('clave', '=', $clave)->first();
    //             $folio_grupo = $curso->folio_grupo;
    //             $respuesta = ['status' => 'success', 'message' => $folio_grupo];

    //         } catch (\Throwable $th) {
    //             return response()->json($th->getMessage(), 501);
    //         }
    //     }else{
    //         $respuesta = ['status' => 'alert', 'message' => 'Fallo en la clave'];
    //     }

    //     return response()->json($respuesta, 200);
    // }

    public function savefotos(Request $request){
        try {
            $clave = $request->input('clave');

            // Validar la clave si es necesario
            if (empty($clave)) {
                throw new \Exception('Fallo en la clave');
            }

            $curso = tbl_curso::where('clave', $clave)->first();

            if (!$curso) {
                throw new \Exception('Curso no encontrado para la clave proporcionada');
            }

            $folio_grupo = $curso->folio_grupo;
            $respuesta = ['status' => 'success', 'message' => $folio_grupo];
        } catch (\Throwable $th) {
            // Puedes loguear o enviar más información sobre la excepción
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }

        return response()->json($respuesta, 200);
    }
}
