<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insertUsing([
            'id',
            'name',
            'guard_name',
            'slug',
            'description',
            'created_at',
            'updated_at',
            'special',
        ], DB::table('roles_')->select(
            'id',
            'slug as name',
            DB::raw("'web' as guard_name"),
            'slug',
            'description',
            'created_at',
            'updated_at',
            DB::raw("CASE
                WHEN slug = 'all-access' THEN 'all-access'
                ELSE NULL
            END as special"),
        ));

        $count = DB::table('roles')->count();
        $this->command->info("Migración completada: {$count} registros transferidos");
        $this->command->info("Todos los roles tienen guard_name = 'web'");

    }
}
