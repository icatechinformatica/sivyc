<?php
namespace App\Interfaces;

interface Reporterf001Interface {
    public function index($user);
    public function getReciboQry($unidad);
    public function sentRF001Format($request);
    public function getDetailRF001Format($concentrado);
    public function storeData(array $request);
    public function updateFormatoRf001($request, $id);
    public function storeComment($request);
    public function updateRf001($id);
    public function generarDocumentoPdf($id);
}
