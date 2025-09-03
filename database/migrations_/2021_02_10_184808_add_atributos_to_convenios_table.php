<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtributosToConveniosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->string('correo_institucion')->nullable();
            $table->string('correo_enlace')->nullable();
            $table->unsignedBigInteger('id_estado')->nullable();
            $table->foreign('id_estado')->references('id')->on('estados');

            $table->string('fecha_firma')->nullable()->change();
            $table->string('fecha_vigencia')->nullable()->change();
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
            $table->dropColumn('correo_institucion');
            $table->dropColumn('correo_enlace');
            $table->dropColumn('id_estado');

            $table->string('fecha_vigencia')->change();
        });
    }
}
