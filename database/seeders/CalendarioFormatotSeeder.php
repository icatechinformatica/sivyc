<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarioFormatotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendario = [
            [
                'mes_informar' => 'ENERO',
                'fecha_entrega' => '28-01',
            ],
            [
                'mes_informar' => 'FEBRERO',
                'fecha_entrega' => '26-02',
            ],
            [
                'mes_informar' => 'MARZO',
                'fecha_entrega' => '31-03',
            ],
            [
                'mes_informar' => 'ABRIL',
                'fecha_entrega' => '28-04',
            ],
            [
                'mes_informar' => 'MAYO',
                'fecha_entrega' => '31-05',
            ],
            [
                'mes_informar' => 'JUNIO',
                'fecha_entrega' => '30-06',
            ],
            [
                'mes_informar' => 'JULIO',
                'fecha_entrega' => '29-07',
            ],
            [
                'mes_informar' => 'AGOSTO',
                'fecha_entrega' => '31-08',
            ],
            [
                'mes_informar' => 'SEPTIEMBRE',
                'fecha_entrega' => '29-09',
            ],
            [
                'mes_informar' => 'OCTUBRE',
                'fecha_entrega' => '29-10',
            ],
            [
                'mes_informar' => 'NOVIEMBRE',
                'fecha_entrega' => '30-11',
            ],
            [
                'mes_informar' => 'DICIEMBRE',
                'fecha_entrega' => '27-12',
            ]
        ];
        //insertaremos los registros
        DB::table('calendario_formatot')->insert($calendario);
        
    }
}
