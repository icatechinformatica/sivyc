<?php

namespace App\Services\Grupo;

use App\Models\Grupo;
use App\Models\Estatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoEstatusService
{

    /**
     * Verifica si el grupo puede transicionar al nuevo estatus según la regla de IDs adyacentes (+/-1).
     * - Si no tiene estatus previo, se permite la transición inicial.
     * - Si el estatus actual es final, no se permite.
     */
    public function puedeTransicionar(Grupo $grupo, int $nuevo_estatus_id): bool
    {
        $estatusActual = $grupo->estatusActual();

        // ? Si no hay estatus previo se permite
        if (!$estatusActual) {
            return true;
        }

        // ? Si el actual es final no se permite
        if ($estatusActual->final) {
            return false;
        }

        // Adyacentes desde el modelo (permitiendo finales como destino)
        return $grupo->estatusAdyacentes(true)->pluck('id')->contains($nuevo_estatus_id);
    }

    /**
     * Compatibilidad: alias del método booleano. Devuelve true/false 
     */
    public function transicionesPosibles(Grupo $grupo, int $nuevo_estatus_id): bool
    {
        return $this->puedeTransicionar($grupo, $nuevo_estatus_id);
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
    public function cambiarEstatus(Grupo $grupo, int $nuevo_estatus_id, ?string $seccion = null)
    {
        // Validar existencia del estatus destino
        $estatusDestino = Estatus::find($nuevo_estatus_id);
        if (!$estatusDestino) {
            return response()->json([
                'error' => 'El estatus destino no existe.'
            ], 404);
        }

        $estatusActual = $grupo->estatusActual();

        // No-op
        if ($estatusActual && (int)$estatusActual->id === (int)$nuevo_estatus_id) {
            return response()->json([
                'error' => 'El grupo ya se encuentra en el estatus solicitado.'
            ], 400);
        }

        // Validación de transición
        if (!$this->puedeTransicionar($grupo, $nuevo_estatus_id)) {
            return response()->json([
                'error' => 'Transición no permitida. El estatus actual es final o la transición no es válida.'
            ], 400);
        }

        // Persistir cambio en transacción
        DB::transaction(function () use ($grupo, $estatusActual, $nuevo_estatus_id, $estatusDestino, $seccion) {
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
                // Campos no esenciales se guardan como null por defecto (observaciones/memorandum/ruta_documento)
            ]);
        });

        return response()->json([
            'ok' => true,
            'mensaje' => 'Estatus actualizado correctamente.'
        ]);
    }
}
