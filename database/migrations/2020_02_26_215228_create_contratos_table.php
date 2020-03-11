<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->bigIncrements('id_contrato');
            $table->string('numero_contrato', 30)->unique();
            $table->string('folio_ine', 80);
            $table->string('cantidad_letras', 250);
            $table->string('lugar_expedicion', 250);
            $table->date('fecha_firma');
            $table->string('testigo_icatech', 250);
            $table->string('testigo_instructor', 250);
            $table->string('municipio', 250)->nullable();
            $table->integer('id_folios');
            $table->string('status');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('id_folios')
                  ->references('id_folios')->on('folios')
                  ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratos');
    }
}
