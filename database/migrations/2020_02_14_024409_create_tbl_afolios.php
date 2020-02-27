<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAfolios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_afolios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unidad', 50);
            $table->string('finicial', 15);
            $table->string('ffinal', 15);
            $table->integer('total');
            $table->string('mod', 10);
            $table->date('facta'); // relacionado con tabla folios
            $table->integer('idfolios'); // relacion con la tabla folios
            $table->timestamps();

            /*$table->foreign('idfolios')
                ->references('id')->on('tbl_folios')
                ->onDelete('set null')->onUpdate('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_afolios');
    }
}
