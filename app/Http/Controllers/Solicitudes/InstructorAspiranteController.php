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
use ZipArchive;
use PDF;
use App\User;

class InstructorAspiranteController extends Controller
{

    public function index(Request $request)
    {
        $unidades = tbl_unidades::select('ubicacion')->distinct()->pluck('ubicacion');
        $especialidades = especialidad::pluck('nombre', 'id')->toArray();
        $total_aspirantes = pre_instructor::WhereNotNull('semaforo')->count();
        $total_enviados = pre_instructor::WhereRaw("semaforo::jsonb @> '[\"ENVIADO\"]'")->count();
        $query = pre_instructor::whereIn('status', ['ENVIADO', 'PREVALIDADO', 'CONVOCADO']);

        if ($request->filled('unidad')) {
            $query->where('unidad_asignada', $request->unidad);
        }

        $data = $query->get();

        return view('solicitudes.instructorAspirante.buzoninstructoraspirante', compact('data', 'unidades','especialidades', 'total_aspirantes','total_enviados'));
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
        $aspirante->status = 'CONVOCADO';
        $aspirante->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante convocado  correctamente.');
    }

    public function aprobar(Request $request)
    {
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $aspirante->status = 'EN FIRMA';
        $aspirante->turnado = 'UNIDAD';

        // --- nrevision logic start ---
        $unidad = strtoupper($aspirante->unidad_asignada);
        if ($unidad === 'SAN CRISTOBAL') {
            $prefix = 'SC';
        } else {
            $prefix = substr($unidad, 0, 2);
        }
        $year = date('Y');
        $base = "{$prefix}-{$year}-";

        // Find last nrevision with this prefix and year
        $last = pre_instructor::where('nrevision', 'like', "{$base}%")
            ->orderByDesc('nrevision')
            ->first();

        if ($last && preg_match('/-(\d{4})$/', $last->nrevision, $matches)) {
            $consecutive = str_pad(((int)$matches[1]) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $consecutive = '0001';
        }

        $aspirante->nrevision = "{$base}{$consecutive}";
        // --- nrevision logic end ---

        $aspirante->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante aprobado correctamente.');
    }

    public function filter(Request $request)
    {
        $unidad = $request->input('unidad');
        $showRechazados = $request->input('showRechazados', false);
        $status = $request->input('status', 'ENVIADO'); // Get current tab status from request

        $rechazadoStatus = [
            'ENVIADO' => 'RECHAZADO ENVIADO',
            'PREVALIDADO' => 'RECHAZADO PREVALIDADO',
            'CONVOCADO' => 'RECHAZADO CONVOCADO'
        ];

        $query = pre_instructor::query();

        if ($unidad) {
            $query->where('unidad_asignada', $unidad);
        }

        if ($showRechazados) {
            // Only show rechazados for the current status
            $query->where('status', $rechazadoStatus[$status]);
        } else {
            // Only show normal for the current status
            $query->where('status', $status);
        }

        $data = $query->get();
        $especialidades = especialidad::pluck('nombre', 'id')->toArray();

        $html = view('solicitudes.instructorAspirante.partials.tabs', [
            'data' => $data,
            'especialidades' => $especialidades,
            'showRechazados' => $showRechazados,
            'status' => $status // Pass current status to view
        ])->render();
        return response()->json(['html' => $html]);
    }

    public function rechazar(Request $request)
    {
        $id = $request->input('id');
        $observacion = $request->input('observacion');
        $context = $request->input('context');
        $aspirante = pre_instructor::find($id);

        if ($context === 'PREVALIDADO') {
            $aspirante->status = 'RECHAZADO PREVALIDADO';
        } elseif ($context === 'CONVOCADO') {
            $aspirante->status = 'RECHAZADO CONVOCADO';
        } else {
            $aspirante->status = 'RECHAZADO ENVIADO';
        }
        $aspirante->rechazo = $observacion;
        $aspirante->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante rechazado correctamente.');
    }

    public function export(Request $request)
    {
        $unidad = $request->input('unidad');
        $status = $request->input('status', 'ENVIADO');
        $showRechazados = $request->input('showRechazados', false);

        $rechazadoStatus = [
            'ENVIADO' => 'RECHAZADO ENVIADO',
            'PREVALIDADO' => 'RECHAZADO PREVALIDADO',
            'CONVOCADO' => 'RECHAZADO CONVOCADO'
        ];

        $query = pre_instructor::query();

        if ($unidad) {
            $query->where('unidad_asignada', $unidad);
        }

        if ($showRechazados) {
            $query->where(function($q) use ($status, $rechazadoStatus) {
                $q->where('status', $status)
                  ->orWhere('status', $rechazadoStatus[$status]);
            });
        } else {
            $query->where('status', $status);
        }

        $data = $query->get();
        $especialidades = \App\Models\especialidad::pluck('nombre', 'id')->toArray();

        $exportData = [];
        foreach ($data as $row) {
            $especialidadNombres = [];
            if (is_array($row->data_especialidad)) {
                foreach ($row->data_especialidad as $esp) {
                    if (isset($especialidades[$esp['especialidad_id']])) {
                        $especialidadNombres[] = $especialidades[$esp['especialidad_id']];
                    }
                }
            }
            $exportData[] = [
                $row->nombre . ' ' . $row->apellidoPaterno . ' ' . $row->apellidoMaterno,
                $row->unidad_asignada,
                implode(', ', $especialidadNombres),
                $row->updated_at,
                $row->status
            ];
        }

        return Excel::download(new \App\Exports\AspirantesExport($exportData), 'aspirantes_'.$status.'.xlsx');
    }
}

