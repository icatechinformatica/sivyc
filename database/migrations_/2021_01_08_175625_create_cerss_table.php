<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCerssTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cerss', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('numero');
            $table->string('direccion')->nullable();
            $table->integer('id_municipio');
            $table->string('titular')->nullable();
            $table->string('telefono')->nullable();
            $table->string('telefono2')->nullable();
            $table->integer('id_unidad');
            $table->boolean('activo');
            $table->integer('iduser_create')->nullable();
            $table->integer('iduser_update')->nullable();
            $table->timestamps();

            $table->foreign('id_municipio')->references('id')->on('tbl_municipios');
            $table->foreign('id_unidad')->references('id')->on('tbl_unidades');
            $table->foreign('iduser_create')->references('id')->on('users');
            $table->foreign('iduser_update')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cerss');
    }
}
