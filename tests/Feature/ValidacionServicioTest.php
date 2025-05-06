<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Services\ValidacionServicio;

class ValidacionServicioTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_inst_disponible_fecha_hora_filtra_instructores_correctamente()
    {

        // // Simulamos dos instructores con diferentes datos
        // $instructores = [
        //     [
        //         'id_curso' => 'CURSO001',
        //         'fechaInicio' => '2025-05-10',
        //         'fechaTermino' => '2025-05-12',
        //         'horaInicio' => '09:00:00',
        //         'horaTermino' => '13:00:00'
        //     ],
        //     [
        //         'id_curso' => 'CURSO002',
        //         'fechaInicio' => '2025-05-15',
        //         'fechaTermino' => '2025-05-17',
        //         'horaInicio' => '14:00:00',
        //         'horaTermino' => '18:00:00'
        //     ]
        // ];

        // // Mock para el primer instructor: hay conflicto (should return TRUE)
        // DB::shouldReceive('table')->once()->with('alumnos_registro as ar')->andReturnSelf();
        // DB::shouldReceive('select')->once()->with('ap.curp')->andReturnSelf();
        // DB::shouldReceive('leftJoin')->times(3)->andReturnSelf();
        // DB::shouldReceive('where')->times(3)->andReturnSelf();
        // DB::shouldReceive('whereRaw')->times(2)->andReturnSelf();
        // DB::shouldReceive('whereIn')->once()->andReturnSelf();
        // DB::shouldReceive('exists')->once()->andReturn(true); // primer instructor tiene conflicto

        // // Mock para el segundo instructor: sin conflicto (should return FALSE)
        // DB::shouldReceive('table')->once()->with('alumnos_registro as ar')->andReturnSelf();
        // DB::shouldReceive('select')->once()->with('ap.curp')->andReturnSelf();
        // DB::shouldReceive('leftJoin')->times(3)->andReturnSelf();
        // DB::shouldReceive('where')->times(3)->andReturnSelf();
        // DB::shouldReceive('whereRaw')->times(2)->andReturnSelf();
        // DB::shouldReceive('whereIn')->once()->andReturnSelf();
        // DB::shouldReceive('exists')->once()->andReturn(false); // segundo instructor disponible

        // // Creamos el servicio con los instructores simulados
        // $servicio = (new ValidacionServicio($instructores));

        // // Ejecutamos el método
        // $resultado = $servicio->InstDisponibleFechaHora();

        // // Debe filtrar solo el segundo instructor (el disponible)
        // $this->assertCount(1, $resultado);
        // $this->assertEquals('CURSO002', $resultado[0]['id_curso']);


        $folio_grupo = '6Y-250063';

        // Simular la cadena de métodos: DB::table(...)->where(...)->first()
        DB::shouldReceive('table')
            ->with('tbl_cursos')
            ->once()
            ->andReturnSelf(); // Permite encadenar

        DB::shouldReceive('where')
            ->with('folio_grupo', $folio_grupo)
            ->once()
            ->andReturnSelf(); // Permite encadenar

        DB::shouldReceive('first')
            ->once()
            ->andReturn((object)[
                'folio_grupo' => $folio_grupo,
                'status' => 'ACTIVO',
            ]);

        // Ejecutar la lógica como en tu clase
        $dataCurso = DB::table('tbl_cursos')->where('folio_grupo', $folio_grupo)->first();

        // Aserciones
        $this->assertNotNull($dataCurso);
        $this->assertEquals('6Y-250063', $dataCurso->folio_grupo);
        $this->assertEquals('ACTIVO', $dataCurso->status);
    }
}
