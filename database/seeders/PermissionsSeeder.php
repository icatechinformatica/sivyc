<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Opción 1: Si quieres limpiar la tabla destino primero
        DB::table('permissions')->truncate();

        DB::table('permissions')->insertUsing([
            'id',
            'name',
            'guard_name',
            'slug',
            'description',
            'created_at',
            'updated_at',
        ], DB::table('permissions_')->select(
            'id',
            'slug as name',
            DB::raw("'web' as guard_name"),
            'slug',
            'description',
            'created_at',
            'updated_at'
        ));

        $count = DB::table('permissions')->count();
        $this->command->info("Migración completada: {$count} registros transferidos");
        $this->command->info("Todos los permisos tienen guard_name = 'web'");

    }
}
