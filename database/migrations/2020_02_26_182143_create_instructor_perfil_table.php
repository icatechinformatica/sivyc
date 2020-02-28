<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorPerfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructor_perfil', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('especialidad', 100); //
            $table->string('clave_especialidad', 80); //
            $table->string('nivel_estudios_cubre_especialidad', 200); //
            $table->string('perfil_profesional', 100);
            $table->string('area_carrera', 100);
            $table->string('carrera', 100); //
            $table->string('estatus', 50);
            $table->string('pais_institucion', 50);
            $table->string('entidad_institucion', 50);
            $table->date('fecha_expedicion_documento');
            $table->string('folio_documento', 50);
            $table->integer('numero_control');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('numero_control')
                  ->references('id')->on('instructores')
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
        Schema::dropIfExists('instructor_perfil');
    }
}
