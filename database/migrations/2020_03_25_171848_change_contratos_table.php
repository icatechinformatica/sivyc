<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('nombre_director');
            $table->dropColumn('testigo1');
            $table->dropColumn('testigo2');
            $table->dropColumn('puesto_testigo1');
            $table->dropColumn('puesto_testigo2');
            $table->dropColumn('cantidad_letras2');
            $table->dropColumn('numero_circular');
            $table->integer('contrato_idtestigo1')->after('fecha_firma');
            $table->integer('contrato_idtestigo2');
            $table->integer('contrato_idtestigo3');
            $table->decimal('cantidad_numero')->after('cantidad_letras1');
            $table->integer('contrato_iddirector');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contratos', function (Blueprint $table) {
            //
        });
    }
}
