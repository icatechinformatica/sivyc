<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_alumno_grupo_vulnerable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->unsignedBigInteger('grupo_vulnerable_id');

            $table->foreign('alumno_id')->references('id')->on('tbl_alumnos')->onDelete('cascade');
            $table->foreign('grupo_vulnerable_id')->references('id_grupo_vulnerable')->on('tbl_aux_grupo_vulnerable')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_alumno_grupo_vulnerable');
    }
};
