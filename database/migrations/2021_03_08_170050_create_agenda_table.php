<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('textColor');
            $table->text('observaciones');
            $table->unsignedBigInteger('id_curso')->nullable();
            $table->unsignedBigInteger('id_instructor')->nullable();
            $table->unsignedBigInteger('id_grupo')->nullable();
            $table->unsignedBigInteger('iduser_created')->nullable();
            $table->unsignedBigInteger('iduser_updated')->nullable();

            $table->foreign('id_curso')->references('id')->on('tbl_cursos');
            $table->foreign('id_instructor')->references('id')->on('instructores');

            $table->foreign('iduser_created')->references('id')->on('users');
            $table->foreign('iduser_updated')->references('id')->on('users');

            $table->unique(['start', 'end', 'id_instructor']);

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
        Schema::dropIfExists('agenda');
    }
}
