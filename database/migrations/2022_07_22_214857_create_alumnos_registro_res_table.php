<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlumnosRegistroResTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alumnos_registro_res', function (Blueprint $table) {
            $table->id();
            $table->string('no_control')->nullable();
            $table->date('fecha')->nullable();
            $table->string('numero_solicitud')->nullable();
            $table->boolean('cerrs')->nullable();
            $table->string('etnia')->nullable();
            $table->boolean('indigena')->nullable();
            $table->boolean('migrante')->nullable();
            $table->integer('id_pre')->nullable();
            $table->timestamps();
            $table->bigInteger('id_curso')->nullable();
            $table->string('horario')->nullable();
            $table->string('grupo')->nullable();
            $table->bigInteger('id_especialidad')->nullable();
            $table->string('unidad')->nullable();
            $table->string('tipo_curso')->nullable();
            $table->string('realizo')->nullable();
            $table->boolean('estatus_modificacion')->nullable();
            $table->string('medio_entero')->nullable();
            $table->string('motivo_eleccion')->nullable();
            $table->unsignedDecimal('costo',8,2)->nullable();
            $table->string('comprobante_pago')->nullable();
            $table->string('tinscripcion')->nullable();
            $table->string('abrinscri')->nullable();
            $table->bigInteger('iduser_created')->nullable();
            $table->bigInteger('iduser_updated')->nullable();
            $table->bigInteger('id_cerss')->nullable();
            $table->bigInteger('id_unidad')->nullable();
            $table->integer('ejercicio')->nullable();
            $table->string('folio_grupo')->nullable();
            $table->string('cct')->nullable();
            $table->date('fecha_turnado')->nullable();
            $table->string('turnado')->nullable();
            $table->boolean('eliminado')->nullable();
            $table->date('inicio')->nullable();
            $table->date('termino')->nullable();
            $table->bigInteger('id_muni')->nullable();
            $table->string('organismo_publico')->nullable();
            $table->bigInteger('id_organismo')->nullable();
            $table->string('grupo_vulnerable')->nullable();
            $table->bigInteger('id_vulnerable')->nullable();
            $table->string('mod')->nullable();
            $table->string('clave_localidad')->nullable();
        });
    }
    //php artisan migrate --path=database/migrations/2022_07_22_214857_create_alumnos_registro_res_table.php
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alumnos_registro_res');
    }
}
