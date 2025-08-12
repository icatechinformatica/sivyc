<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Agregar columna a tbl_grupos
        Schema::table('tbl_grupos', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_grupos', 'seccion_captura')) {
                $table->string('seccion_captura', 50)->nullable();
            }
        });

        // Quitar columna de tbl_grupo_estatus
        Schema::table('tbl_grupo_estatus', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_grupo_estatus', 'seccion')) {
                $table->dropColumn('seccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Restaurar columna en tbl_grupo_estatus
        Schema::table('tbl_grupo_estatus', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_grupo_estatus', 'seccion')) {
                $table->string('seccion', 50)->nullable();
            }
        });

        // Quitar columna de tbl_grupos
        Schema::table('tbl_grupos', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_grupos', 'seccion_captura')) {
                $table->dropColumn('seccion_captura');
            }
        });
    }
};
