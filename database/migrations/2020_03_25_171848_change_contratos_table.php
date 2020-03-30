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
            $table->dropForeign('contratos_instructor_perfilid_foreign');
            $table->foreign('instructor_perfilid')
                  ->references('id')->on('especialidades')
                  ->onDelete('set null')->onUpdate('cascade');
            $table->decimal('cantidad_numero', 10, 2)->after('cantidad_letras1');

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
