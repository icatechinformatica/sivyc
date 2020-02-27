<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCalificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_calificaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 50);
            $table->string('matricula', 30); // relacionado con la tabla alumno
            $table->string('alumno', 100);
            $table->string('acreditado', 10);
            $table->string('noacreditado', 10);
            $table->bigInteger('idcurso'); // relacionado a tabla cursos
            $table->bigInteger('idgrupo');
            $table->string('area', 50);
            $table->string('espe', 100);
            $table->string('curso', 100);
            $table->string('mod', 10);
            $table->string('instructor', 70); // relacionado con instructor
            $table->date('inicio');
            $table->date('termino');
            $table->string('hini', 30);
            $table->string('hfin', 30);
            $table->integer('dura');
            $table->string('ciclo', 30);
            $table->integer('periodo');
            $table->string('calificacion', 15);
            $table->string('hini2', 30)->nullable(); // nulos
            $table->string('hfin2', 30)->nullable(); // nulos
            $table->timestamps();

            $table->foreign('matricula')
                ->references('no_control')->on('tbl_alumnos')
                ->onDelete('set null')->onUpdate('cascade');

            /*$table->foreign('idcurso')
                ->references('id')->on('tbl_cursos')
                ->onDelete('set null')->onUpdate('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_calificaciones');
    }
}
