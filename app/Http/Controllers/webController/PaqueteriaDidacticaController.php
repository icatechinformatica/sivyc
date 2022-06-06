<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaqueteriaDidacticaController extends Controller
{
    //
    public function index($idCurso)
    {
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas',compact('idCurso'));
    }

    public function store(Request $request, $idCurso)
    {
        //
        dd($request->toArray());
    }
}
