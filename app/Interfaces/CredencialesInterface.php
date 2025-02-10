<?php
namespace App\Interfaces;

interface CredencialesInterface {
    public function generarQrCode($id);
    public function getFuncionarios();
    public function descargarQr($id);
}
