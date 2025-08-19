<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEspecialidadInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('especialidad_instructores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('especialidad_id');
            $table->integer('perfilprof_id');
            $table->integer('pago_id');
            $table->integer('zona');
            $table->string('validado_impartir');
            $table->string('unidad_solicita');
            $table->string('memorandum_validacion');
            $table->date('fecha_validacion');
            $table->string('memorandum_modificacion')->nullable();
            $table->string('observacion')->nullable();
            $table->timestamps();

            $table->foreign('especialidad_id')
                  ->references('id')->on('especialidades')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('perfilprof_id')
                  ->references('id')->on('instructor_perfil')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('pago_id')
                  ->references('id')->on('criterio_pago')
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
        Schema::dropIfExists('especialidad_instructores');
    }
}
