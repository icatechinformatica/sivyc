<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAlumnosRegistroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            // modificaciones
            $table->boolean('chk_acta_nacimiento')->nullable();
            $table->boolean('chk_curp')->nullable();
            $table->boolean('chk_comprobante_domicilio')->nullable();
            $table->boolean('chk_fotografia')->nullable();
            $table->boolean('chk_ine')->nullable();
            $table->boolean('chk_pasaporte_licencia')->nullable();
            $table->boolean('chk_comprobante_ultimo_grado')->nullable();
            $table->string('acta_nacimiento', 255)->nullable();
            $table->string('curp', 255)->nullable();
            $table->string('comprobante_domicilio', 255)->nullable();
            $table->string('fotografia', 255)->nullable();
            $table->string('ine', 255)->nullable();
            $table->string('pasaporte_licencia_manejo', 255)->nullable();
            $table->string('comprobante_ultimo_grado', 255)->nullable();
            $table->boolean('chk_comprobante_calidad_migratoria')->nullable();
            $table->string('comprobante_calidad_migratoria', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            //
        });
    }
}
