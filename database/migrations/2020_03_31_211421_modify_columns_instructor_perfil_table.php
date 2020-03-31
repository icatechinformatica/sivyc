<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnsInstructorPerfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructor_perfil', function (Blueprint $table) {
            // agregar campos
            $table->string('tipo_honorario', 255)->nullable();
            $table->string('registro_agente_capacitador_externo', 255)->nullable();
            $table->string('unidad_capacitacion_solicita_validacion', 255)->nullable();
            $table->string('memorandum_validacion', 255)->nullable();
            $table->date('fecha_validacion')->nullable();
            $table->string('modificacion_memo', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructor_perfil', function (Blueprint $table) {
            //
        });
    }
}
