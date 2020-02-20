<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblInscripcion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_inscripcion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 50);
            $table->string('matricula', 30); // relacionar con tabla alumno
            $table->string('nombre', 80);
            $table->bigInteger('id_curso'); // relacionado con curso
            $table->string('curso', 200);
            $table->string('instructor', 80);
            $table->date('inicio');
            $table->date('termino');
            $table->string('hinicio', 15);
            $table->string('hfin', 15);
            $table->string('tinscripcion', 50);
            $table->string('abrinscri', 15);
            $table->string('hini2', 15);
            $table->string('hfin2', 15);
            $table->string('munidad', 50);
            $table->decimal('costo', 6, 2);
            $table->timestamps();

            $table->foreign('matricula')
                ->references('no_control')->on('tbl_alumnos')
                ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_curso')
                ->references('id')->on('tbl_cursos')
                ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_inscripcion');
    }
}
