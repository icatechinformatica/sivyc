<?php

namespace App\Repositories;

use App\Models\Alumnopre;
use App\Interfaces\AlumnosInterface;

class AlumnosRepository implements AlumnosInterface
{
    protected $alumno;

    public function __construct(Alumnopre $alumno)
    {
        $this->alumno = $alumno;
    }

    public function obtenerTodos($registrosPorPagina = 15)
    {
        return $this->alumno
                        ->orderBy('id', 'desc')
                        ->paginate($registrosPorPagina);
    }

    public function buscarPaginado($busqueda = null, $registrosPorPagina = 15)
    {
        $query = $this->alumno->newQuery();
        
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('curp', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('matricula', 'ILIKE', "%{$busqueda}%")
                  ->orWhereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) ILIKE ?", ["%{$busqueda}%"])
                  ->orWhereRaw("CONCAT(apellido_paterno, ' ', apellido_materno, ' ', nombre) ILIKE ?", ["%{$busqueda}%"]);
            });
        }
        
        return $query->orderBy('apellido_paterno')
                    ->orderBy('apellido_materno')
                    ->orderBy('nombre')
                    ->paginate($registrosPorPagina);
    }

    public function buscarPorCURP($curp)
    {
        return $this->alumno->where('curp', $curp)->first();
    }

    public function crear(array $data)
    {
        return $this->alumno->create($data);
    }

    public function actualizar($id, array $data)
    {
        $alumno = $this->alumno->find($id);
        if ($alumno) {
            $alumno->update($data);
            return $alumno;
        }
        return null;
    }

    public function eliminar($id)
    {
        $alumno = $this->alumno->find($id);
        if ($alumno) {
            return $alumno->delete();
        }
        return false;
    }
}
