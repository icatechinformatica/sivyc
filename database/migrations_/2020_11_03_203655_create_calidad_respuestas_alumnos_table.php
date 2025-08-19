<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalidadRespuestasAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calidad_respuestas_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inscripcion')->references('id')->on('tbl_inscripcion');
            $table->string('matricula');
            $table->string('nombre');
            $table->foreignId('id_tbl_cursos')->references('id')->on('tbl_cursos');
            $table->foreignId('id_curso')->references('id')->on('cursos');
            $table->foreignId('id_encuesta')->references('id')->on('calidad_encuestas');
            $table->json('respuestas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calidad_respuestas_alumnos');
    }
}
