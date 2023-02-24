<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSupervisionInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervision_instructores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->nullable(true);
            $table->string('apellido_paterno', 150)->nullable(true);
            $table->string('apellido_materno', 150)->nullable(true);
            $table->date('fecha_padron');
            $table->date('fecha_contrato');
            $table->string('cct', 15)->nullable(true);
            $table->string('nombre_curso', 255)->nullable(true);
            $table->date('inicio_curso');
            $table->date('termino_curso');
            $table->string('modalidad_curso', 30)->nullable(true);
            $table->string('tipo_curso', 15)->nullable(true);
            $table->bigInteger('total_mujeres')->nullable(true);
            $table->decimal('monto_honorarios', 10, 2);
            $table->bigInteger('total_hombres')->nullable(true);
            $table->string('lugar_curso', 150)->nullable(true);
            $table->bigInteger('id_tbl_cursos')->nullable(true);
            $table->string('obs_nombre', 150)->nullable(true);
            $table->string('obs_fecha_padron', 150)->nullable(true);
            $table->string('obs_fecha_contrato', 150)->nullable(true);
            $table->string('obs_honorarios', 150)->nullable(true);
            $table->string('obs_curso', 150)->nullable(true);
            $table->string('obs_horas_curso', 150)->nullable(true);
            $table->string('obs_modalidad', 150)->nullable(true);
            $table->string('obs_fecha_inicio', 150)->nullable(true);
            $table->string('obs_fecha_termino', 150)->nullable(true);
            $table->string('obs_mujeres', 150)->nullable(true);
            $table->string('obs_hombres', 150)->nullable(true);
            $table->string('obs_tipo', 150)->nullable(true);
            $table->string('obs_lugar', 150)->nullable(true);
            $table->string('hini_curso', 150)->nullable(true);
            $table->string('hfin_curso', 150)->nullable(true);
            $table->smallInteger('horas_curso');
            $table->smallInteger('horas_diarias');
            $table->bigInteger('id_instructor')->nullable(true);
            $table->bigInteger('id_curso')->nullable(true);
            $table->string('obs_horario', 150)->nullable(true);
            $table->string('obs_horas_diarias', 150)->nullable(true);
            $table->boolean('ok_nombre')->default(true);
            $table->boolean('ok_fecha_padron')->default(true);
            $table->boolean('ok_fecha_contrato')->default(true);
            $table->boolean('ok_honorarios')->default(true);
            $table->boolean('ok_curso')->default(true);
            $table->boolean('ok_horas_curso')->default(true);
            $table->boolean('ok_modalidad')->default(true);
            $table->boolean('ok_mujeres')->default(true);
            $table->boolean('ok_hombres')->default(true);
            $table->boolean('ok_tipo')->default(true);
            $table->boolean('ok_lugar')->default(true);
            $table->boolean('ok_horas_diarias')->default(true);
            $table->boolean('ok_horario')->default(true);
            $table->text('comentarios');
            $table->boolean('enviado')->default(false);
            $table->boolean('ok_fecha_inicio')->default(true);
            $table->boolean('ok_fecha_termino')->default(true);
            $table->string('numero_apertura', 50)->nullable(true);
            $table->date('fecha_autorizacion');
            $table->boolean('ok_numero_apertura')->default(true);
            $table->boolean('ok_fecha_autorizacion')->default(true);
            $table->string('obs_numero_apertura', 150)->nullable(true);
            $table->string('obs_fecha_autorizacion', 150)->nullable(true);
            $table->bigInteger('id_user')->nullable(true);
            $table->string('unidad', 50)->nullable(true);
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
        Schema::dropIfExists('supervision_instructores');
    }
}
