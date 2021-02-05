<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTblUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *comentario
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_unidades', function (Blueprint $table) {
            $table->string('direccion')->nullable();
            $table->bigInteger('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('coordenadas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_unidades', function (Blueprint $table) {
            //
        });
    }
}
