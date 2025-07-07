<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTblAfolios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_afolios', function (Blueprint $table) {
            $table->bigInteger('num_inicio')->nullable();
            $table->bigInteger('num_fin')->nullable();
            $table->bigInteger('id_unidad')->nullable();  
            $table->bigInteger('contador')->nullable();  
            $table->string('num_acta', 30)->nullable();
            $table->string('file_acta')->nullable();
            $table->unsignedBigInteger('iduser_created')->nullable();
            $table->unsignedBigInteger('iduser_updated')->nullable();
            $table->string('activo')->nullable();

            $table->foreign('iduser_created')->references('id')->on('users');
            $table->foreign('iduser_updated')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
