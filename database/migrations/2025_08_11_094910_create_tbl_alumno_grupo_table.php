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
        Schema::create('tbl_alumno_grupo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('tbl_alumnos')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('tbl_grupos')->onDelete('cascade');
            $table->float('costo')->nullable();
            $table->string('comprobante_pago', 150)->nullable();
            $table->string('tinscripcion', 50)->nullable();
            $table->string('abrinscri', 15)->nullable();
            $table->string('folio_pago')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->integer('id_folio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_alumno_grupo');
    }
};
