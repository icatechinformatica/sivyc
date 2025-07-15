<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosPoaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos_poa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('mes')->nullable();
            $table->string('unidad_capacitacion', 255)->nullable();
            $table->string('accion_movil', 50)->nullable();
            $table->string('plantel', 50)->nullable();
            $table->string('objetivo_horas', 50)->nullable();
            $table->string('objetivo_curso_abierto', 50)->nullable();
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
        Schema::dropIfExists('cursos_poa');
    }
}
