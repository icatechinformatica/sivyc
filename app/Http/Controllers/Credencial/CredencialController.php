<?php

namespace App\Http\Controllers\Credencial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CredencialesInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        return view('credencial.credencial', compact('getAllFuncionarios'))->render();
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
        return view('credencial.detalle_credencial')->render();
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
        $result = $this->credencial->generarQrCode();
        $imageData = $result->getString();
        $qrCodeBase64 = base64_encode($imageData);
        $data = [
            'qrCodeBase64' => $qrCodeBase64,
        ];
        return view('credencial.credencial', $data);
        // return '<img src="data:image/png;base64,' . $qrCodeBase64 . '" alt="QR Code">';
    }
}
