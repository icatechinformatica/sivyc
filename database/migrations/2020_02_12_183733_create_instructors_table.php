<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('curriculum');
            $table->string('certificado_estudios');
            $table->string('constancia_cursos');
            $table->string('acta_nacimiento');
            $table->string('ine');
            $table->string('comprobante_domicilio');
            $table->string('constancia_agente');
            $table->string('seleccion_firmada');
            $table->string('formato_entrevista');
            $table->string('curp');
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('correo');
            $table->string('especialidad');
            $table->string('observacion');
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
        Schema::dropIfExists('instructor');
    }
}
