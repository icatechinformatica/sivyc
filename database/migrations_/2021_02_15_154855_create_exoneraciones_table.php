<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExoneracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exoneraciones', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_unidad_capacitacion');
            $table->string('no_memorandum');
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_municipio');
            $table->string('localidad');
            $table->date('fecha_memorandum');
            $table->string('tipo_exoneracion');
            $table->integer('porcentaje');
            $table->text('razon_exoneracion');
            $table->string('grupo_beneficiado');
            $table->text('observaciones')->nullable();
            $table->string('no_convenio');
            $table->string('memo_soporte_dependencia')->nullable();
            $table->unsignedBigInteger('iduser_created')->nullable();
            $table->unsignedBigInteger('iduser_updated')->nullable();

            $table->foreign('id_unidad_capacitacion')->references('id')->on('tbl_unidades');
            $table->foreign('id_estado')->references('id')->on('estados');
            $table->foreign('id_municipio')->references('id')->on('tbl_municipios');
            $table->foreign('iduser_created')->references('id')->on('users');
            $table->foreign('iduser_updated')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exoneraciones');
    }
}
