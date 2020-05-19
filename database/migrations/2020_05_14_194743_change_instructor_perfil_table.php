<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInstructorPerfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructor_perfil', function (Blueprint $table) {
            $table->dropColumn('especialidad');
            $table->dropColumn('clave_especialidad');
            $table->dropColumn('nivel_estudios_cubre_especialidad');
            $table->dropColumn('carrera');
            $table->dropColumn('tipo_honorario');
            $table->dropColumn('unidad_capacitacion_solicita_validacion');
            $table->dropColumn('memorandum_validacion');
            $table->dropColumn('fecha_validacion');
            $table->dropColumn('modificacion_memo');
            $table->dropColumn('critero_pago_id');
            $table->dropColumn('registro_agente_capacitador_externo');

            $table->string('ciudad_institucion')->nullable();
            $table->string('nombre_institucion')->nullable();
            $table->string('grado_profesional')->nullable();
            $table->string('experiencia_laboral')->nullable();
            $table->string('experiencia_docente')->nullable();
            $table->string('cursos_recibidos')->nullable();
            $table->string('estandar_conocer')->nullable();
            $table->string('registro_stps')->nullable();
            $table->string('capacitador_icatech')->nullable();
            $table->string('recibidos_icatech')->nullable();
            $table->string('cursos_impartidos')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructor_perfil', function (Blueprint $table) {
            //
        });
    }
}
