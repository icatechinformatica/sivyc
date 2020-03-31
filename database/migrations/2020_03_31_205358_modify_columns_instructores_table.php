<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructores', function (Blueprint $table) {
            // eliminar campos
            $table->dropColumn('tipo_honorario');
            $table->dropColumn('registro_agente_capacitador_externo');
            $table->dropColumn('memoramdum_validacion');
            $table->dropColumn('fecha_validacion');
            $table->dropColumn('modificacion_memo');
            $table->dropColumn('unidad_capacitacion_solicita_validacion_instructor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructores', function (Blueprint $table) {
            //
        });
    }
}
