<?php

namespace App\Services\Instructor;

use App\Interfaces\InstructoresRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetWithOutUserService {
    public function __construct(private InstructoresRepositoryInterface $instructoresRepository) {}

    public function execute(): LengthAwarePaginator {
        try {
            return $this->instructoresRepository->getWithOutUser();
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener los instructores');
        }
    }

    public function executeAll(): \Illuminate\Support\Collection {
        try {
            return $this->instructoresRepository->getAllWithOutUser();
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudieron obtener todos los instructores');
        }
    }
}