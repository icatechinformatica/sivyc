<?php

namespace App\Services\Grupo;

use App\Models\Grupo;
use App\Models\Estatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoEstatusService
{

    /**
     * Verifica si el grupo puede transicionar al nuevo estatus según la regla de IDs adyacentes (+/-1)
     * y devuelve un resultado con mensaje para el usuario.
     *
     * Reglas principales:
     * - Si no tiene estatus previo, se permite la transición inicial.
     * - Si el estatus actual es final, no se permite.
     * - Solo se permite transicionar a estatus adyacentes (según modelo), incluyendo finales como destino.
     * - Para pasar a REVISIÓN (id=2) debe cumplir el mínimo de alumnos.
     *
     * @return array{ok: bool, mensaje: string}
     */
    public function puedeTransicionar(Grupo $grupo, int $nuevo_estatus_id): array
    {
        $estatusActual = $grupo->estatusActual();

        // ? Si no hay estatus previo se permite
        if (!$estatusActual) {
            return [
                'ok' => true,
                'mensaje' => 'Transición inicial permitida.'
            ];
        }

        // ? Si el actual es final no se permite
        if ($estatusActual->final) {
            $tieneAllAccess = auth()->user()->roles->contains('especial', 'all-access');

            if ($tieneAllAccess) {
                return [
                    'ok' => true,
                    'mensaje' => 'Transición permitida para usuarios con acceso total.'
                ];
            }
            return [
                'ok' => false,
                'mensaje' => 'No es posible cambiar el estatus porque el actual es final.'
            ];
        }

        // ? Validar que el registro de grupo este completo

        // ? Hora del curso cubiertas

        $curso = DB::table('vista_cursos')->where('id_curso', $grupo->id_curso)->first();
        if ($grupo->horasTotales() < $curso->horas) {
            return [
                'ok' => false,
                'mensaje' => 'Para enviar a Revisión se requiere cubrir todas las horas del curso.'
            ];
        }

        // ? Instructor asignado
        if (!$grupo->tieneInstructorAsignado()) {
            return [
                'ok' => false,
                'mensaje' => 'Para enviar a Revisión se requiere asignar un instructor.'
            ];
        }


        // ? Validar transición a REVISIÓN
        if ($nuevo_estatus_id === 2) { // Sabiendo que el ID de REVISIÓN es 2
            if (!$this->permitirRevision($grupo)) {
                return [
                    'ok' => false,
                    'mensaje' => 'Para enviar a Revisión se requiere cumplir el mínimo de alumnos: ' . $grupo->servicio->alumnos_min
                ];
            }
        }

        // Adyacentes desde el modelo (permitiendo finales como destino)
        $permitidos = $grupo->estatusAdyacentes(true)->pluck('id');
        if ($permitidos->contains($nuevo_estatus_id)) {
            return [
                'ok' => true,
                'mensaje' => 'Transición permitida.'
            ];
        }

        return [
            'ok' => false,
            'mensaje' => 'Transición no permitida: solo puedes avanzar a un estatus adyacente.'
        ];
    }

    /**
     * Compatibilidad: alias booleano. Devuelve true/false tomando el campo 'ok'.
     */
    public function transicionesPosibles(Grupo $grupo, int $nuevo_estatus_id): bool
    {
        $resultado = $this->puedeTransicionar($grupo, $nuevo_estatus_id);
        return (bool)($resultado['ok'] ?? false);
    }

    /**
     * Cambia el estatus del grupo creando un nuevo registro en la pivote tbl_grupo_estatus.
     * Marca el estatus anterior como no-último.
     * Nota: es_ultimo_estatus en la pivote se establece en true SOLO si el estatus destino tiene final = true.
     *
     * Pivote establecido automáticamente (sin columna 'seccion' en pivote):
     * - fecha_cambio: now()
     * - id_usuario: Auth::id()
     * - es_ultimo_estatus: (bool) Estatus->final
     * Además, si se recibe $seccion, se actualiza Grupo.seccion_captura.
     */
    public function cambiarEstatus(Grupo $grupo, int $nuevo_estatus_id, ?string $seccion = null, $observacion = null)
    {
        // ? Validar existencia del estatus destino
        $estatusDestino = Estatus::find($nuevo_estatus_id);
        if (!$estatusDestino) {
            return response()->json(['error' => 'El estatus destino no existe.'], 404);
        }

        $estatusActual = $grupo->estatusActual();

        // ? No-op
        if ($estatusActual && (int)$estatusActual->id === (int)$nuevo_estatus_id) {
            return response()->json(['error' => 'El grupo ya se encuentra en el estatus solicitado.'], 400);
        }

        // ? Validación de transición con mensaje detallado
        $validacion = $this->puedeTransicionar($grupo, $nuevo_estatus_id);
        if (!$validacion['ok']) {
            return response()->json(['error' => $validacion['mensaje']], 400);
        }

        // La observación del turnado se almacena en la pivote tbl_grupo_estatus (campo 'observaciones').

        // Persistir cambio en transacción
        DB::transaction(function () use ($grupo, $estatusActual, $nuevo_estatus_id, $estatusDestino, $seccion, $observacion) {
            // Actualizar seccion_captura del grupo si se proporciona
            if ($seccion !== null && $seccion !== '') {
                $grupo->seccion_captura = $seccion;
                $grupo->save();
            }
            // Marcar estatus previo como no-último
            if ($estatusActual) {
                $grupo->estatus()->updateExistingPivot($estatusActual->id, [
                    'es_ultimo_estatus' => false,
                ]);
            }

            // Adjuntar nuevo estatus como último
            $grupo->estatus()->attach($nuevo_estatus_id, [
                'fecha_cambio' => now(),
                'es_ultimo_estatus' => (bool) $estatusDestino->final,
                'id_usuario' => Auth::id(),
                'observaciones' => $observacion,
                // Campos no esenciales se guardan como null por defecto (observaciones/memorandum/ruta_documento)
            ]);
        });

        return response()->json([
            'ok' => true,
            'mensaje' => 'Estatus actualizado correctamente.'
        ]);
    }

    public function permitirRevision(Grupo $grupo): bool
    {
        // Para pasar a revision es necesario tener el minimo de alumnos en base al tipo de servicio
        $minAlumnos = $grupo->servicio->alumnos_min ?? 1;
        $numAlumnos = $grupo->alumnos()->count();

        return $numAlumnos >= $minAlumnos;
    }
}
