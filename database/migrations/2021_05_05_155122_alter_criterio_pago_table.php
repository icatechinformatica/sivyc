<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCriterioPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criterio_pago', function (Blueprint $table) {
            $table->decimal('ze2_2021')->nullable();
            $table->decimal('ze3_2021')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('criterio_pago', function (Blueprint $table) {
            //
        });
    }
}
