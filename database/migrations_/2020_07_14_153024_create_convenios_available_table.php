<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConveniosAvailableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenios_available', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('convenio_id');
            $table->boolean('CHK_TUXTLA');
            $table->boolean('CHK_TAPACHULA');
            $table->boolean('CHK_COMITAN');
            $table->boolean('CHK_REFORMA');
            $table->boolean('CHK_TONALA');
            $table->boolean('CHK_VILLAFLORES');
            $table->boolean('CHK_JIQUIPILAS');
            $table->boolean('CHK_CATAZAJA');
            $table->boolean('CHK_YAJALON');
            $table->boolean('CHK_SAN_CRISTOBAL');
            $table->boolean('CHK_CHIAPA_DE_CORZO');
            $table->boolean('CHK_MOTOZINTLA');
            $table->boolean('CHK_BERRIOZABAL');
            $table->boolean('CHK_PIJIJIAPAN');
            $table->boolean('CHK_JITOTOL');
            $table->boolean('CHK_LA_CONCORDIA');
            $table->boolean('CHK_VENUSTIANO_CARRANZA');
            $table->boolean('CHK_TILA');
            $table->boolean('CHK_TEOPISCA');
            $table->boolean('CHK_OCOSINGO');
            $table->boolean('CHK_CINTALAPA');
            $table->boolean('CHK_COPAINALA');
            $table->boolean('CHK_SOYALO');
            $table->boolean('CHK_ANGEL_ALBINO_CORZO');
            $table->boolean('CHK_ARRIAGA');
            $table->boolean('CHK_PICHUCALCO');
            $table->boolean('CHK_JUAREZ');
            $table->boolean('CHK_SIMOJOVEL');
            $table->boolean('CHK_MAPASTEPEC');
            $table->boolean('CHK_VILLA_CORZO');
            $table->boolean('CHK_CACAHOTAN');
            $table->boolean('CHK_ONCE_DE_ABRIL');
            $table->boolean('CHK_TUXTLA_CHICO');
            $table->boolean('CHK_OXCHUC');
            $table->boolean('CHK_CHAMULA');
            $table->boolean('CHK_OSTUACAN');
            $table->boolean('CHK_PALENQUE');
            $table->timestamps();

            $table->foreign('convenio_id')->references('id')->on('convenios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convenios_available');
    }
}
