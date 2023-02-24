<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_solicitudes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_curso')->nullable();
            $table->string('tipo_solicitud')->nullable();
            $table->string('num_solicitud')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->string('opcion_solicitud')->nullable();
            $table->text('obs_solicitud')->nullable();
            $table->string('archivo_solicitud')->nullable();
            $table->string('num_respuesta')->nullable();
            $table->date('fecha_respuesta')->nullable();
            $table->text('obs_respuesta')->nullable();
            $table->string('archivo_respuesta')->nullable();
            $table->string('status')->nullable();
            $table->string('turnado')->nullable();
            $table->unsignedBigInteger('iduser_created')->nullable();
            $table->unsignedBigInteger('iduser_updated')->nullable();

            $table->timestamps();

            $table->foreign('id_curso')->references('id')->on('tbl_cursos');
            $table->foreign('iduser_created')->references('id')->on('users');
            $table->foreign('iduser_updated')->references('id')->on('users');

            $table->unique(['id_curso','num_solicitud']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_solicitudes');
    }
}
