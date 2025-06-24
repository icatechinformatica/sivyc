<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaAdscripcion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_adscripcion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('area', 150)->nullable();
            $table->integer('organo_id')->unsigned();

            $table->foreign('organo_id')->references('id')->on('organo_administrativo')
            ->onDelete('set null');

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
        Schema::dropIfExists('area_adscripcion');
    }
}
