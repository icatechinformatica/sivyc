<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosRegistroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos_registro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_control', 255);
            $table->date('fecha');
            $table->string('numero_solicitud', 30);
            $table->char('sexo', 10);
            $table->string('curp', 30);
            $table->date('fecha_nacimiento');
            $table->string('domicilio', 100);
            $table->string('colonia', 100);
            $table->integer('codigo_postal');
            $table->string('municipio', 100);
            $table->string('estado', 100);
            $table->string('estado_civil', 100);
            $table->string('discapacidad_presente', 100);
            $table->string('ultimo_grado_estudios', 100);
            $table->string('empresa_trabaja', 150);
            $table->string('antiguedad', 150);
            $table->string('direccion_empresa', 255);
            $table->boolean('cerrs')->nullable();
            $table->string('etnia', 100);
            $table->boolean('indigena')->nullable();
            $table->boolean('migrante')->nullable();
            $table->integer('id_pre');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('id_pre')
                  ->references('id')->on('alumnos_pre')
                  ->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos_registro');
    }
}
