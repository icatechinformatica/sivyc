<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaqueteriasDidacticasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paqueterias_didacticas', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('id_curso');
            $table->jsonb('carta_descriptiva');
            $table->jsonb('eval_alumno');
            $table->boolean('estatus');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('id_user_created');
            $table->bigInteger('id_user_updated')->nullable();
            $table->bigInteger('id_user_deleted')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paqueterias_didacticas');
    }
}
