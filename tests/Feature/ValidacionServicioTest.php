<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Services\ValidacionServicio;

class ValidacionServicioTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_inst_disponible_fecha_hora_filtra_instructores_correctamente()
    {
       // Suponiendo que tienes un modelo para instructores
       $instructor = DB::table('instructores')->insertGetId([ 'nombre' => 'Instructor Prueba', ]);

        // Simula agenda para esos 5 días
        $agenda = collect();
        for ($i = 0; $i < 5; $i++) {
            $day = Carbon::parse("2025-03-10")->addDays($i);
            $start = $day->copy()->setTime(9, 0, 0);
            $end = $day->copy()->setTime(17, 0, 0);
            $agenda->push((object)[
                'id_instructor' => $instructor,
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString()
            ]);
        }


        // Inyecta instructores en el servicio
        $instructorObj = (object)['id' => $instructor];
        $servicio = new \App\Services\ValidacionServicio([$instructorObj]);

        $resultado = $servicio->InstNoRebase40HorasSem($agenda);

        $this->assertCount(1, $resultado);
        $this->assertEquals($instructor, $resultado[0]->id);
    }
}
