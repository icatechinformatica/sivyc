<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidadoTablaSupreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curso_validado_tabla_supre', function (Blueprint $table) {
            $table->integer('idcurso_validado')->unsigned();
            $table->foreign('idcurso_validado')->references('id')->on('curso_validado');
            $table->integer('id_supre')->unsigned();
            $table->foreign('id_supre')->references('id_supre')->on('tabla_supre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('curso_validado_tabla_supre');
    }
}
