<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EspecialidadService;

class EspecialidadController extends Controller
{
    public function __construct() {}

    public function index()       { return view('especialidades.index', ['productos' => $this->service->listar()]); }
    public function create()      { return view('especialidades.create'); }
    public function store(Request $r) { $this->service->crear($r->all()); return redirect()->route('especialidad.index'); }
    public function edit($id)     { return view('especialidades.edit', ['especialidad' => $this->service->obtener($id)]); }
    public function update(Request $r, $id) { $this->service->actualizar($id, $r->all()); return redirect()->route('especialidad.index'); }
    public function destroy($id)  { $this->service->eliminar($id); return redirect()->route('especialidad.index'); }
}