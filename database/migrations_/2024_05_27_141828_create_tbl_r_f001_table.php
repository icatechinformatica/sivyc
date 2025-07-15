<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblRF001Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_rf001', function (Blueprint $table) {
            $table->id();
            $table->string('memorandum', 150);
            $table->string('estado', 100);
            $table->json('movimientos');
            $table->bigInteger('id_unidad');
            $table->string('envia', 250);
            $table->string('dirigido', 250);
            $table->json('archivos');
            $table->string('unidad', 150);
            $table->date('periodo_inicio');
            $table->date('periodo_fin');
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
        Schema::dropIfExists('tbl_rf001');
    }
}
