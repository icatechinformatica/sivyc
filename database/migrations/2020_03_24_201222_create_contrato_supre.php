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
        Schema::create('supre_directorio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supre_dest');
            $table->integer('supre_rem');
            $table->integer('supre_valida');
            $table->integer('supre_elabora');
            $table->integer('supre_ccp1');
            $table->integer('supre_ccp2');
            $table->integer('val_firmante')->nullable();
            $table->integer('val_ccp1')->nullable();
            $table->integer('val_ccp2')->nullable();
            $table->integer('val_ccp3')->nullable();
            $table->integer('val_ccp4')->nullable();
            $table->integer('id_supre')->nullable();
            $table->timestamps();

            $table->foreign('id_supre')->references('id')
                  ->on('tabla_supre')->onUpdate('cascade')->onDelete('set null');
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
