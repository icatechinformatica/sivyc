<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCriterioPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criterio_pago', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('perfil_profesional');
            $table->decimal('monto_hora_ze2');
            $table->decimal('monto_hora_ze3');
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
        Schema::dropIfExists('categoria_pago');
    }
}
