<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAlumnoRegistroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            $table->string('medio_entero');
            $table->string('eleccion_capacitacion');
            $table->string('id_curso');
            $table->string('horario');
            $table->string('grupo');
            $table->dropColumn('sexo');
            $table->dropColumn('curp');
            $table->dropColumn('fecha_nacimiento');
            $table->dropColumn('domicilio');
            $table->dropColumn('colonia');
            $table->dropColumn('codigo_postal');
            $table->dropColumn('municipio');
            $table->dropColumn('estado');
            $table->dropColumn('estado_civil');
            $table->dropColumn('discapacidad_presente');

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
