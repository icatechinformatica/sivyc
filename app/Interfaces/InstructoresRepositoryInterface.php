<?php
namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstructoresRepositoryInterface {
    public function all(): LengthAwarePaginator; // Obtiene todos los instructores con paginación
    public function getWithOutUser(): LengthAwarePaginator; // Obtiene instructores sin usuario asociado
    public function getAllWithOutUser(): \Illuminate\Support\Collection; // Todos sin paginación
    public function createUser(array $data): array; // Crea un usuario a partir de
}
