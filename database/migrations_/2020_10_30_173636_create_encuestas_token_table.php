<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestasTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuestas_token', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url_token');
            $table->string('tmp_token');
            $table->bigInteger('ttl');
            $table->bigInteger('id_supervisor');
            $table->bigInteger('id_curso');
            $table->bigInteger('id_alumno');
            $table->bigInteger('id_instructor');
            $table->smallInteger('cantidad_usuarios');
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
        Schema::dropIfExists('encuestas_token');
    }
}
