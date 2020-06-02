<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnRealizoToAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            // agregar campos
            $table->string('realizo', 255)->nullable();
            $table->boolean('tiene_documentacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            //
        });
    }
}
