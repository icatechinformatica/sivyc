<?php
namespace App\Interfaces;
use Illuminate\Http\Request;

interface RequisicionRepositoryInterface {
    public function searchPartidaPresupuestal(Request $request);
    public function searchMateriales(Request $req);
    public function createRequisition(Request $req);
    public function loadRequisicion();
    public function loadItems($idReq);
    public function checkRequisicion($email);
    public function addJustification($req);
    public function getJustificacion($id);
    public function destroyReq($id);
    public function getCatUnidades();
    public function updateReq(Request $request, $id);
    public function uploadFile(Request $request);
}
