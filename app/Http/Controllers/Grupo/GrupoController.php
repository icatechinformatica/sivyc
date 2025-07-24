<?php

namespace App\Http\Controllers\Grupo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index(){}

    public function create()
    {
        return view('grupos.create');
    }

    public function store()
    {
        dd('Registrando grupo...');
    }

    public function asignarAlumnos()
    {
        return view('grupos.asignar_alumnos');
    }
}
