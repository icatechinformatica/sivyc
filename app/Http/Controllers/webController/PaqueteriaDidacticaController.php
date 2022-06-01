<?php

namespace App\Http\Controllers\webController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaqueteriaDidacticaController extends Controller
{
    //
    public function index()
    {
        return view('layouts.pages.paqueteriasDidacticas.paqueterias_didacticas');
    }
}
