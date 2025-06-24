<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_unidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 50);
            $table->string('cct', 15);
            $table->string('dunidad', 150);
            $table->string('dgeneral', 150);
            $table->bigInteger('plantel');
            $table->string('academico', 150);
            $table->string('vinculacion', 150);
            $table->string('dacademico', 150);
            $table->string('pdgeneral', 150);
            $table->string('pdacademico', 150);
            $table->string('pdunidad', 150);
            $table->string('pacademico', 150);
            $table->string('pvinculacion', 150);
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
        Schema::dropIfExists('tbl_unidades');
    }
}
