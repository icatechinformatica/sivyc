<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnEspecialidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('especialidades', function (Blueprint $table) {
            //
            $table->bigInteger('id_areas')->nullable();
            $table->dropColumn('especialidad_ocupacional');

            $table->foreign('id_areas')->references('id')->on('area')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('especialidades', function (Blueprint $table) {
            //
        });
    }
}
