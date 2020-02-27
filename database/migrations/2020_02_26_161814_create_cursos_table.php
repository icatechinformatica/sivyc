<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('especialidad', 80);
            $table->string('nombre_curso', 80);
            $table->char('modalidad', 10);
            $table->integer('horas');
            $table->string('clasificacion', 50);
            $table->decimal('costo', 10, 2);
            $table->integer('duracion');
            $table->string('objetivo', 255);
            $table->string('perfil', 255);
            $table->boolean('solicitud_autorizacion')->nullable();
            $table->date('fecha_validacion');
            $table->string('memo_validacion', 200);
            $table->string('memo_actualizacion', 200);
            $table->date('fecha_actualizacion');
            $table->string('unidad_amovil', 200);
            $table->text('descripcion');
            $table->string('no_convenio', 30);
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
        Schema::dropIfExists('cursos');
    }
}
