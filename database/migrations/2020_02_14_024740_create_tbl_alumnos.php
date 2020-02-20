<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAlumnos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_alumnos', function (Blueprint $table) {
            $table->integer('id');
            $table->string('unidad', 50);
            $table->string('no_control', 30)->unique();
            $table->primary('no_control');
            $table->string('nombre', 50);
            $table->string('curp', 30);
            $table->string('domi', 100);
            $table->string('col', 80);
            $table->date('fnacimiento');
            $table->string('sexo', 10);
            $table->string('muni', 80);
            $table->string('ecivil', 30);
            $table->string('trabaja', 10);
            $table->string('escol', 50);
            $table->integer('rescol');
            $table->string('disca', 30);
            $table->integer('rdisca');
            $table->bigInteger('tcasa');
            $table->bigInteger('tcelular');
            $table->string('etnia', 30);
            $table->string('indigina', 10);
            $table->string('migrante', 10);
            $table->string('soltera', 10);
            $table->string('facetiw', 100);
            $table->string('correo', 100)->unique();
            $table->string('cerrs', 10);
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
        Schema::dropIfExists('tbl_alumnos');
    }
}
