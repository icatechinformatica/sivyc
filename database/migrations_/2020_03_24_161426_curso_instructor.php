<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CursoInstructor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curso_instructor', function (Blueprint $table) {
            $table->bigInteger('id_instructores');
            $table->bigInteger('id_cursos');
            $table->timestamps();

            /**
             * generamos las relaciones
             */
            $table->foreign('id_instructores')->references('id')
                  ->on('instructores')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('id_cursos')->references('id')
                  ->on('cursos')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curso_instructor');
    }
}
