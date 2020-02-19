<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     * Creado por Orlando Chávez
     * @return void
     */
    public function up()
    {
        Schema::create('instructor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('curp');
            $table->string('rfc');
           /* $table->string('sexo');
            $table->string('estado_civil');
            $table->date('fecha_nacimiento');
            $table->string('lugar_nacimiento');
            $table->string('lugar_residencia');
            $table->string('domicilio');
            $table->string('telefono');
            $table->string('correo');
            $table->string('clabe');
            $table->string('banco');
            $table->string('numero_cuenta');
            $table->string('grado_estudio');
            $table->string('perfil_profesional');
            $table->string('area_carrera');
            $table->string('licenciatura');
            $table->string('estatus');
            $table->string('institucion_pais');
            $table->string('institucion_entidad');
            $table->string('institucion_nombre');
            $table->date('fecha_documento');
            $table->string('folio_documento');
            $table->string('capacitado_icatech');*/
            $table->string('cv');
         /*   $table->string('numero_control');
            $table->string('honorario');
            $table->string('registro_agente');
            $table->string('uncap_validacion');
            $table->string('memo_validacion');
            $table->string('memo_mod');
            $table->string('observacion');
            $table->string('slug')->unique();*/
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
