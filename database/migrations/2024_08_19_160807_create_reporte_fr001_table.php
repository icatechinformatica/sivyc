<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReporteFr001Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reporte_rf001', function (Blueprint $table) {
            $table->id();
            $table->integer('id_rf001');
            $table->string('status_doc', 50)->nullable();
            $table->string('no_oficio', 50)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('uuid_sellado', 255)->nullable();
            $table->datetime('fecha_sellado');
            $table->string('nombre_archivo', 255)->nullable();
            $table->text('cadena_sello')->nullable();
            $table->string('link_verificar', 255)->nullable();
            $table->jsonb('hcancelado')->nullable();
            $table->jsonb('obj_documento')->nullable();
            $table->text('cadena_original')->nullable();
            $table->xml('documento_xml')->nullable();
            $table->integer('iduser_create')->nullable();
            $table->intener('id_user_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reporte_rf001');
    }
}
