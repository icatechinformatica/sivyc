<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContratoDirectorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contrato_directorio', function (Blueprint $table) {
            $table->integer('solpa_elaboro')->nullable();
            $table->integer('solpa_para')->nullable();
            $table->integer('solpa_ccp1')->nullable();
            $table->integer('solpa_ccp2')->nullable();
            $table->integer('solpa_ccp3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contrato_directorio', function (Blueprint $table) {
            //
        });
    }
}
