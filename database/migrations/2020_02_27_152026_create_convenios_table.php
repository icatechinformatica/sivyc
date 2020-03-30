<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConveniosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convenios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_convenio', 30)->unique();
            $table->string('institucion', 255);
            $table->char('tipo_sector', 5);
            $table->date('fecha_firma');
            $table->date('fecha_vigencia');
            $table->string('archivo_convenio', 255);
            $table->string('poblacion', 255);
            $table->string('municipio', 255);
            $table->string('nombre_titular', 255);
            $table->string('nombre_enlace', 255);
            $table->text('direccion');
            $table->bigInteger('telefono');
            $table->boolean('status')->nullable();
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
        Schema::dropIfExists('convenios');
    }
}
