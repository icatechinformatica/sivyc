<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuxGrupoVulnerableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $grupos = [
            'LGBTTTI+',
            'PERSONA AFROAMERICANA',
            'PERSONA INDÍGENA',
            'PERSONA ADULTA MAYOR',
            'TRABAJADORA SEXUAL',
            'DISCAPACIDAD PARA OIR',
            'DISCAPACIDAD INTELECTUAL',
            'MADRE JEFA DE FAMILIA',
            'DISCAPACIDAD PARA HABLAR',
            'MUJERES EMBARAZADAS',
            'PERSONA MIGRANTE',
            'PERSONA PRIVADA DE LA LIBERTAD',
            'DISCAPACIDAD PARA VER',
            'DISCAPACIDAD MOTRIZ',
        ];

        foreach ($grupos as $grupo) {
            DB::table('tbl_aux_grupo_vulnerable')->updateOrInsert([
                'grupo_vulnerable' => $grupo
            ]);
        }
    }
}
