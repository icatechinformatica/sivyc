<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalidadRespuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calidad_respuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_encuesta');
            $table->integer('id_tbl_cursos');
            $table->integer('id_curso');
            $table->integer('id_instructor');
            $table->string('unidad');
            $table->date('fecha_aplicacion');
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
        Schema::dropIfExists('calidad_respuestas');
    }
}
