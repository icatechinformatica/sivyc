<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_instructor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 30);
            $table->string('nombre', 100);
            $table->date('fnacimiento');
            $table->string('sexo', 15);
            $table->string('rfc', 30);
            $table->string('curp', 30);
            $table->string('muni', 50);
            $table->string('domi', 80);
            $table->string('col', 50);
            $table->bigInteger('codigo');
            $table->bigInteger('tcasa');
            $table->bigInteger('tcelular');
            $table->string('email', 80);
            $table->string('espe', 250);
            $table->string('inst', 15);
            $table->string('valida', 50);
            $table->string('mcontrato', 50);
            $table->string('contrato', 50);
            $table->string('hononetos', 15);
            $table->string('mocontrato', 30);
            $table->string('nrhono', 15);
            $table->string('escol', 80);
            $table->string('doc', 50); // pendiente a checar
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
        Schema::dropIfExists('tbl_instructor');
    }
}
