<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblFolios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_folios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 50);
            $table->bigInteger('id_curso'); // relacionado con el curso
            $table->date('fecha_acta'); // relacionado con folios cambiar a id del folio
            $table->string('matricula', 30);
            $table->string('nombre', 80);
            $table->string('folio', 25);
            $table->date('fecha_expedicion');
            $table->string('movimiento', 50);
            $table->string('motivo', 30);
            $table->string('mod', 10);
            $table->string('fini', 15);
            $table->string('ffin', 15);
            $table->string('focan', 15); // null;
            $table->timestamps();

            $table->foreign('id_curso')
                  ->references('id')->on('tbl_cursos')
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
        Schema::dropIfExists('tbl_folios');
    }
}
