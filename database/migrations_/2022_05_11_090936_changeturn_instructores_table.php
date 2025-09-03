<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeturnInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructores', function (Blueprint $table) {
            $table->string('turnado')->nullable();
            $table->string('nrevision')->nullable();
            $table->jsonb('entrevista')->nullable();
            $table->jsonb('exp_docente')->nullable();
            $table->jsonb('exp_laboral')->nullable();
            $table->integer('telefono_casa')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('entidad_nacimiento')->nullable();
            $table->string('municipio_nacimiento')->nullable();
            $table->string('localidad_nacimiento')->nullable();
            $table->string('clave_loc_nacimiento')->nullable();
            $table->integer('codigo_postal')->nullable();
            $table->bigint('telefono_casa')->nullable();
            $table->string('curriculum')->nullable();
            $table->string('arch_curriculum_personal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructores', function (Blueprint $table) {
            //
        });
    }
}
