<?php

namespace App\Services\Alumno;

use App\Interfaces\AlumnosTempInterface;

class RegistroTempService
{
    protected $alumnosTempInterface;

    public function __construct(AlumnosTempInterface $alumnosTempInterface)
    {
        $this->alumnosTempInterface = $alumnosTempInterface;
    }

    public function guardarEnSeccion($seccion, $datos)
    {
        try {
            if (isset($datos['finalizado']) && $datos['finalizado'] === true) {
                $datosSeccion = isset($datos['datos']) ? $datos['datos'] : $datos;
                $resultado = $this->alumnosTempInterface->guardarEnSeccion($seccion, $datosSeccion);
                return $resultado === true ? true : $resultado;
            }
            return 'No finalizado';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarSeccion($seccion, $datos, $id = null)
    {
        try {
            return $this->alumnosTempInterface->actualizarSeccion($seccion, $datos, $id);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Error al actualizar los datos en la sección.'], 500);
        }
    }

}
