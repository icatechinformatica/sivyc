<?php

use Illuminate\Database\Seeder;
use App\Models\Instituto;

class InstitutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Instituto::WHERE('id', 1)->update([
            'cuentas_bancarias' => [
                "pago_instructor" => [
                    'BBVA' => '119702938'
                ],
                'REFORMA' => [
                    'BBVA' => '0122103486'
                ],
                'JIQUIPILAS' => [
                    'BBVA' => '0122103508'
                ],
                'VILLAFLORES' => [
                    'BBVA' => '0122103656'
                ],
                'CATAZAJA' => [
                    'BBVA' => '0122103729'
                ],
                'YAJALON' => [
                    'BBVA' => '0122103796'
                ],
                'SAN CRISTOBAL' => [
                    'BBVA' => '0122103907'
                ],
                'TUXTLA' => [
                    'BBVA' => '0122103974'
                ],
                "TAPACHULA" => [
                    'BBVA' => '0122104008'
                ],
                "COMITAN" => [
                    'BBVA' => '0122104040'
                ],
                "TONALA" => [
                    'BBVA' => '0122104113'
                ],
                "OCOSINGO" => [
                    'BBVA' => '0122104156'
                ]
            ]
        ]);
    }
}
