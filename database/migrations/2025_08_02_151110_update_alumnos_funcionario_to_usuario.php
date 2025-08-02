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
        Schema::table('tbl_alumnos', function (Blueprint $table) {
            // Eliminar la foreign key constraint existente
            $table->dropForeign(['id_funcionario_realizo']);

            // Renombrar la columna
            $table->renameColumn('id_funcionario_realizo', 'id_usuario_realizo');
        });

        Schema::table('tbl_alumnos', function (Blueprint $table) {
            // Agregar la nueva foreign key constraint hacia tblz_usuarios
            $table->foreign('id_usuario_realizo')->references('id')->on('tblz_usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_alumnos', function (Blueprint $table) {
            // Eliminar la foreign key constraint nueva
            $table->dropForeign(['id_usuario_realizo']);

            // Renombrar la columna de vuelta
            $table->renameColumn('id_usuario_realizo', 'id_funcionario_realizo');
        });

        Schema::table('tbl_alumnos', function (Blueprint $table) {
            // Restaurar la foreign key constraint original hacia tbl_funcionarios
            $table->foreign('id_funcionario_realizo')->references('id')->on('tbl_funcionarios')->onDelete('set null');
        });
    }
};
