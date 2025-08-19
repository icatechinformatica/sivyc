<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiasInhabilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dias_inhabiles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha');
            $table->bigInteger('iduser_created')->nullable();
            $table->bigInteger('iduser_updated')->nullable();
            $table->timestamps();
        });
    }
    //php artisan migrate --path=database/migrations/2022_08_25_101136_create_dias_inhabiles_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dias_inhabiles');
    }
}
