<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsAndRemoveToAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            //
            $table->string('ultimo_grado_estudios', 150)->nullable();
            $table->string('empresa_trabaja', 150)->nullable();
            $table->string('antiguedad', 150)->nullable();
            $table->string('direccion_empresa', 255)->nullable();
            $table->string('medio_entero', 255)->nullable();
            $table->string('eleccion_capacitacion', 80)->nullable();
            $table->boolean('chk_acta_nacimiento')->nullable();
            $table->boolean('chk_curp', 80)->nullable();
            $table->boolean('chk_comprobante_domicilio')->nullable();
            $table->boolean('chk_fotografia')->nullable();
            $table->boolean('chk_ine')->nullable();
            $table->boolean('chk_pasaporte_licencia')->nullable();
            $table->boolean('chk_comprobante_ultimo_grado')->nullable();
            $table->boolean('chk_comprobante_calidad_migratoria')->nullable();
            $table->string('acta_nacimiento', 255)->nullable();
            $table->string('documento_curp', 255)->nullable();
            $table->string('comprobante_domicilio', 255)->nullable();
            $table->string('fotografia', 255)->nullable();
            $table->string('ine', 255)->nullable();
            $table->string('pasaporte_licencia_manejo', 255)->nullable();
            $table->string('comprobante_ultimo_grado', 255)->nullable();
            $table->string('comprobante_calidad_migratoria', 255)->nullable();
            $table->string('puesto_empresa', 255)->nullable();
            $table->string('sistema_capacitacion_especificar', 255)->nullable();
            $table->dropColumn('interes_en_curso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            //
        });
    }
}
