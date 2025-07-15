<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('nombre_ccp1');
            $table->dropColumn('puesto_ccp1');
            $table->dropColumn('nombre_ccp2');
            $table->dropColumn('puesto_ccp2');
            $table->dropColumn('nombre_ccp3');
            $table->dropColumn('puesto_ccp3');
            $table->dropColumn('elaboro');
            $table->dropColumn('nombre_para');
            $table->dropColumn('puesto_para');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
            //
        });
    }
}
