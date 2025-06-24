<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreliminarToExoneraciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exoneraciones', function (Blueprint $table) {
            $table->string('turnado')->nullable();
            $table->string('folio_grupo')->nullable();
            $table->string('nrevision')->nullable();
            $table->string('noficio')->nullable();
            $table->date('foficio')->nullable();
            $table->string('fini')->nullable();
            $table->string('ffin')->nullable();
            $table->string('realizo')->nullable();
            $table->string('valido')->nullable();
            $table->timestamp('fenvio')->nullable();
            $table->timestamp('frespuesta')->nullable();
            $table->text('pobservacion')->nullable();
            $table->unsignedBigInteger('ejercicio')->nullable();
            $table->string('cct')->nullable();
            $table->string('motivo')->nullable();
        });
    }
    //php artisan migrate --path=database/migrations/2022_05_18_110429_add_preliminar_to_exoneraciones.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exoneraciones', function (Blueprint $table) {
            $table->dropColumn('turnado');
            $table->dropColumn('folio_grupo');
            $table->dropColumn('nrevision');
            $table->dropColumn('noficio');
            $table->dropColumn('foficio');
            $table->dropColumn('fini');
            $table->dropColumn('ffin');
            $table->dropColumn('realizo');
            $table->dropColumn('valido');
            $table->dropColumn('fenvio');
            $table->dropColumn('frespuesta');
            $table->dropColumn('pobservacion');
            $table->dropColumn('ejercicio');
            $table->dropColumn('cct');
            $table->dropColumn('motivo');
        });
    }
}
