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
            $table->string('cantidad_letras1', 250);
            $table->string('cantidad_letras2', 250);
            $table->string('numero_circular', 250);
            $table->string('nombre_director', 250);
            $table->string('unidad_capacitacion', 250);
            $table->string('municipio', 250)->nullable();
            $table->string('testigo1', 250);
            $table->string('puesto_testigo1', 250);
            $table->string('testigo2', 250);
            $table->string('puesto_testigo2', 250);
            $table->date('fecha_firma');
            $table->integer('id_folios');
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
