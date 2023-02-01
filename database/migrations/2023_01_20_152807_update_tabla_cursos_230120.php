<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTablaCursos230120 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->jsonb('servicio')->nullable();
            $table->boolean('proyecto')->nullable();
            $table->string('file_carta_descriptiva')->nullable();
            $table->string('motivo')->nullable();
            $table->bigInteger('iduser_created')->nullable();
            $table->bigInteger('iduser_updated')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            //
        });
    }
    
}
