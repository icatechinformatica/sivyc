<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToConveniosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->string('tipo_convenio')->nullable();
            $table->string('nombre_firma')->nullable();
            $table->unsignedBigInteger('id_municipio')->nullable();
            $table->string('telefono_enlace')->nullable();
            $table->string('activo')->nullable();
            $table->string('sector')->nullable();
            $table->jsonb('unidades')->nullable();

            $table->foreign('id_municipio')->references('id')->on('tbl_municipios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropColumn('tipo_convenio');
            $table->dropColumn('nombre_firma');
            $table->dropColumn('id_municipio');
            $table->dropColumn('telefono_enlace');
            $table->dropColumn('activo');
            $table->dropColumn('sector');
            $table->dropColumn('unidades');
        });
    }
}
