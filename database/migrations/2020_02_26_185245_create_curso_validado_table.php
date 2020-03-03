<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursoValidadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curso_validado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('clave_curso', 255)->unique();
            $table->integer('id_curso');
            $table->string('dias');
            $table->string('horario');
            $table->integer('id_instructor');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('id')
                  ->references('id')->on('instructores')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_curso')
                  ->references('id')->on('cursos')
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
        Schema::dropIfExists('curso_validado');
    }
}
