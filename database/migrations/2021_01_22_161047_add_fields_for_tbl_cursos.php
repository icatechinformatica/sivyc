<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsForTblCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_cursos', function (Blueprint $table) {
            // modificaciones en turnados
            $table->jsonb('memos')->nullable();
            $table->jsonb('observaciones_formato_t')->nullable();
            $table->date('fecha_turnado')->nullable();
            $table->string('turnado', 255)->nullable();
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
