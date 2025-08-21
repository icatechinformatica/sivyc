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
        Schema::table('tbl_alumno_grupo', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('id_ultimo_grado')->nullable();
            $table->jsonb('grupos_vulnerables')->nullable();
            $table->string('medio_entero', 15)->nullable();
            $table->string('medio_confirmacion', 15)->nullable();

            $table->foreign('id_ultimo_grado')->references('id_grado_estudio')->on('tbl_cat_grado_estudios')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_alumno_grupo', function (Blueprint $table) {
            //
            $table->dropForeign(['id_ultimo_grado']);
            $table->dropColumn('id_ultimo_grado');
            $table->dropColumn('grupos_vulnerables');
            $table->dropColumn('medio_entero');
            $table->dropColumn('medio_confirmacion');
        });
    }
};
