<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryExoneracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_exoneraciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_exoneracion');
            $table->string('folio_grupo');
            $table->unsignedBigInteger('id_unidad_capacitacion');
            $table->string('no_memorandum')->nullable();
            $table->date('fecha_memorandum')->nullable();
            $table->string('tipo_exoneracion');
            $table->string('razon_exoneracion');
            $table->text('observaciones');
            $table->string('no_convenio')->nullable();
            $table->string('memo_soporte_dependencia');
            $table->unsignedBigInteger('iduser_created');
            $table->unsignedBigInteger('iduser_updated')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->string('status')->nullable();
            $table->string('nrevision');
            $table->string('noficio')->nullable();
            $table->date('foficio')->nullable();
            $table->string('fini')->nullable();
            $table->string('ffin')->nullable();
            $table->string('realizo');
            $table->string('valido')->nullable();
            $table->timestamp('fenvio')->nullable();
            $table->timestamp('frespuesta')->nullable();
            $table->text('pobservacion')->nullable();
            $table->string('turnado');
            $table->unsignedBigInteger('ejercicio');
            $table->string('cct');
            $table->string('activo')->nullable();
            $table->string('motivo')->nullable();
        });
    }
    //php artisan migrate --path=database/migrations/2022_05_18_105253_create_history_exoneraciones_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_exoneraciones');
    }
}
