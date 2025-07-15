<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemoranduTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_memorandum', function (Blueprint $table) {
            $table->id();
            $table->integer('consecutivo')->unique();
            $table->string('descripcion', 200);
            $table->string('unidad', 150);
            $table->integer('contador');
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
        Schema::dropIfExists('tbl_memorandum');
    }
}
