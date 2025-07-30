<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\AlumnosTempInterface;

class AlumnosTempRepository implements AlumnosTempInterface
{
    protected $table = 'temp_reg_alumno';
    public function guardarEnSeccion($seccion, $datos, $curp = null)
    {
        try {
            $id_funcionario_captura = Auth::id() ?? 1;
            $curp = isset($datos['curp']) ? $datos['curp'] : null;
            if (!empty($curp)) {
                $updated = DB::table($this->table)->updateOrInsert(
                    ['curp' => $curp],
                    [
                        $seccion => json_encode($datos),
                        'id_funcionario_captura' => $id_funcionario_captura
                    ]
                );
            } else {
                $insertData = [
                    $seccion => json_encode($datos),
                    'id_funcionario_captura' => $id_funcionario_captura
                ];
                if ($curp !== null) {
                    $insertData['curp'] = $curp;
                }
                $updated = DB::table($this->table)->insert($insertData);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarSeccion($seccion, $datos, $curp = null)
    {
        try {
            DB::table($this->table)->where('curp', $curp)->update([$seccion => json_encode($datos)]);
            return response()->json(['success' => true, 'message' => 'Datos actualizados correctamente.']);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
