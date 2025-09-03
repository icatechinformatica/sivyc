<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAddEspecialidadInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructores', function (Blueprint $table) {
            //
            $table->bigInteger('id_especialidad')->nullable();

            $table->foreign('id_especialidad')->references('id')
                  ->on('especialidades')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructores', function (Blueprint $table) {
            //
        });
    }
}
