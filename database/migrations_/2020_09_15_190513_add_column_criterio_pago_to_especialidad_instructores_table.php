<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCriterioPagoToEspecialidadInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('especialidad_instructores', function (Blueprint $table) {
            //
            $table->integer('criterio_pago_id')->unsigned()->nullable();

            $table->foreign('criterio_pago_id')
                ->references('id')->on('criterio_pago')
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
        Schema::table('especialidad_instructores', function (Blueprint $table) {
            //
        });
    }
}
