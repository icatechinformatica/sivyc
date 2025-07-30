<?php

namespace App\Interfaces;

interface AlumnosTempInterface
{
    public function guardarEnSeccion($seccion, $datos);
    public function actualizarSeccion($seccion, $datos, $id = null);
}