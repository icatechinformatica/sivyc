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
        Schema::create('tbl_alumno_estatus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_alumno')->constrained('tbl_alumnos')->onDelete('cascade');
            $table->foreignId('id_estatus')->constrained('tbl_aux_estatus')->onDelete('cascade');
            $table->jsonb('secciones')->nullable();
            $table->date('fecha_estatus')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_alumno_estatus');
    }
};
