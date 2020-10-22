<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSupervisionTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervision_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url_token', 255)->nullable();
            $table->string('tmp_token', 255)->nullable();
            $table->bigInteger('ttl')->nullable();
            $table->bigInteger('id_supervisor')->nullable();
            $table->bigInteger('id_curso')->nullable();
            $table->bigInteger('id_alumno')->nullable();
            $table->bigInteger('id_instructor')->nullable();
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
        Schema::dropIfExists('supervision_tokens');
    }
}
