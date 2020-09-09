<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCriterioPagoZonaToEspecialidadInstructorCursoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('especialidad_instructor_curso', function (Blueprint $table) {
            //
            $table->integer('pago_id')->unsigned()->nullable();
            $table->integer('zona')->unsigned()->nullable();

            $table->foreign('pago_id')
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
        Schema::table('especialidad_instructor_curso', function (Blueprint $table) {
            //
        });
    }
}
