<?php
/* Creador: Orlando Chavez */
namespace App\Http\Controllers\Solicitudes;

use Illuminate\Http\Request;
use Redirect,Response;
use App\Http\Controllers\Controller;
use PDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;
use App\Models\tbl_unidades;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SolApoyoController extends Controller
{
    public function index(Request $request)
    {
        return view('layouts.pages.ranking_solicitudes_apoyo');
    }

}
