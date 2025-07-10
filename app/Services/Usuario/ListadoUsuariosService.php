<?php

namespace App\Services\Usuario;

use App\Services\Funcionario\GetWithOutUserService as FuncionarioGetWithOutUserService;
use App\Services\Instructor\GetWithOutUserService as InstructorGetWithOutUserService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListadoUsuariosService
{
    public function __construct(
        private FuncionarioGetWithOutUserService $funcionarioService,
        private InstructorGetWithOutUserService $instructorService
    ) {}

    public function execute(int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        try {
            // Obtener TODOS los datos sin paginar
            $funcionarios = $this->funcionarioService->executeAll();
            $instructores = $this->instructorService->executeAll();

            // Transformar y combinar los datos
            $registrosCombinados = $this->combinarRegistros($funcionarios, $instructores);

            // Crear paginación del resultado combinado
            return $this->crearPaginacion($registrosCombinados, $page, $perPage);
            
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener los usuarios para listado');
        }
    }

    private function combinarRegistros($funcionarios, $instructores): \Illuminate\Support\Collection
    {
        // Transformar funcionarios
        $registrosFuncionarios = collect($funcionarios)->map(function ($item) {
            $item->tipo_registro = 'funcionario';
            return $item;
        });
        
        // Transformar instructores
        $registrosInstructores = collect($instructores)->map(function ($item) {
            $item->tipo_registro = 'instructor';
            return $item;
        });

        // Combinar ambas colecciones
        return $registrosFuncionarios->merge($registrosInstructores);
    }

    private function crearPaginacion(\Illuminate\Support\Collection $registros, int $page, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $registros->forPage($page, $perPage),
            $registros->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }
}
