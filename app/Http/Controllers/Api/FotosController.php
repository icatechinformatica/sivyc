<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tbl_curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exceptions\TokenInvalidException;

class FotosController extends Controller
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

    protected function img_upload($img, $id, $nom, $anio)
    {
        // # nuevo nombre del archivo
        $tamanio = $img->getSize();
        $extensionFile = $img->getClientOriginalExtension();
        $imgFile = trim($nom . "_" . $id . '.' .$extensionFile);
        $directorio = '/' . $anio . '/evidenciafotos/' . $id . '/'.$imgFile;
        $img->storeAs('/uploadFiles/'.$anio.'/evidenciafotos/'.$id, $imgFile);
        $imgUrl = Storage::url('/uploadFiles' . $directorio);
        return [$imgUrl, $directorio];
    }

    public function recibirimg (Request $request) {
        $idcurso = $request->idcurso;
        $arrayUrlFotos = [];
        $token = $this->token;

        if (empty($idcurso)) {throw new \Exception('Fallo en la clave');}

        $db_anio = tbl_curso::select('evidencia_fotografica', DB::raw('EXTRACT(YEAR FROM inicio) as anio'))->where('id', $idcurso)->first();
        if (!$db_anio) { throw new \Exception('Curso no encontrado para la clave proporcionada');}
        $anio = $db_anio->anio;

        // Eliminar fotos si esque lo hay
        if(isset($db_anio->evidencia_fotografica['url_fotos'])){
            $array_fotosbd = $db_anio->evidencia_fotografica['url_fotos'];
            if(is_array($array_fotosbd) && count($array_fotosbd) > 0){
                for ($i=0; $i < count($array_fotosbd); $i++) {
                    $filePath = 'uploadFiles'.$array_fotosbd[$i];
                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    } else { return response()->json(['mensaje' => "¡Error!, Documento no encontrado ->".$filePath]); }
                }
            }
        }

        if ($request->hasFile('imagenes')) {
            #Recibimos la imagen y la guardamos en la base de datos
            $imagenes = $request->file('imagenes');

            #Procedemos a guardar la imagen
            try {
                for ($i=0; $i < count($imagenes); $i++) {
                    $url_foto = $this->img_upload($imagenes[$i], $idcurso, 'foto'.($i+1), $anio);
                    array_push($arrayUrlFotos, $url_foto[1]);
                }
            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            }

            ##Agregamos el md5 a las imagenes
            $arrayFotoMd5 = [];
            for ($i=0; $i < count($arrayUrlFotos); $i++) {
                try {
                    $fotmd5  = $this->generarMD5DeImagen($arrayUrlFotos[$i]);
                    array_push($arrayFotoMd5, $fotmd5);
                } catch (\Throwable $th) {
                    return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
                }
            }

            ## Guardamos las url en la base de datos
            try {
                $curso = tbl_curso::find($idcurso);
                $json = $curso->evidencia_fotografica;
                $json['url_fotos'] = $arrayUrlFotos;
                $json['md5_fotos'] = $arrayFotoMd5;
                $curso->evidencia_fotografica = $json;
                $curso->save();
                ##Eliminamos el token en la bd
                DB::table('tokens_sendimg')->where('token', $token)->delete();
                $respuesta = ['status' => 'success', 'message' => "¡Imagenes Guardadas con exito!"];
            } catch (\Throwable $th) {
                return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
            }

        }else{
            $respuesta = ['status' => 'success', 'message' => 'No existe imagen para guardar '.$idcurso];
        }

        return response()->json($respuesta, 200);

    }

    #Generar MD5 de las fotos.
    function generarMD5DeImagen($rutaImagen) {
        if (Storage::exists('uploadFiles'.$rutaImagen)) {
            $md5Hash = md5_file(storage_path('app/public/uploadFiles'.$rutaImagen));
            return $md5Hash;
        } else {
            return null;
        }
    }
}
