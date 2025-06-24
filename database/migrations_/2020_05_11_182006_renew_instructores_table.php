<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenewInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructores', function (Blueprint $table) {
            $table->dropColumn('experiencia_laboral');
            $table->dropColumn('experiencia_docente');
            $table->dropColumn('cursos_recibidos');
            $table->dropColumn('capacitados_icatech');
            $table->dropColumn('curso_recibido_icatech');
            $table->dropColumn('cursos_impartidos');
            $table->dropColumn('observaciones');
            $table->dropColumn('cursos_conocer');
            $table->dropColumn('archivo_cv');

            $table->string('tipo_honorario')->nullable();
            $table->string('archivo_ine')->nullable();
            $table->string('archivo_domicilio')->nullable();
            $table->string('archivo_curp')->nullable();
            $table->string('archivo_alta')->nullable();
            $table->string('archivo_bancario')->nullable();
            $table->string('archivo_fotografia')->nullable();
            $table->string('archivo_estudios')->nullable();
            $table->string('archivo_otraid')->nullable();

            $table->string('domicilio')->nullable()->change();
            $table->string('banco')->nullable()->change();
            $table->string('no_cuenta')->nullable()->change();
            $table->string('interbancaria')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructores', function (Blueprint $table) {
            //
        });
    }
}
