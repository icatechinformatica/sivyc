<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers;

use App\Models\pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Redirect,Response;

class PagoController extends Controller
{
    public function crear_pago()
    {
        return view('layouts.pages.frmpago');
    }
}
