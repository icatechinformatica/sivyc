<?php
namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FuncionariosRepositoryInterface {
    public function all(): LengthAwarePaginator;
}
