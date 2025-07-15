<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspecialidadInstructorCursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('especialidad_instructor_curso', function (Blueprint $table) {
            $table->integer('id_especialidad_instructor')->unsigned();
            $table->integer('curso_id')->unsigned();


            $table->foreign('id_especialidad_instructor')
                ->references('id')->on('especialidad_instructores')
                ->onUpdate('cascade')->onDelete('set null');

            $table->foreign('curso_id')
                ->references('id')->on('cursos')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('especialidad_instructor_curso');
    }
}
