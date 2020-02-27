<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_memo', 50);
            $table->date('fecha');
            $table->string('nombre_ccp1', 150);
            $table->string('puesto_ccp1', 150);
            $table->string('nombre_ccp2', 150);
            $table->string('puesto_ccp2', 150);
            $table->string('nombre_ccp3', 150);
            $table->string('puesto_ccp3', 150);
            $table->string('elaboro', 150);
            $table->integer('id_status');
            $table->integer('id_contrato');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('id_status')
                  ->references('id')->on('status')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_contrato')
                  ->references('id_contrato')->on('contratos')
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
        Schema::dropIfExists('pagos');
    }
}
