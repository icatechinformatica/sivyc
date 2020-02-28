<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\webController;

use App\Models\pago;
use App\Models\instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Redirect,Response;
use App\Http\Controllers\Controller;

class PagoController extends Controller
{
    public function index()
    {
        $pago = new pago();
        $data = $pago::where('id', '!=', '0')->latest()->get();
        return view('layouts.pages.vstapago', compact('data'));
    }

    public function crear_pago()
    {
        return view('layouts.pages.frmpago');
    }

    public function modificar_pago()
    {
        return view('layouts.pages.modpago');
    }
}
