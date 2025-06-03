<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\TokenInvalidException;
use Carbon\Carbon;

class PreregistroinsController extends Controller
{

    protected $token;

    public function __construct(Request $request)
    {
        $this->token = $request->bearerToken(); // Obtener el token del encabezado

        if (!$this->isValidToken($this->token)) {
            throw new TokenInvalidException();
        }
    }

    private function isValidToken($token)
    {
        return DB::table('tokens_sendimg')->where('token', $token)->exists();
    }

    protected function pdf_upload($pdf, $curp, $nombre, $anio)
    {
        $extension = $pdf->getClientOriginalExtension();
        $fileName = trim($nombre . '.' . $extension);
        $directorio = '/'.$anio.'/preregistro/' . $curp . '/' . $fileName;

        $pdf->storeAs('/'.$anio.'/preregistro/'.$curp, $fileName);
        $pdfUrl = Storage::url('/' . $directorio);

        return [$pdfUrl, $directorio];
    }

    public function recibirpdfs (Request $request) {
        $curp = $request->curp;
        $token = $this->token;
        $exitoUpd = 0;

        if (empty($curp)) {throw new \Exception('La curp no es valida');}

        $anioActual = Carbon::now()->year;

        $documentos = [
            'pdf_domicilio' => 'archivo_domicilio',
            'pdf_curp' => 'archivo_curp',
            'pdf_nacimiento' => 'archivo_otraid',
            'pdf_rfc' => 'archivo_rfc',
            'foto_selfie' => 'archivo_fotografia',
            'pdf_cv' => 'archivo_curriculum_personal',
            'pdf_estudio' => 'archivo_estudios',
            'pdf_ine' => 'archivo_ine',
        ];

        $urlPDFs = [];

        try {

            foreach ($documentos as $campo => $campoBD) {
                if ($request->hasFile($campo)) {
                    $pdf = $request->file($campo);
                    $archivo = $this->pdf_upload($pdf, $curp, strtolower($campo), $anioActual);
                    $urlPDFs[$campoBD] = $archivo[1];
                }
            }


            if (count($urlPDFs) > 0) {
                //Guardar direccion en la base de datos
                $exitoUpd = DB::table('instructor')->where('curp', $curp)->update($urlPDFs);
                DB::table('tokens_sendimg')->where('token', $token)->delete();
            }
            // archivo_ine
            // archivo_domicilio
            // archivo_curp
            // archivo_fotografia
            // archivo_estudios
            // archivo_otraid
            // archivo_curriculum_personal
            // archivo_rfc

            if ($exitoUpd) {
                return response()->json(['status' => 'success', 'message' => "Documentos Guardados con éxito!"], 200);
            }else{
                return response()->json(['status' => 'error', 'message' => "Error al intentar guardar los documentos!"], 500);
            }

        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }

        // Eliminar fotos si esque lo hay
        // if(isset($db_anio->evidencia_fotografica['url_fotos'])){
        //     $array_fotosbd = $db_anio->evidencia_fotografica['url_fotos'];
        //     if(is_array($array_fotosbd) && count($array_fotosbd) > 0){
        //         for ($i=0; $i < count($array_fotosbd); $i++) {
        //             $filePath = 'uploadFiles'.$array_fotosbd[$i];
        //             if (Storage::exists($filePath)) {
        //                 Storage::delete($filePath);
        //             } else { return response()->json(['mensaje' => "¡Error!, Documento no encontrado ->".$filePath]); }
        //         }
        //     }
        // }

    }


}
