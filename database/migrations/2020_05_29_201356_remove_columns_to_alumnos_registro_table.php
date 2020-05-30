<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsToAlumnosRegistroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            //
            $table->dropColumn('ultimo_grado_estudios');
            $table->dropColumn('empresa_trabaja');
            $table->dropColumn('antiguedad');
            $table->dropColumn('direccion_empresa');
            $table->dropColumn('medio_entero');
            $table->dropColumn('eleccion_capacitacion');
            $table->dropColumn('chk_acta_nacimiento');
            $table->dropColumn('chk_curp');
            $table->dropColumn('chk_comprobante_domicilio');
            $table->dropColumn('chk_fotografia');
            $table->dropColumn('chk_ine');
            $table->dropColumn('chk_pasaporte_licencia');
            $table->dropColumn('chk_comprobante_ultimo_grado');
            $table->dropColumn('acta_nacimiento');
            $table->dropColumn('curp');
            $table->dropColumn('comprobante_domicilio');
            $table->dropColumn('fotografia');
            $table->dropColumn('ine');
            $table->dropColumn('pasaporte_licencia_manejo');
            $table->dropColumn('comprobante_ultimo_grado');
            $table->dropColumn('chk_comprobante_calidad_migratoria');
            $table->dropColumn('comprobante_calidad_migratoria');
            $table->dropColumn('puesto_empresa');
            $table->dropColumn('sistema_capacitacion_especificar');
            $table->string('tipo_curso', 50)->nullable();
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
