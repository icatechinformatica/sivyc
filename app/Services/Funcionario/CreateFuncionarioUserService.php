<?php

namespace App\Services\Funcionario;

use App\Interfaces\FuncionariosRepositoryInterface;

class CreateFuncionarioUserService
{
    public function __construct(private FuncionariosRepositoryInterface $funcionariosRepository) {}

    public function execute(array $data): array
    {
        try {
            return $this->funcionariosRepository->createUser(['id_funcionario' => $data['id_funcionario']]);
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudo crear el usuario funcionario: ' . $e->getMessage(), 0, $e);
        }
    }
}
