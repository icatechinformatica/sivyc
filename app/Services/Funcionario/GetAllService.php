<?php

namespace App\Services\Funcionario;

use App\Interfaces\FuncionariosRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAllService {
    public function __construct(private FuncionariosRepositoryInterface $funcionariosRepository) {}

    public function execute(): LengthAwarePaginator {
        try {
            return $this->funcionariosRepository->all();
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener los funcionarios');
        }
    }
}