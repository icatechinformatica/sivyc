<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Interfaces\FuncionariosRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class DBFuncionariosRepository implements FuncionariosRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return DB::table('funcionarios_view')->select()->paginate(20);
    }
}