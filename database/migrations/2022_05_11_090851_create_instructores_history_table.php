<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructoresHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructores_history', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('id_instructor');
            $table->string('nrevision');
            $table->integer('id_user');
            $table->string('movimiento');
            $table->string('status');
            $table->string('turnado');
            $table->jsonb('data_instructor');
            $table->jsonb('data_perfil');
            $table->jsonb('data_especialidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instructores_history');
    }
}
