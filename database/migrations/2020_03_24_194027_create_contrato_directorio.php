<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratoDirectorio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contrato_directorio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('contrato_iddirector');
            $table->integer('contrato_idtestigo1');
            $table->integer('contrato_idtestigo2');
            $table->integer('contrato_idtestigo3');
            $table->integer('id_contrato');
            $table->timestamps();

            $table->foreign('id_contrato')->references('id_contrato')
                  ->on('contratos')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contrato_directorio');
    }
}
