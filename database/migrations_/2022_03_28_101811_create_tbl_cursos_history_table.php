<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblCursosHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_cursos_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tbl_cursos');
            $table->string('cct');
            $table->string('unidad');
            $table->string('nombre');
            $table->string('curp');
            $table->string('rfc');
            $table->string('clave');
            $table->text('mvalida');
            $table->string('mod', 4);
            $table->string('area', 80);
            $table->string('espe', 100);
            $table->string('curso', 100);
            $table->date('inicio');
            $table->date('termino');
            $table->string('dia',100);
            $table->integer('dura');
            $table->string('hini', 15);
            $table->string('hfin', 15);
            $table->string('horas', 10);
            $table->string('ciclo', 15);
            $table->string('plantel', 50);
            $table->text('depen');
            $table->string('muni', 50);
            $table->string('sector', 10);
            $table->string('programa', 50);
            $table->text('nota');
            $table->string('munidad', 60);
            $table->string('efisico', 250);
            $table->string('cespecifico', 60);
            $table->string('mpaqueteria', 60);
            $table->string('mexoneracion', 60);
            $table->integer('hombre');
            $table->integer('mujer');
            $table->string('tipo', 15);
            $table->date('fcespe')->nullable();
            $table->string('cgeneral', 60);
            $table->date('fcgen')->nullable();
            $table->string('opcion', 50);
            $table->text('motivo');
            $table->integer('cp');
            $table->string('ze', 15);
            $table->timestamps();
            $table->integer('id_curso');
            $table->integer('id_instructor');
            $table->string('modinstructor',255);
            $table->string('nmunidad',255);
            $table->string('nmacademico',255);
            $table->text('observaciones');
            $table->string('status',255);
            $table->string('realizo',255);
            $table->string('valido',255);
            $table->string('arc',5);
            $table->string('tcapacitacion',255);
            $table->string('status_curso',30)->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_modificacion')->nullable();
            $table->unsignedDecimal('costo',8,2);
            $table->text('motivo_correccion')->nullable();
            $table->string('pdf_curso',255)->nullable();
            $table->json('json_supervision')->nullable();
            $table->jsonb('memos')->nullable();
            $table->jsonb('observaciones_formato_t')->nullable();
            $table->date('fecha_turnado')->nullable();
            $table->string('turnado',255);
            $table->boolean('proceso_terminado');
            $table->string('tipo_curso',25);
            $table->date('fecha_envio')->nullable();
            $table->bigInteger('id_especialidad');
            $table->string('instructor_escolaridad',25);
            $table->string('instructor_titulo',25);
            $table->string('instructor_sexo');
            $table->string('instructor_mespecialidad');
            $table->string('medio_virtual',25)->nullable();
            $table->string('folio_grupo',15);
            $table->bigInteger('id_municipio');
            $table->string('link_virtual',255)->nullable();
            $table->string('clave_especialidad',50);
            $table->string('file_arc01',255)->nullable();
            $table->string('file_arc02',255)->nullable();
            $table->json('mov_arc02')->nullable();
            $table->bigInteger('id_cerss')->nullable();
            $table->bigInteger('tdias');
            $table->boolean('asis_finalizado');
            $table->boolean('calif_finalizado');
            $table->bigInteger('clave_localidad');
            $table->bigInteger('id_gvulnerable')->nullable();
            $table->date('fecha_arc01')->nullable();
            $table->date('fecha_arc02')->nullable();
            $table->string('instructor_tipo_identificacion');
            $table->string('instructor_folio_identificacion');
            $table->string('status_solicitud');
            $table->timestamp('fenviado_preliminar');
            $table->timestamp('frespuesta_preliminar')->nullable();
            $table->text('obspreliminar')->nullable();
            $table->string('num_revision', 60)->nullable();
        });
    }
    //php artisan migrate --path=database/migrations/2022_03_28_101811_create_tbl_cursos_history_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_cursos_history');
    }
}
