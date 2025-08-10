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
        Schema::create('tbl_grupo_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_grupo')->constrained('tbl_grupos')->onDelete('cascade');
            $table->datetime('fecha_inicio');
            $table->datetime('fecha_fin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_grupo_agenda');
    }
};
