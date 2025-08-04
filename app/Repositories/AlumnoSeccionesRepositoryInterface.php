<?php

namespace App\Repositories;

interface AlumnoSeccionesRepositoryInterface
{
    /**
     * Crea o actualiza un alumno por CURP.
     *
     * @param array $datos
     * @return mixed
     */
    public function actualizarOrCrearPorCURP(array $datos);
    public function obtenerArchivosDocumentos($curp);
    public function obtenerCERSSPorCURP($curp);
}
