<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles_sistemas = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'special' => 'all-access'
            ],
            [
                'name' => 'Delegado Administrativo',
                'slug' => 'administrativo',
                'special' => 'all-access'
            ],
            [
                'name' => 'Departamento Académico',
                'slug' => 'depto_academico',
                'special' => 'all-access'
            ],
            [
                'name' => 'Planeación',
                'slug' => 'planeacion',
                'special' => 'all-access'
            ],
            [
                'name' => 'Unidad de Vinculación',
                'slug' => 'unidad_vinculacion',
                'special' => 'all-access'
            ],
            [
                'name' => 'Dirección de Vinculación',
                'slug' => 'direccion_vinculacion',
                'special' => 'all-access'
            ],
            [
                'name' => 'Departamento de Financiero',
                'slug' => 'depto_financiero',
                'special' => 'all-access'
            ],
            [
                'name' => 'Director de Unidad',
                'slug' => 'director_unidad',
                'special' => 'all-access'
            ]
        ];

        DB::table('roles')->insert($roles_sistemas);
    }
}
