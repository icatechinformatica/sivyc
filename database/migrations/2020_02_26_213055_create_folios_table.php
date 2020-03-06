<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folios', function (Blueprint $table) {
            $table->bigIncrements('id_folios');
            $table->string('numero_presupuesto', 80);
            $table->string('folio_validacion', 80);
            $table->integer('iva');
            $table->decimal('importe_hora', 10, 2);
            $table->decimal('importe_total', 10, 2);
            $table->integer('id_supre');
            $table->integer('id_cursos');
            $table->timestamps();

            /**
             * llave foranea
             */
            $table->foreign('id_supre')
                  ->references('id')->on('tabla_supre')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_cursos')
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
        Schema::dropIfExists('folios');
    }
}
