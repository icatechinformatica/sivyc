<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            $table->string('curp');
            $table->string('sexo');
            $table->date('fecha_nacimiento');
            $table->string('domicilio');
            $table->string('colonia');
            $table->integer('cp');
            $table->string('municipio');
            $table->string('estado');
            $table->string('estado_civil');
            $table->string('discapacidad')->null();
            $table->dropColumn('curso');
            $table->dropColumn('horario');
            $table->dropColumn('especialidad_que_desea_inscribirse');
            $table->dropColumn('modo_entero_del_sistema');
            $table->dropColumn('motivos_eleccion_sistema_capacitacion');
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
