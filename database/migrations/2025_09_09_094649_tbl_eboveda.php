<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('tbl_eboveda', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 100)->unique();
            $table->string('consecutivo', 100);
            $table->jsonb('data')->nullable();
            $table->integer('id_grupo')->nullable()->comment('id del grupo de usuarios que pueden ver el documento');
            $table->integer('id_estado')->nullable()->comment('id del estado del documento');
            $table->text('cadena_original')->nullable();
            $table->string('uuid_sellado', 150)->nullable();
            $table->timestamp('fecha_sellado')->nullable();
            $table->text('cadena_sellado')->nullable();
            $table->text('documento_md5')->nullable();
            $table->text('documento_xml')->nullable();
            $table->text('contenido')->nullable();
            $table->integer('id_tipo')->nullable()->comment('id del tipo de documento');
            $table->string('link_verificacion', 250)->nullable();
            $table->integer('id_status')->nullable()->comment('id del status del documento');
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
        //
    }
};
