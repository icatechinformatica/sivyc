<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModificationTblCalificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_calificaciones', function (Blueprint $table) {
            // eliminar columnas y agregar una nueva
            $table->dropColumn('hini2');
            $table->dropColumn('hfin2');
            $table->string('valido', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_calificaciones', function (Blueprint $table) {
            //
        });
    }
}
