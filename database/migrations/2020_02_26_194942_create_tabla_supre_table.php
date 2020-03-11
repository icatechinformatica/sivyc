<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablaSupreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabla_supre', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad_capacitacion', 80);
            $table->string('no_memo', 80);
            $table->date('fecha');
            $table->string('nombre_para', 120);
            $table->string('puesto_para', 120);
            $table->string('nombre_remitente', 120);
            $table->string('puesto_remitente', 120);
            $table->string('nombre_ccp1', 120);
            $table->string('puesto_ccp1', 120);
            $table->string('nombre_ccp2', 120);
            $table->string('puesto_ccp2', 120);
            $table->string('nombre_valida', 120);
            $table->string('puesto_valida', 120);
            $table->string('nombre_elabora', 120);
            $table->string('puesto_elabora', 120);
            $table->string('status', 25);
            $table->string('observacion');
            $table->string('folio_validacion');
            $table->string('fecha_validacion');
            $table->string('nombre_firmante');
            $table->string('puesto_firmante');
            $table->string('val_ccp1');
            $table->string('val_ccpp1');
            $table->string('val_ccp2');
            $table->string('val_ccpp2');
            $table->string('val_ccp3');
            $table->string('val_ccpp3');
            $table->string('val_ccp4');
            $table->string('val_ccpp4');
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
        Schema::dropIfExists('tabla_supre');
    }
}
