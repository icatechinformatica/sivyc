<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign('pagos_id_status_foreign');
            $table->dropColumn('id_status');
            $table->string('nombre_para', 150);
            $table->string('puesto_para', 150);
            $table->string('no_pago',50)->nullable();
            $table->string('descripcion',300)->nullable();
            $table->date('fecha')->nullable()->change();

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
