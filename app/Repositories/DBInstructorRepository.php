<?php

namespace App\Repositories;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Interfaces\InstructoresRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class DBInstructorRepository implements InstructoresRepositoryInterface
{
    public function all(): LengthAwarePaginator
    {
        return DB::table('instructores_view')->select()->paginate(20);
    }

    public function getWithOutUser(): LengthAwarePaginator
    {
        // Obtener instructores sin usuario asociado o que tienen usuario pero NO como instructor
        return DB::table('instructores_view as iv')
            ->leftJoin('tblz_usuarios as u', 'u.registro_id', '=', 'iv.f_id')
            ->where(function($query) {
                $query->whereNull('u.id')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('u.id')
                                   ->where('u.registro_type', '!=', 'App\Models\instructor');
                      });
            })
            ->select('iv.*')
            ->paginate(50); // Aumentar a 50 registros por página
    }

    public function getAllWithOutUser(): \Illuminate\Support\Collection
    {
        // Obtener TODOS los instructores sin usuario asociado o que tienen usuario pero NO como instructor
        return DB::table('instructores_view as iv')
            ->leftJoin('tblz_usuarios as u', 'u.registro_id', '=', 'iv.f_id')
            ->where(function($query) {
                $query->whereNull('u.id')  // Sin usuario asociado
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('u.id')  // Tiene usuario pero...
                                   ->where('u.registro_type', '!=', 'App\Models\instructor'); // NO es instructor
                      });
            })
            ->select('iv.*')
            ->get(); // get() en lugar de paginate() para obtener todos
    }

    public function countWithOutUser(): int
    {
        // Contar instructores sin usuario asociado o que tienen usuario pero NO como instructor
        return DB::table('instructores_view as iv')
            ->leftJoin('tblz_usuarios as u', 'u.registro_id', '=', 'iv.f_id')
            ->where(function($query) {
                $query->whereNull('u.id')
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('u.id')
                                   ->where('u.registro_type', '!=', 'App\Models\instructor');
                      });
            })
            ->count();
    }

    public function createUser(array $data): array
    {
        // * Validación de integridad: verificar que el instructor existe en la vista
        // * Nota: instructor_view es una vista de base de datos, las consultas SELECT/WHERE/EXISTS funcionan normalmente
        $instructorExists = DB::table('instructores_view')->where('f_id', $data['id_instructor'])->exists();
        if (!$instructorExists) {
            throw new \InvalidArgumentException('El instructor especificado no existe');
        }


        // * Validación de duplicados: verificar si ya existe un usuario para este instructor
        $existingUser = User::where('registro_id', $data['id_instructor'])->where('registro_type', 'App\Models\instructor')->first();
        if ($existingUser) {
            throw new \RuntimeException('Ya existe un usuario para este instructor');
        }

        $defaultPassword = 'icatech_' . $data['id_instructor'];

        try {
            $user = User::create([
                'registro_id' => $data['id_instructor'],
                'registro_type' => 'App\Models\instructor',
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
