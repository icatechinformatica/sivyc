<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cursos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cct', 15);
            $table->string('unidad', 30);
            $table->string('nombre', 50);
            $table->string('curp', 30);
            $table->string('rfc', 30);
            $table->string('clave', 30);
            $table->bigInteger('grupo');
            $table->string('mvalida', 30);
            $table->string('mod', 15);
            $table->string('turno', 15);
            $table->string('area', 80);
            $table->string('espe', 100);
            $table->string('curso', 100);
            $table->date('inicio');
            $table->date('termino');
            $table->string('dia', 100);
            $table->string('dia2', 100);
            $table->integer('pini');
            $table->integer('pfin');
            $table->integer('dura');
            $table->string('hini', 15);
            $table->string('hfin', 15);
            $table->string('horas', 10);
            $table->string('ciclo', 15);
            $table->string('plantel', 30);
            $table->text('depen');
            $table->string('muni', 50);
            $table->string('sector', 10);
            $table->string('programa', 50);
            $table->text('nota');
            $table->string('hini2', 15);
            $table->string('hfin2', 15);
            $table->string('munidad', 50);
            $table->string('efisico', 250);
            $table->string('cespecifico', 50);
            $table->string('mpaqueteria', 30);
            $table->string('mexoneracion', 10);
            $table->integer('hombre');
            $table->integer('mujer');
            $table->string('tipo', 15);
            $table->date('fcespe');
            $table->string('cgeneral', 15);
            $table->date('fcgen');
            $table->string('opcion', 50);
            $table->text('motivo');
            $table->integer('cp');
            $table->string('ze', 15);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('tbl_cursos');
    }
}
