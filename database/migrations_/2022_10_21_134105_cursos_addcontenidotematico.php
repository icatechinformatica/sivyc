<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CursosAddcontenidotematico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {           
            $table->jsonb('carta_descriptiva')->nullable();
            $table->jsonb('eval_alumno')->nullable();
            $table->string('estatus_paqueteria')->nullable();
            $table->boolean('active')->nullable();
            $table->string('tipoSoli')->nullable();
            $table->string('motivoSoli',2000)->nullable();
            $table->string('observaciones',2000)->nullable();            
            $table->timestamp('fecha_alta')->nullable();
            $table->timestamp('fecha_u_mod')->nullable();
            $table->timestamp('fecha_baja')->nullable();
            $table->bigInteger('id_user_created')->nullable();
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
        Schema::table('cursos', function (Blueprint $table) {
            //
        });
    }
}
