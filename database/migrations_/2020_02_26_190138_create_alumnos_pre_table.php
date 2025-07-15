<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos_pre', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 255);
            $table->string('apellidoPaterno', 255);
            $table->string('apellidoMaterno', 255);
            $table->string('correo', 50)->unique()->nullable();
            $table->bigInteger('telefono');
            $table->string('curso', 255);
            $table->string('horario', 100);
            $table->string('especialidad_que_desea_inscribirse', 100);
            $table->string('modo_entero_del_sistema', 100);
            $table->string('motivos_eleccion_sistema_capacitacion', 200);
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
        Schema::dropIfExists('alumnos_pre');
    }
}
