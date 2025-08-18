<?php

namespace App\Services\Curso;

use App\Interfaces\Repositories\CursoRepositoryInterface;
use App\Interfaces\Repositories\CatalogoRepositoryInterface;
use App\Repositories\CursoRepository;
use App\Repositories\CatalogoRepository;

class CursoService
{
    protected $cursoRepository;
    protected $catalogoRepository;

    public function __construct(?CursoRepositoryInterface $cursoRepository = null, ?CatalogoRepositoryInterface $catalogoRepository = null)
    {
        $this->cursoRepository = $cursoRepository ?: new CursoRepository();
        $this->catalogoRepository = $catalogoRepository ?: new CatalogoRepository();
    }

    /**
     * Obtiene la información del curso por ID
     */
    public function obtenerCursoPorId($id_curso, $status_curso = null)
    {
        $incluir_inactivos = ($status_curso === 'AUTORIZADO');
        return $this->cursoRepository->obtenerPorId($id_curso, $incluir_inactivos);
    }

    /**
     * Obtiene cursos filtrados por tipo, modalidad y unidad
     */
    public function obtenerCursosFiltrados($tipo, $modalidad, $unidad, $status_curso = null, $id_curso_autorizado = null)
    {
        $incluir_inactivos = ($status_curso === 'AUTORIZADO');
        $cursos = $this->cursoRepository->obtenerCursosFiltrados($tipo, $modalidad, $unidad, $incluir_inactivos);

        // Si el curso está autorizado, agregarlo a la lista
        if ($status_curso === 'AUTORIZADO' && $id_curso_autorizado) {
            $curso_autorizado = $this->obtenerCursoPorId($id_curso_autorizado, $status_curso);
            if ($curso_autorizado) {
                $cursos->put($id_curso_autorizado, $curso_autorizado->nombre_curso);
            }
        }

        return $cursos;
    }

    /**
     * Obtiene localidades por clave de municipio
     */
    public function obtenerLocalidadesPorMunicipio($id_municipio)
    {
        return $this->catalogoRepository->obtenerLocalidadesPorMunicipio($id_municipio);
    }

    /**
     * Obtiene la especialidad de un curso
     */
    public function obtenerEspecialidadCurso($id_curso)
    {
        return $this->cursoRepository->obtenerEspecialidadCurso($id_curso);
    }
}
