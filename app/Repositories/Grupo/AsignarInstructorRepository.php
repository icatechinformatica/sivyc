<?php

namespace App\Repositories\Grupo;

use Illuminate\Support\Facades\DB;
use App\Interfaces\Repositories\InstructorRepositoryInterface;

class AsignarInstructorRepository implements InstructorRepositoryInterface
{
    /**
     * Obtiene instructores internos que ya tienen cursos en el período
     */
    public function obtenerInstructoresInternos($fecha_inicio)
    {
        return DB::table('instructores as i')
            ->select('i.id')
            ->join('tbl_cursos as c', 'c.id_instructor', 'i.id')
            ->where('i.tipo_instructor', 'INTERNO')
            ->where('curso_extra', false)
            ->where(DB::raw("EXTRACT(YEAR FROM c.inicio)"), date('Y', strtotime($fecha_inicio)))
            ->where(DB::raw("EXTRACT(MONTH FROM c.inicio)"), date('m', strtotime($fecha_inicio)))
            ->havingRaw('count(*) >= 2')
            ->groupby('i.id');
    }

    /**
     * Obtiene instructores por especialidad
     */
    public function obtenerInstructoresPorEspecialidad($id_especialidad)
    {
        return DB::table(DB::raw('(select id_instructor, id_curso from agenda group by id_instructor, id_curso) as t'))
            ->select(
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'instructores.id',
                DB::raw('count(id_curso) as total')
            )
            ->rightJoin('instructores', 't.id_instructor', '=', 'instructores.id')
            ->leftJoin('instructor_perfil', 'instructor_perfil.numero_control', '=', 'instructores.id')
            ->leftJoin('tbl_unidades', 'tbl_unidades.cct', '=', 'instructores.clave_unidad')
            ->leftJoin('especialidad_instructores', 'especialidad_instructores.perfilprof_id', '=', 'instructor_perfil.id')
            ->where('especialidad_instructores.especialidad_id', $id_especialidad)
            ->groupBy('t.id_instructor', 'instructores.id')
            ->orderBy('instructor')
            ->limit(1)
            ->get();
    }

    /**
     * Obtiene información básica de un instructor
     */
    public function obtenerInformacionBasica($id_instructor)
    {
        return DB::table('instructores')
            ->select(
                'id',
                DB::raw('CONCAT("apellidoPaterno", ' . "' '" . ' ,"apellidoMaterno",' . "' '" . ',instructores.nombre) as instructor'),
                'tipo_honorario'
            )
            ->where('id', $id_instructor)
            ->first();
    }

    /**
     * Obtiene validación de especialidad del instructor
     */
    public function obtenerValidacionEspecialidad($id_especialidad, $id_instructor, $memo_especialidad)
    {
        return DB::table('especialidad_instructores')
            ->where('especialidad_id', $id_especialidad)
            ->where('id_instructor', $id_instructor)
            ->whereExists(function ($query) use ($memo_especialidad) {
                $query->select(DB::raw("elem->>'arch_val'"))
                    ->from(DB::raw("jsonb_array_elements(hvalidacion) AS elem"))
                    ->where(DB::raw("elem->>'memo_val'"), '=', $memo_especialidad);
            })
            ->value(DB::raw("(SELECT elem->>'arch_val' FROM jsonb_array_elements(hvalidacion) AS elem WHERE elem->>'memo_val' = '$memo_especialidad') as pdfvalida"));
    }

    /**
     * Buscar instructores por nombre o especialidad
     * Por ahora usando datos de prueba, fácil de migrar a BD real
     */
    public function buscarInstructores(string $busqueda, int $limite = 20)
    {
        $instructoresPrueba = $this->obtenerDatosPrueba();
        
        // Si no hay búsqueda, devolver todos
        if (empty(trim($busqueda))) {
            return collect($instructoresPrueba)->take($limite);
        }
        
        // Normalizar búsqueda (eliminar acentos para búsqueda flexible)
        $busquedaNormalizada = $this->normalizarTexto($busqueda);
        
        // Filtrar instructores
        $instructoresFiltrados = collect($instructoresPrueba)->filter(function ($instructor) use ($busquedaNormalizada) {
            $nombreNormalizado = $this->normalizarTexto($instructor['nombre']);
            $especialidadNormalizada = $this->normalizarTexto($instructor['especialidad']);
            
            return str_contains($nombreNormalizado, $busquedaNormalizada) || 
                   str_contains($especialidadNormalizada, $busquedaNormalizada);
        });
        
        return $instructoresFiltrados->take($limite);
    }

    /**
     * Datos de prueba de instructores
     * En mayúsculas según estándar del proyecto
     * 
     * TODO: Migrar a base de datos cuando esté listo
     */
    private function obtenerDatosPrueba(): array
    {
        return [
            [
                'id' => 1,
                'nombre' => 'MARÍA ELENA GONZÁLEZ RODRÍGUEZ',
                'especialidad' => 'DESARROLLO WEB Y PROGRAMACIÓN',
                'experiencia' => '8 años',
                'email' => 'maria.gonzalez@icatech.gob.mx',
                'telefono' => '664-123-4567',
                'activo' => true
            ],
            [
                'id' => 2,
                'nombre' => 'JOSÉ MANUEL TORRES LÓPEZ',
                'especialidad' => 'ADMINISTRACIÓN Y CONTABILIDAD',
                'experiencia' => '12 años',
                'email' => 'jose.torres@icatech.gob.mx',
                'telefono' => '664-234-5678',
                'activo' => true
            ],
            [
                'id' => 3,
                'nombre' => 'ANA PATRICIA MORALES HERNÁNDEZ',
                'especialidad' => 'DISEÑO GRÁFICO Y MULTIMEDIA',
                'experiencia' => '6 años',
                'email' => 'ana.morales@icatech.gob.mx',
                'telefono' => '664-345-6789',
                'activo' => true
            ],
            [
                'id' => 4,
                'nombre' => 'CARLOS ALBERTO RUIZ MENDOZA',
                'especialidad' => 'MECÁNICA AUTOMOTRIZ',
                'experiencia' => '15 años',
                'email' => 'carlos.ruiz@icatech.gob.mx',
                'telefono' => '664-456-7890',
                'activo' => true
            ],
            [
                'id' => 5,
                'nombre' => 'LAURA ISABEL VÁZQUEZ CASTRO',
                'especialidad' => 'ENFERMERÍA Y PRIMEROS AUXILIOS',
                'experiencia' => '10 años',
                'email' => 'laura.vazquez@icatech.gob.mx',
                'telefono' => '664-567-8901',
                'activo' => true
            ],
            [
                'id' => 6,
                'nombre' => 'ROBERTO DANIEL JIMÉNEZ FLORES',
                'especialidad' => 'SOLDADURA Y METALURGIA',
                'experiencia' => '20 años',
                'email' => 'roberto.jimenez@icatech.gob.mx',
                'telefono' => '664-678-9012',
                'activo' => true
            ],
            [
                'id' => 7,
                'nombre' => 'SANDRA LETICIA PÉREZ MORALES',
                'especialidad' => 'GASTRONOMÍA Y REPOSTERÍA',
                'experiencia' => '9 años',
                'email' => 'sandra.perez@icatech.gob.mx',
                'telefono' => '664-789-0123',
                'activo' => true
            ],
            [
                'id' => 8,
                'nombre' => 'MIGUEL ÁNGEL RIVERA SANTOS',
                'especialidad' => 'ELECTRICIDAD INDUSTRIAL',
                'experiencia' => '14 años',
                'email' => 'miguel.rivera@icatech.gob.mx',
                'telefono' => '664-890-1234',
                'activo' => true
            ],
            [
                'id' => 9,
                'nombre' => 'CARMEN LETICIA MORENO DÍAZ',
                'especialidad' => 'COSMETOLOGÍA Y ESTÉTICA',
                'experiencia' => '7 años',
                'email' => 'carmen.moreno@icatech.gob.mx',
                'telefono' => '664-901-2345',
                'activo' => true
            ],
            [
                'id' => 10,
                'nombre' => 'FERNANDO JOSÉ RAMÍREZ AGUILAR',
                'especialidad' => 'CARPINTERÍA Y EBANISTERÍA',
                'experiencia' => '18 años',
                'email' => 'fernando.ramirez@icatech.gob.mx',
                'telefono' => '664-012-3456',
                'activo' => true
            ]
        ];
    }

    /**
     * Normalizar texto eliminando acentos y caracteres especiales
     * Compatible con el estándar de mayúsculas del proyecto
     */
    private function normalizarTexto(string $texto): string
    {
        $texto = strtoupper($texto); // Convertir a mayúsculas
        
        // Reemplazar caracteres acentuados
        $acentos = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ñ' => 'N', 'Ü' => 'U'
        ];
        
        $texto = strtr($texto, $acentos);
        
        // Eliminar caracteres especiales excepto espacios y números
        $texto = preg_replace('/[^\w\s]/u', '', $texto);
        
        return trim($texto);
    }
}
