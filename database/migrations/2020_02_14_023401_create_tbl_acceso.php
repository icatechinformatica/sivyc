<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblAcceso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_acceso', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombrecompleto');
            $table->integer('idcategoria');
            $table->integer('numeroenlace');
            $table->string('usuario');
            $table->string('contrasena');
            $table->string('correo')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('tbl_acceso');
    }
}
