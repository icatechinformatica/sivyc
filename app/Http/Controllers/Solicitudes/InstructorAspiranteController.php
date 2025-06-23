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

    public function convocar(Request $request)
    {
        $direccionUnidad = null;
        $id = $request->input('id');
        $aspirante = pre_instructor::find($id);
        $aspirante->status = 'EN CAPTURA';
        $aspirante->turnado = 'UNIDAD';
        $aspirante->fecha_entrevista = $request->input('fecha_entrevista');
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

        $aspirante->nrevision = $unidad_inicial . '-' . date('Y') . '-' . $consecutive;
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
        ];

        try {
            $response = $this->whatsapp_convocado_msg($infowhats);
        } catch (\Exception $e) {
            $response = [
                'status' => false,
                'message' => 'Error al enviar mensaje: ' . $e->getMessage(),
            ];
        }

        $aspirante->save();

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
            $responsewsp = $this->whatsapp_rechazo_msg($aspirante);
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

    public function whatsapp_convocado_msg($instructor)
    {
        $plantilla = "Asunto: Resultado del Proceso de Selección de Instructores\n\nEstimado(a) {{nombre}}, Aspirante a Instructor Externo del ICATECH:\n\nPor medio de la presente, le informamos que ha sido seleccionado(a) para continuar a la siguiente etapa del proceso de selección de instructores externos, la cual consiste en la entrevista personal y el cotejo de documentación en la Unidad de Capacitación {{unidad}}, con la finalidad de corroborar la documentación cargada en el sistema y con base en el soporte documental validar la especialidad que le corresponde.\nLe solicitamos presentarse el día {{fecha}}, en nuestras oficinas ubicadas en {{direccionUnidad}}\nDeberá llevar consigo en copia legible los siguientes documentos:\n[Lista de documentos requeridos: CV Personal, certificados de estudios (secundaria, preparatoria, licenciatura, maestría, doctorado), constancias de cursos, acta de nacimiento, Identificación oficial (Preferentemente INE), CURP (Del mes en curso), comprobante de domicilio, constancia de situación fiscal con Régimen de Sueldos y Salarios e Ingresos Asimilados a Salarios, con actividad económica Asalariado (Del mes en curso), Caratula del Estado de Cuenta Bancario, etc.]\nAgradecemos su interés en formar parte de nuestro equipo y le recordamos que la puntualidad y la presentación de la documentación completa son requisitos indispensables para continuar en el proceso.\nQuedamos atentos a cualquier duda. Sea usted bienvenido a esta familia Icatech.\n\nAtentamente,\nDR. CÉSAR ARTURO ESPINOSA MORALES\nDIRECTOR GENERAL DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS";
        $resultados = [];

        $fecha_formateada = Carbon::parse($instructor['fecha'])->translatedFormat('j \d\e F \d\e\l Y');
        $telefono_formateado = '521'.$instructor['telefono'];
        // Reemplazar variables en plantilla
        $mensaje = str_replace(
            ['{{nombre}}', '{{unidad}}', '{{fecha}}', '{{direccionUnidad}}'],
            [$instructor['nombre'], $instructor['unidad'], $fecha_formateada, $instructor['direccionUnidad']],
            $plantilla
        );

        $callback = $this->whatsapp_msg($telefono_formateado, $mensaje);

        return $callback;
    }
    private function whatsapp_rechazo_msg($aspirante)
    {
        $plantilla = "Asunto: Resultado del Proceso de Selección de Instructores\n\n Estimado(a) {{nombre}}, Aspirante a Instructor Externo del ICATECH:\n\n Agradecemos sinceramente su interés y participación en la convocatoria para la selección de instructores externos del ICATECH. Despues de revisar cuidadosamente los perfiles recibidos, lamentamos informarle que en esta ocasión no ha sido seleccionado(a) para continuar a la segunda etapa del proceso. Valoramos el tiempo y el esfuerzo que dedicó al presentar su postulación, y lo(a) invitamos cordialmente a participar en futuras convocatorias\n\nLe reiteramos nuestro agradecimiento por su disposición y compromiso con la formación y el desarrollo profesional. \n\nAtentamente,\nDR. CÉSAR ARTURO ESPINOSA MORALES\nDIRECTOR GENERAL DEL INSTITUTO DE CAPACITACIÓN Y VINCULACIÓN TECNOLÓGICA DEL ESTADO DE CHIAPAS";
        $nombre = $aspirante->nombre . ' ' . $aspirante->apellidoPaterno . ' ' . $aspirante->apellidoMaterno;
        $telefono_formateado = '521'.$aspirante->telefono;

        $mensaje = str_replace(
            ['{{nombre}}'],
            [$nombre],
            $plantilla
        );

        $callback = $this->whatsapp_msg($telefono_formateado, $mensaje);

        return $callback;
    }

    private function whatsapp_msg($telefono_formateado, $mensaje)
    {
        $response = Http::post('https://mensajeria.icatech.gob.mx/send-message', [
            'number' => $telefono_formateado,
            'message' => $mensaje,
        ]);

        $resultados[] = [
            'numero' => $telefono_formateado,
            'status' => $response->successful(),
            'respuesta' => $response->json(),
        ];

        return $response->json($resultados);
    }
}

