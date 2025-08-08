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
        Schema::create('tbl_grupo_estatus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo')->constrained('tbl_grupos')->onDelete('cascade');
            $table->foreignId('id_estatus')->constrained('tbl_aux_estatus')->onDelete('set null');
            $table->foreignId('id_usuario')->constrained('tblz_usuarios')->onDelete('set null');
            $table->string('observaciones')->nullable();
            $table->string('memorandum')->nullable();
            $table->string('ruta_documento')->nullable();
            $table->date('fecha_cambio')->nullable();
            $table->boolean('es_ultimo_estatus')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_grupo_estatus');
    }
};
