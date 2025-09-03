<?php
namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FuncionariosRepositoryInterface {
    public function all(): LengthAwarePaginator;
    public function getWithOutUser(): LengthAwarePaginator;
    public function getAllWithOutUser(): \Illuminate\Support\Collection; // Todos sin paginación
    public function createUser(array $data): array; // Define the return type for createUser method
}
