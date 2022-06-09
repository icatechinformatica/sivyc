<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use App\Models\curso;
use Illuminate\Http\Request;

class PaqueteriaDidacticaController extends Controller
{
    //
    public function index($idCurso)
    {

        $curso = curso::toBase()->where('id', $idCurso)->first();
    
        // dump($curso );
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas',compact('idCurso', 'curso'));
    }

    public function store(Request $request, $idCurso)
    {
        //
        dd($request->toArray());
    }
}
