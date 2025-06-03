<?php

namespace App\Http\Controllers\Solicitudes;

use App\Models\instructor;
use App\Models\pre_instructor;
use App\Models\cursoVALIDADO;
use App\Models\curso;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\InstructorPerfil;
use App\Models\tbl_unidades;
use App\Models\especialidad;
use App\Models\estado_civil;
use App\Models\status;
use App\Models\especialidad_instructor;
use App\Models\criterio_pago;
use App\Models\instructor_history;
use App\Models\Inscripcion;
use App\Models\localidad;
use App\Models\Calificacion;
use App\Models\tbl_curso;
use App\Models\Banco;
use App\Models\pago;
use App\Models\pais;
use App\Models\contratos;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FormatoTReport;
use ZipArchive;
use PDF;
use App\User;

class InstructorAspiranteController extends Controller
{

    public function index(Request $request)
    {
        // dd('asd');
        $data = pre_instructor::WhereIn('status',['ENVIADO','PREVALIDADO','COTEJADO'])->Get();
        return view('solicitudes.instructorAspirante.buzoninstructoraspirante', compact('data'));
    }

    public function prevalidar(Request $request)
    {
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $aspirante->status = 'PREVALIDADO';
        $aspirante->save();

        // Redirect back to the index with a success message
        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante prevalidado correctamente.');
    }

    public function cotejar(Request $request)
    {
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $aspirante->status = 'COTEJADO';
        $aspirante->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante cotejado correctamente.');
    }

    public function aprobar(Request $request)
    {
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $aspirante->status = 'EN FIRMA';
        $aspirante->turnado = 'UNIDAD';
        $aspirante->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante aprobado correctamente.');
    }
}

