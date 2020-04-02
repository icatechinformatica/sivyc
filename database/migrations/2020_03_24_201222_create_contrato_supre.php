<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratoSupre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contrato_supre', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supre_dest');
            $table->integer('supre_rem');
            $table->integer('supre_valida');
            $table->integer('supre_elabora');
            $table->integer('supre_ccp1');
            $table->integer('supre_ccp2');
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
        Schema::dropIfExists('contrato_supre');
    }
}
