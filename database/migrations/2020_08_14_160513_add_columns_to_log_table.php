<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            //
            $table->ipAddress('direccion_ip')->nullable();
            $table->macAddress('direccion_mac')->nullable();
            $table->text('accion')->nullable();
            $table->string('usuario', 150)->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('modulo', 80)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            //
        });
    }
}
