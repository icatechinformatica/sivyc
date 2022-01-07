<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganismosPublicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organismos_publicos', function (Blueprint $table) {
            $table->id(); 
            $table->string('organismo');
            $table->string('sector')->nullable();
            $table->string('poder_pertenece')->nullable();
            $table->string('tipo')->nullable();
            $table->string('nombre_titular')->nullable();
            $table->unsignedBigInteger('id_estado');
            $table->unsignedBigInteger('id_municipio');
            $table->unsignedBigInteger('clave_localidad')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('activo');

            $table->timestamps();
        });
    }
    //php artisan migrate --path=database/migrations/2021_11_30_101440_create_organismos_publicos_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organismos_publicos');
    }
}
