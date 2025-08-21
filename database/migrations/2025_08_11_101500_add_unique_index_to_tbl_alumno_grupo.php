<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_alumno_grupo', function (Blueprint $table) {
            // Agregar índice único si no existe aún
            $table->unique(['alumno_id', 'grupo_id'], 'tbl_alumno_grupo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_alumno_grupo', function (Blueprint $table) {
            $table->dropUnique('tbl_alumno_grupo_unique');
        });
    }
};
