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
        Schema::create('tbl_grupos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_unidad')->constrained('tbl_unidades');
            $table->foreignId('id_instructor')->constrained('tbl_instructores');

            $table->string('clave_grupo', 10)->unique();
            $table->foreignId('id_modalidad_curso')->constrained('tbl_aux_modalidad_curso')->onDelete('set null');
            $table->foreignId('id_curso')->constrained('cursos')->onDelete('set null');
            $table->foreignId('id_modalidad_capacitacion')->constrained('tbl_modalidades_cursos')->onDelete('set null');
            $table->foreignId('id_organismo_publico')->constrained('organismos_publicos')->onDelete('set null');
            $table->foreignId('id_municipio')->constrained('tbl_municipios')->onDelete('set null');
            $table->foreignId('id_localidad')->constrained('tbl_localidades')->onDelete('set null');
            $table->string('programa')->nullable();

            $table->string('efisico', 255)->nullable();
            $table->string('cespecifico', 60)->nullable();
            $table->foreignId('id_tipo_exoneracion')->constrained('tbl_exoneraciones')->onDelete('set null');
            $table->string('medio_virtual', 25)->nullable();
            $table->string('link_virtual', 255)->nullable();
            $table->foreignId('id_cerss')->constrained('cerss')->onDelete('set null');
            $table->boolean('asis_finalizado')->default(false);
            $table->boolean('calif_finalizado')->default(false);
            $table->string('num_revision', 255)->nullable();
            $table->string('num_revision_arc02', 255)->nullable();
            $table->string('evidencia_fotografica', 255)->nullable();
            $table->boolean('vb_dg')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_grupos');
    }
};
