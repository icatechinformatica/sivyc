<?php

namespace App\Services\Funcionario;

use App\Interfaces\FuncionariosRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetWithOutUserService {
    public function __construct(private FuncionariosRepositoryInterface $funcionariosRepository) {}

    public function execute(): LengthAwarePaginator {
        try {
            return $this->funcionariosRepository->getWithOutUser();
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener los funcionarios');
        }
    }

    public function executeAll(): \Illuminate\Support\Collection {
        try {
            return $this->funcionariosRepository->getAllWithOutUser();
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener todos los funcionarios');
        }
    }
}