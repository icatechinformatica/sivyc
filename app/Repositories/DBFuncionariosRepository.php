<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Interfaces\FuncionariosRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class DBFuncionariosRepository implements FuncionariosRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return DB::table('funcionarios_view')->select()->paginate(20);
    }

    public function getWithOutUser(): LengthAwarePaginator
    {
        // Obtener funcionarios sin usuario asociado o que tienen usuario pero NO como funcionario
        return DB::table('funcionarios_view as fv')
            ->leftJoin('tblz_usuarios as u', 'u.registro_id', '=', 'fv.f_id')
            ->where(function($query) {
                $query->whereNull('u.id')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('u.id')
                                   ->where('u.registro_type', '!=', 'App\Models\funcionario');
                      });
            })
            ->select('fv.*')
            ->paginate(50); // Aumentar a 50 registros por página
    }

    public function getAllWithOutUser(): \Illuminate\Support\Collection
    {
        // Obtener TODOS los funcionarios sin usuario asociado o que tienen usuario pero NO como funcionario
        return DB::table('funcionarios_view as fv')
            ->leftJoin('tblz_usuarios as u', 'u.registro_id', '=', 'fv.f_id')
            ->where(function($query) {
                $query->whereNull('u.id')  // Sin usuario asociado
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('u.id')  // Tiene usuario pero...
                                   ->where('u.registro_type', '!=', 'App\Models\funcionario'); // NO es funcionario
                      });
            })
            ->select('fv.*')
            ->get(); // get() en lugar de paginate() para obtener todos
    }

    public function createUser(array $data): array
    {
        // * Validación de integridad: verificar que el funcionario existe en la vista
        // * Nota: funcionarios_view es una vista de base de datos, las consultas SELECT/WHERE/EXISTS funcionan normalmente
        $funcionarioExists = DB::table('funcionarios_view')->where('f_id', $data['id_funcionario'])->exists();
        if (!$funcionarioExists) {
            throw new \InvalidArgumentException('El funcionario especificado no existe');
        }


        // * Validación de duplicados: verificar si ya existe un usuario para este funcionario
        $existingUser = User::where('registro_id', $data['id_funcionario'])->where('registro_type', 'App\Models\funcionario')->first();
        if ($existingUser) {
            throw new \RuntimeException('Ya existe un usuario para este funcionario');
        }

        $defaultPassword = 'icatech_' . $data['id_funcionario'];

        try {
            $user = User::create([
                'registro_id' => $data['id_funcionario'],
                'registro_type' => 'App\Models\funcionario',
                'password' => bcrypt($defaultPassword),
                'activo' => true,
            ]);

            return [
                'status' => 'success',
                'user' => $user,
                'default_password' => $defaultPassword
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException('No se pudo crear el usuario: ' . $e->getMessage());
        }
    }
}
