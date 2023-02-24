<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalidadEncuestasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calidad_encuestas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('idparent');
            $table->string('nombre');
            $table->json('respuestas')->nullable();
            $table->boolean('activo');
            $table->string('dirigido_a')->nullable();
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
        Schema::dropIfExists('calidad_encuestas');
    }
}
