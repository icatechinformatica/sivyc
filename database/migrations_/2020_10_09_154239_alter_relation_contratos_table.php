<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRelationContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign('contratos_instructor_perfilid_foreign');
        });

        Schema::table('contratos', function ($table) {
            $table->foreign('instructor_perfilid')->references('id')->on('especialidad_instructores');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contrato', function (Blueprint $table) {
            //
        });
    }
}
