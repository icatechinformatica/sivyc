<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CrstatusEspecialidadInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('especialidad_instructores', function (Blueprint $table) {
            $table->string('memorandum_solicitud')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->string('status')->nullable();
            $table->string('memorandum_solicitud')->nullable();
            $table->integer('solicito')->nullable();
            $table->string('observacion_validacion')->nullable();
            $table->date('fecha_baja')->nullable();
            $table->string('memorandum_baja')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('especialidad_instructores', function (Blueprint $table) {
            //
        });
    }
}
