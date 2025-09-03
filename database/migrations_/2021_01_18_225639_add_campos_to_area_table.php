<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area', function (Blueprint $table) {
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
        Schema::table('area', function (Blueprint $table) {
            $table->dropColumn('iduser_created');
            $table->dropColumn('iduser_updated');
            $table->dropColumn('activo');
        });
    }
}
