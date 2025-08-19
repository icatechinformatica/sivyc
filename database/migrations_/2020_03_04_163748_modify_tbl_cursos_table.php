<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTblCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_cursos', function (Blueprint $table) {
            //
            $table->integer('id_curso');
            $table->integer('id_instructor');

            /**
             * Llave foraneas
             */
            $table->foreign('id_curso')
                  ->references('id')->on('cursos')
                  ->onDelete('set null')->onUpdate('cascade');

            $table->foreign('id_instructor')
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
        Schema::table('tbl_cursos', function (Blueprint $table) {
            //
        });
    }
}
