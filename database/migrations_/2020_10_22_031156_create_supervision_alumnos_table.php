<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisionAlumnosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervision_alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable(true);
            $table->string('apellido_paterno', 255)->nullable(true);
            $table->string('apellido_materno', 255)->nullable(true);
            $table->smallInteger('votes')->nullable(true);
            $table->string('escolaridad', 50)->nullable(true);
            $table->date('fecha_inscripcion')->nullable(true);
            $table->text('documentos')->nullable(true);
            $table->string('curso', 150)->nullable(true);
            $table->string('numero_apertura', 50)->nullable(true);
            $table->date('fecha_autorizacion')->nullable(true);
            $table->string('modalidad', 5)->nullable(true);
            $table->date('fecha_inicio')->nullable(true);
            $table->date('fecha_termino')->nullable(true);
            $table->string('hinicio', 10)->nullable(true);
            $table->string('hfin', 10)->nullable(true);
            $table->string('tipo', 15)->nullable(true);
            $table->string('lugar', 150)->nullable(true);
            $table->decimal('cuota', 10, 2);
            $table->boolean('ok_nombre');
            $table->boolean('ok_edad')->default(true);
            $table->boolean('ok_escolaridad');
            $table->boolean('ok_fecha_inscripcion')->default(true);
            $table->boolean('ok_documentos');
            $table->boolean('ok_curso');
            $table->boolean('ok_numero_apertura');
            $table->boolean('ok_fecha_autorizacion')->default(true);
            $table->boolean('ok_modalidad');
            $table->boolean('ok_fecha_termino');
            $table->boolean('ok_horario');
            $table->boolean('ok_tipo');
            $table->boolean('ok_lugar');
            $table->boolean('ok_cuota');
            $table->string('obs_nombre', 150)->nullable(true);
            $table->string('obs_edad', 150)->nullable(true);
            $table->string('obs_escolaridad', 150)->nullable(true);
            $table->string('obs_fecha_inscripcion', 150)->nullable(true);
            $table->string('obs_documentos', 150)->nullable(true);
            $table->string('obs_curso', 150)->nullable(true);
            $table->string('obs_numero_apertura', 150)->nullable(true);
            $table->string('obs_fecha_autorizacion', 150)->nullable(true);
            $table->string('obs_modalidad', 150)->nullable(true);
            $table->string('obs_fecha_inicio', 150)->nullable(true);
            $table->string('obs_fecha_termino', 150)->nullable(true);
            $table->string('obs_horario', 150)->nullable(true);
            $table->string('obs_tipo', 150)->nullable(true);
            $table->string('obs_lugar', 150)->nullable(true);
            $table->string('obs_cuota', 150)->nullable(true);
            $table->bigInteger('id_tbl_cursos')->nullable(true);
            $table->bigInteger('id_curso')->nullable(true);
            $table->bigInteger('id_alumno')->nullable(true);
            $table->bigInteger('id_instructor')->nullable(true);
            $table->boolean('enviado')->default(false);
            $table->bigInteger('id_tbl_inscripcion')->nullable(true);
            $table->bigInteger('id_user')->nullable(true);
            $table->text('comentarios');
            $table->string('unidad', 150)->nullable(true);
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
        Schema::dropIfExists('supervision_alumnos');
    }
}
