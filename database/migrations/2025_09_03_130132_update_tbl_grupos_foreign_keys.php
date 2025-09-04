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
        Schema::table('tbl_grupos', function (Blueprint $table) {
            // Eliminar las foreign keys existentes
            $table->dropForeign(['id_modalidad']);
            $table->dropForeign(['id_imparticion']);
            $table->dropForeign(['id_servicio']);

            // Eliminar las columnas existentes
            $table->dropColumn(['id_modalidad', 'id_imparticion', 'id_servicio']);

            // Agregar las nuevas columnas con sus foreign keys
            $table->foreignId('id_modalidad_curso')->nullable()->constrained('tbl_aux_modalidad_curso', 'id_modalidad_curso')->onDelete('set null');
            $table->foreignId('id_tipo_curso')->nullable()->constrained('tbl_aux_tipo_curso', 'id_tipo_curso')->onDelete('set null');
            $table->foreignId('id_categoria_formacion')->nullable()->constrained('tbl_aux_categorias_formacion', 'id_categoria_formacion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_grupos', function (Blueprint $table) {
            // Eliminar las nuevas foreign keys
            $table->dropForeign(['id_modalidad_curso']);
            $table->dropForeign(['id_tipo_curso']);
            $table->dropForeign(['id_categoria_formacion']);

            // Eliminar las nuevas columnas
            $table->dropColumn(['id_modalidad_curso', 'id_tipo_curso', 'id_categoria_formacion']);

            // Restaurar las columnas originales
            $table->foreignId('id_modalidad')->nullable()->constrained('tbl_aux_modalidades')->onDelete('set null');
            $table->foreignId('id_imparticion')->nullable()->constrained('tbl_aux_imparticiones')->onDelete('set null');
            $table->foreignId('id_servicio')->nullable()->constrained('tbl_aux_servicios')->onDelete('set null');
        });
    }
};
