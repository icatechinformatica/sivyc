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

            $table->foreignId('id_unidad')->nullable()->constrained('tbl_unidades')->onDelete('set null');
            $table->foreignId('id_instructor')->nullable()->constrained('tbl_instructores')->onDelete('set null');

            $table->string('clave_grupo', 10)->nullable()->unique();
            $table->foreignId('id_curso')->nullable()->constrained('cursos')->onDelete('set null');

            $table->foreignId('id_modalidad')->nullable()->constrained('tbl_aux_modalidades')->onDelete('set null'); // * CAE y EXT
            $table->foreignId('id_imparticion')->nullable()->constrained('tbl_aux_imparticiones')->onDelete('set null'); // * Presencial y A Distancia
            $table->foreignId('id_servicio')->nullable()->constrained('tbl_aux_servicios')->onDelete('set null'); // * Curso y Certificación

            $table->foreignId('id_organismo_publico')->nullable()->constrained('organismos_publicos')->onDelete('set null');
            $table->foreignId('id_municipio')->nullable()->constrained('tbl_municipios')->onDelete('set null');
            $table->foreignId('id_localidad')->nullable()->constrained('tbl_localidades')->onDelete('set null');
            $table->string('programa')->nullable();

            $table->string('efisico', 255)->nullable();
            $table->string('cespecifico', 60)->nullable();
            $table->foreignId('id_tipo_exoneracion')->nullable()->constrained('tbl_exoneraciones')->onDelete('set null');
            $table->string('medio_virtual', 25)->nullable();
            $table->string('link_virtual', 255)->nullable();
            $table->foreignId('id_cerss')->nullable()->constrained('cerss')->onDelete('set null');
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
