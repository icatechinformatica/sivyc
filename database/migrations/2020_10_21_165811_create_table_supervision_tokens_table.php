<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSupervisionTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervision_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('url_token', 255)->nullable(true);
            $table->string('tmp_token', 255)->nullable(true);
            $table->bigInteger('ttl')->nullable(true);
            $table->bigInteger('id_supervisor')->nullable(true);
            $table->bigInteger('id_curso')->nullable(true);
            $table->bigInteger('id_alumno')->nullable(true);
            $table->bigInteger('id_instructor')->nullable(true);
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
