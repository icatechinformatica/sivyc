<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTablaSupreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tabla_supre', function (Blueprint $table) {
            // eliminar columnas
            $table->dropColumn('nombre_para');
            $table->dropColumn('puesto_para');
            $table->dropColumn('nombre_remitente');
            $table->dropColumn('puesto_remitente');
            $table->dropColumn('nombre_ccp1');
            $table->dropColumn('puesto_ccp1');
            $table->dropColumn('nombre_ccp2');
            $table->dropColumn('puesto_ccp2');
            $table->dropColumn('nombre_valida');
            $table->dropColumn('puesto_valida');
            $table->dropColumn('nombre_elabora');
            $table->dropColumn('puesto_elabora');
            $table->dropColumn('nombre_firmante');
            $table->dropColumn('puesto_firmante');
            $table->dropColumn('val_ccp1');
            $table->dropColumn('val_ccpp1');
            $table->dropColumn('val_ccp2');
            $table->dropColumn('val_ccpp2');
            $table->dropColumn('val_ccp3');
            $table->dropColumn('val_ccpp3');
            $table->dropColumn('val_ccp4');
            $table->dropColumn('val_ccpp4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tabla_supre', function (Blueprint $table) {
            //
        });
    }
}
