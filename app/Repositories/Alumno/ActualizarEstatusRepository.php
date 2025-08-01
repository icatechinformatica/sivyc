<?php

namespace App\Repositories\Alumno;

use App\Models\Alumno;
use App\Models\Estatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Interfaces\ActualizarEstatusRepositoryInterface;

class ActualizarEstatusRepository implements ActualizarEstatusRepositoryInterface
{
    public function actualizarEstatus(int $alumnoId, int $nuevoEstatus, ?string $seccion = null): bool
    {
        try {

            $alumno = Alumno::find($alumnoId);
            
            if (!$alumno) return false; 

            $alumno->estatus()->sync([$nuevoEstatus]);
            if ($seccion) {
                $alumno->estatus()->updateExistingPivot($nuevoEstatus, [
                    'secciones' => json_encode([$seccion => ['finalizada' => true]])
                ]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Error al actualizar estatus: ' . $e->getMessage());
            return false;
        }
    }
}
