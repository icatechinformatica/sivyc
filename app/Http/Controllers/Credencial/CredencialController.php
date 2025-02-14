<?php

namespace App\Http\Controllers\Credencial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CredencialesInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CredencialController extends Controller
{
    private CredencialesInterface $credencial;
    public function __construct(CredencialesInterface $credencial) {
        $this->credencial = $credencial;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $getAllFuncionarios = $this->credencial->getFuncionarios();
        $filtro = $request->get('filtroBusqueda');

        $getAllFuncionarios->when(isset($filtro) && $filtro !== '', function ($query) use ($filtro) {
            return $query->where('nombre_trabajador', '=', trim($filtro));
        });

        $query = $getAllFuncionarios->orderBy('clave_empleado', 'ASC')->paginate(15);

        return view('credencial.index', compact('query'))->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $idCodificar = base64_encode($id);
        $result = $this->credencial->generarQrCode($idCodificar);
        $perfil = $this->credencial->getFuncionario($id);
        $avatar = $this->getAvatarUrl($id);
        $imageData = $result->getString();
        $qrCodeBase64 = base64_encode($imageData);
        $data = [
            'qrCodeBase64' => $qrCodeBase64,
            'perfil' => $perfil,
            'id' => $id,
            'avatar' => $avatar ?? null,
        ];
        return view('credencial.detalle_credencial', $data)->render();
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
        $idDecode = base64_decode($id);
        $avatar = $this->getAvatarUrl($idDecode);
        $perfil = $this->credencial->getFuncionario($idDecode);
        return view('credencial.perfil', compact('perfil', 'avatar'))->render();
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

    public function getQrCode($id)
    {
        $result = $this->credencial->generarQrCode($id);
        $imageData = $result->getString();
        $qrCodeBase64 = base64_encode($imageData);
        $data = [
            'qrCodeBase64' => $qrCodeBase64,
        ];
        return view('credencial.credencial', $data);
        // return '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code">';
    }

    public function download($id)
    {
        $descargarQr = $this->credencial->descargarQr($id);
        return response($descargarQr->getString())
        ->header('Content-Type', 'image/png')
        ->header('Content-Disposition', 'attachment; filename="codigo_qr.png"');
    }

    public function uploadPhoto(Request $request) : JSONResponse
    {
        // Configurar datos dinámicos
        $remplazar = true;
        // Validar la imagen
        $request->validate([
            'photo' => 'required|image|mimes:png,jpg,jpeg,gif|max:4096',
        ]);

        $file = $request->file('photo');

        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $data = [
            'archivo' => $file, // Instancia UploadedFile
            'remplazar' => $remplazar ?? false,
            'carpeta' => $request->get('curp'),
        ];

        return response()->json(
            [
                'data' => $this->credencial->setProfilePicture($data)
            ],
            Response::HTTP_CREATED
        );
    }

    public function getAvatarUrl($id)
    {
        $perfil = $this->credencial->getFuncionario($id);
        $pathFile = '2025/funcionarios/'.$perfil->curp_usuario;
        $archivos = Storage::files($pathFile); // Obtiene los archivos en la carpeta
        if (!empty($archivos)) {
            $nombreArchivo = pathinfo($archivos[0], PATHINFO_FILENAME); // Nombre sin extensión
            $extension = pathinfo($archivos[0], PATHINFO_EXTENSION); // Extensión

            $path = $pathFile."/".$nombreArchivo.".".$extension;

            return Storage::url($path);
        }
    }
}
