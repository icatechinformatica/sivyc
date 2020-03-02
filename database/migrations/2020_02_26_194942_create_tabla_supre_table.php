<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablaSupreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabla_supre', function (Blueprint $table) {
            $table->bigIncrements('id_supre');
            $table->string('unidad_capacitacion', 80);
            $table->string('no_memo', 80);
            $table->date('fecha');
            $table->string('nombre_para', 120);
            $table->string('puesto_para', 120);
            $table->string('nombre_remitente', 120);
            $table->string('puesto_remitente', 120);
            $table->string('nombre_ccp1', 120);
            $table->string('puesto_ccp1', 120);
            $table->string('nombre_ccp2', 120);
            $table->string('puesto_ccp2', 120);
            $table->string('nombre_valida', 120);
            $table->string('puesto_valida', 120);
            $table->string('nombre_elabora', 120);
            $table->string('puesto_elabora', 120);
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
        Schema::dropIfExists('tabla_supre');
    }
}
