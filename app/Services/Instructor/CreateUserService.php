<?php

namespace App\Services\Instructor;

use App\Interfaces\InstructoresRepositoryInterface;

class CreateUserService
{
    public function __construct(private InstructoresRepositoryInterface $instructoresRepository) {}

    public function execute(array $data): array
    {
        try {
            return $this->instructoresRepository->createUser(['id_instructor' => $data['id_instructor']]);
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudo crear el usuario instructor: ' . $e->getMessage(), 0, $e);
        }
    }
}
