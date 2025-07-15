<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblChecadorAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_checador_asistencias', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_enlace');
            $table->date('fecha');
            $table->time('entrada')->nullable();
            $table->time('salida')->nullable();
            $table->boolean('retardo')->default(false);
            $table->boolean('inasistencia')->default(false);
            $table->boolean('justificante')->default(false);
            $table->string('observaciones')->nullable();
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
        Schema::dropIfExists('tbl_checador_asistencias');
    }
}
