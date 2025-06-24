<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAlumnosRegistroAddStatusToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            // agregar una nueva columna llamada estatus_modificacion
            $table->boolean('estatus_modificacion')->default(false);
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
