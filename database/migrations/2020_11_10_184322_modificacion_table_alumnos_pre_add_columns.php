<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificacionTableAlumnosPreAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            // es cereso
            $table->string('nombre_cerss', 250)->nullable();
            $table->string('direccion_cerss', 255)->nullable();
            $table->string('titular_cerss', 150)->nullable();
            $table->string('numero_expediente', 250)->nullable();
            $table->string('rfc_cerss', 50)->nullable();
            $table->string('nacionalidad', 100)->nullable();
            $table->string('ficha_cerss', 150)->nullable();
            $table->boolean('chk_ficha_cerss')->default(false);
            $table->boolean('es_cereso')->default(false);
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
