<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPuestoEmpresaToAlumnosRegistroTable extends Migration
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
            $table->string('puesto_empresa', 255)->nullable();
            $table->string('sistema_capacitacion_especificar', 255)->nullable();
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
