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
            $table->string('organismo', 255);
            $table->char('tipo_sector', 5);
            $table->string('lugar_expedicion', 150);
            $table->date('fecha_firma');
            $table->date('fecha_inicio');
            $table->date('fecha_conclusion');
            $table->string('rfc', 20);
            $table->string('archivo_convenio', 255);
            $table->text('comentario');
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
