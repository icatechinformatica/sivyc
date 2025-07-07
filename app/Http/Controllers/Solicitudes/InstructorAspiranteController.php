<?php

namespace App\Http\Controllers\Solicitudes;

use App\Models\instructor;
use App\Models\pre_instructor;
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
use App\User;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Hash;
class InstructorAspiranteController extends Controller
{
    public function index(Request $request)
    {
        $unidades = tbl_unidades::select('ubicacion')->distinct()->pluck('ubicacion');
        $especialidades = especialidad::pluck('nombre', 'id')->toArray();
        $total_aspirantes = pre_instructor::WhereNotNull('semaforo')->count();
        $total_enviados = pre_instructor::WhereRaw("semaforo::jsonb @> '[\"ENVIADO\"]'")->count();
        $total_convocados = pre_instructor::WhereRaw("semaforo::jsonb @> '[\"ENVIADO\"]'")->Where('status','EN CAPTURA')->count();
        $query = pre_instructor::whereIn('status', ['ENVIADO', 'PREVALIDADO', 'CONVOCADO']);

        if ($request->filled('unidad')) {
            $query->where('unidad_asignada', $request->unidad);
        }

        $data = $query->OrderBy('nombre', 'ASC')->get();

        return view('solicitudes.instructorAspirante.buzoninstructoraspirante', compact('data', 'unidades','especialidades', 'total_aspirantes','total_enviados','total_convocados'));
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

    public function convocar(Request $request)
    {
        $direccionUnidad = null;
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $ins_oficial = instructor::Where('curp', $aspirante->curp)->First();

        $aspirante->status = $ins_oficial->status = 'EN CAPTURA';
        $aspirante->turnado = $ins_oficial->turnado = 'UNIDAD';
        $aspirante->fecha_entrevista = $request->input('fecha_entrevista');
        $aspirante->numero_control = 'Pendiente';

        $ins_oficial->nombre = $aspirante->nombre;
        $ins_oficial->apellidoPaterno = $aspirante->apellidoPaterno;
        $ins_oficial->apellidoMaterno = $aspirante->apellidoMaterno;
        $ins_oficial->estado = TRUE;

        //verifica que el id_oficial sea diferente de 0
        if($aspirante->id_oficial == 0) {
            if($ins_oficial) {
                $aspirante->id_oficial = $ins_oficial->id;
            }

        }

        //verifica si ya tiene especialidades validadas anteriormente y las cambia a REVALIDACION EN CAPTURA
        if(!is_null($aspirante->data_especialidad)) {
            $data = $aspirante->data_especialidad;
            foreach($data as $key => $especialidad)
            {
                if($especialidad['status'] == 'VALIDADO') {
                    $especialidad['status'] = 'REVALIDACION EN CAPTURA';
                    $especialidad['fecha_solicitud'] = $especialidad['fecha_validacion'] = $especialidad['memorandum_solicitud'] = $especialidad['memorandum_validacion'] = null;
                    $especialidad['unidad_solicita'] = $aspirante->unidad_asignada;
                }
                $data[$key] = $especialidad;
            }
            $aspirante->data_especialidad = $data;
        }
        // termina proceso de revalidacion de especialidades

        //proceso para generar nrevision
        $unidad_inicial = substr($aspirante->unidad_asignada,0,2);
        if($unidad_inicial === 'SA') { $unidad_inicial = 'SC'; }
        $last_nrevision = pre_instructor::Where('nrevision', 'like', $unidad_inicial.'-'.date('Y').'-%')
            ->orderBy('nrevision', 'desc')
            ->value('nrevision');

        if ($last_nrevision) {
            $last_consecutive = explode('-', $last_nrevision)[2];
            $consecutive = str_pad((int)$last_consecutive + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $consecutive = '0001';
        }

        $aspirante->nrevision = $ins_oficial->nrevision = $unidad_inicial . '-' . date('Y') . '-' . $consecutive;
        //termina proceso de generacion de nrevision

        //proceso para enviar mensaje de WhatsApp
        $direcbd = explode('*',tbl_unidades::where('unidad', $aspirante->unidad_asignada)->value('direccion'));
        foreach ($direcbd as $key => $value) {
            $direccionUnidad = $direccionUnidad . $value;
            if(str_contains($value, 'C.P.')) {
                break;
            }
        }

        $infowhats = [
            'nombre' => $aspirante->nombre . ' ' . $aspirante->apellidoPaterno . ' ' . $aspirante->apellidoMaterno,
            'unidad' => $aspirante->unidad_asignada,
            'fecha' => $aspirante->fecha_entrevista,
            'telefono' => $aspirante->telefono,
            'direccionUnidad' => $direccionUnidad,
            'sexo' => $aspirante->sexo,
            'telefono_unidad' => tbl_unidades::where('unidad', $aspirante->unidad_asignada)->value('telefono'),
        ];

        $response = $this->whatsapp_convocado_msg($infowhats, app(WhatsAppService::class));
        // Check if the response indicates an error
        if (isset($response['status']) && $response['status'] === false) {
            // Handle the error as you wish
            return redirect()->route('aspirante.instructor.index')
                ->with('error', 'Error al enviar mensaje de WhatsApp: ' . ($response['respuesta']['error'] ?? 'Error desconocido'));
        }
        //termina proceso de envio de mensaje de WhatsApp

        $aspirante->save();
        $ins_oficial->save();

        return redirect()->route('aspirante.instructor.index')
            ->with('success', 'Aspirante convocado correctamente.');
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
            $query->whereIn('status', ['ENVIADO', 'PREVALIDADO', 'CONVOCADO']);
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

        // Send WhatsApp message if rejected in PREVALIDADO
        if ($context === 'PREVALIDADO') {
            $infowhats = [
                'nombre' => $aspirante->nombre . ' ' . $aspirante->apellidoPaterno . ' ' . $aspirante->apellidoMaterno,
                'telefono' => $aspirante->telefono,
                'sexo' => $aspirante->sexo,
            ];
            $response = $this->whatsapp_rechazo_msg($infowhats, app(WhatsAppService::class));
            // Check if the response indicates an error
            if (isset($response['status']) && $response['status'] === false) {
                // Handle the error as you wish
                return redirect()->route('aspirante.instructor.index')
                    ->with('error', 'Error al enviar mensaje de WhatsApp: ' . ($response['respuesta']['error'] ?? 'Error desconocido'));
            }
        }
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
            $especialidadNombres = $perfilProfesional = $areaCarrera = [];
            if (is_array($row->data_especialidad)) {
                foreach ($row->data_especialidad as $esp) {
                    if (isset($especialidades[$esp['especialidad_id']])) {
                        $especialidadNombres[] = $especialidades[$esp['especialidad_id']];
                    }
                }
            }
            if (is_array($row->data_perfil)) {
                foreach ($row->data_perfil as $per) {
                    $perfilProfesional[] = $per['grado_profesional'] . ' EN ' . $per['carrera'];
                    $areaCarrera[] = $per['area_carrera'];
                }
            }
            $exportData[] = [
                $row->nombre . ' ' . $row->apellidoPaterno . ' ' . $row->apellidoMaterno,
                $row->unidad_asignada,
                implode(', ', $perfilProfesional),
                implode(', ', $especialidadNombres),
                implode(', ', $areaCarrera),
                $row->updated_at,
                $row->status
            ];
        }

        return Excel::download(new \App\Exports\AspirantesExport($exportData), 'aspirantes_'.$status.'.xlsx');
    }

    public function whatsapp_convocado_msg($instructor, WhatsAppService $whatsapp)
    {
        $plantilla = DB::Table('tbl_wsp_plantillas')->Where('nombre', 'aspirante_instructor_convocado')->First();
        $fecha_formateada = Carbon::parse($instructor['fecha'])->translatedFormat('j \d\e F \d\e\l Y');
        $hora_formateada = Carbon::parse($instructor['fecha'])->format('H:i');
        // Reemplazar variables en plantilla
        $mensaje = str_replace(
            ['{{nombre}}', '{{unidad}}', '{{fecha}}', '{{horas}}', '{{direccionUnidad}}','{{telefono_unidad}}','\n'],
            [$instructor['nombre'], $instructor['unidad'], $fecha_formateada, $hora_formateada, $instructor['direccionUnidad'], $instructor['telefono_unidad'],"\n"],
            $plantilla->plantilla
        );

        if ($instructor['sexo'] == 'MASCULINO') {
            $mensaje = str_replace(['(a)'], [''], $mensaje);
        } else {
            $mensaje = str_replace(['o(a)','r(a)'], ['a','ra'], $mensaje);
        }

        $callback = $whatsapp->cola($instructor['telefono'], $mensaje, $plantilla->prueba);

        return $callback;
    }
    private function whatsapp_rechazo_msg($instructor, WhatsAppService $whatsapp)
    {
        $plantilla = DB::Table('tbl_wsp_plantillas')->Where('nombre', 'aspirante_instructor_rechazado')->First();

        $mensaje = str_replace(
            ['{{nombre}}'],
            [$instructor['nombre']],
            $plantilla->plantilla
        );

        if ($instructor['sexo'] == 'MASCULINO') {
            $mensaje = str_replace(['(a)'], [''], $mensaje);
        } else {
            $mensaje = str_replace(['o(a)','r(a)'], ['a','ra'], $mensaje);
        }

        $callback = $whatsapp->cola($telefono_formateado, $mensaje, $plantilla->prueba);

        return $callback;
    }

    public function whatsapp_rechazo_masivo() {
        set_time_limit(0);
        $id_rechazados = [
            ]; //aqui meter los ids de los rechazados por tandas
        $rechazados = pre_instructor::WhereIn('id',$id_rechazados)->Select('id','telefono','nombre',"apellidoPaterno","apellidoMaterno",'sexo')->Get();
        foreach($rechazados as $key => $aspirante) {
            $infowhats = [
                'nombre' => $aspirante->nombre . ' ' . $aspirante->apellidoPaterno . ' ' . $aspirante->apellidoMaterno,
                'telefono' => $aspirante->telefono,
                'sexo' => $aspirante->sexo
            ];

            try {
                $response = $this->whatsapp_rechazo_msg($infowhats, app(WhatsAppService::class));
            } catch (\Exception $e) {
                $response = [
                    'status' => false,
                    'message' => 'Error al enviar mensaje: ' . $e->getMessage(),
                ];

            }
            sleep('5');
        }
        dd('complete');
    }

    //funcion que ira en el controlador de superadministrador para enviar mensajes de WhatsApp de restablecimiento de contraseña a los instructores
    public function whatsapp_restablecer_pwd() {
        dd('prueba');
        $user = DB::Connection('mysql')->Table('users')->Where('curp', 'MAMG570608HCSTRL04')->First();
        $instructor = DB::Table('instructores')->Where('curp', 'MAMG570608HCSTRL04')->Select('rfc','nombre',"apellidoPaterno","apellidoMaterno",'telefono')->First();
        $user->password = Hash::make($instructor->rfc);
        $plantilla = DB::Table('tbl_wsp_plantillas')->Where('nombre', 'restablecer_pwd_instructor')->Value('plantilla');
        $telefono_formateado = '521'.$instructor->telefono;

        $mensaje = str_replace(
            ['{{nombre}}','{{usuario}}','{{pwd}}','\n'],
            [$instructor->nombre,$user->email,$instructor->rfc,"\n"],
            $plantilla
        );
        $whatsapp = app(WhatsAppService::class);
        $callback = $whatsapp->send($telefono_formateado, $mensaje);
        dd($callback);
        $user->save();
    }
}

