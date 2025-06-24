<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInstructorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructor', function (Blueprint $table) {
            $table->string('numero_control')->after('id');
            $table->renameColumn('apellido_paterno', "apellidoPaterno")->nullable();
            $table->renameColumn('apellido_materno', "apellidoMaterno")->nullable();
            $table->dropColumn('cv')->nullable();
            $table->string('sexo')->before('created_at')->nullable();
            $table->string('estado_civil')->before('created_at')->nullable();
            $table->string('fecha_nacimiento')->before('created_at')->nullable();
            $table->string('entidad')->before('created_at')->nullable();
            $table->string('municipio')->before('created_at')->nullable();
            $table->string('asentamiento')->before('created_at')->nullable();
            $table->string('domicilio')->before('created_at')->nullable();
            $table->string('telefono')->before('created_at')->nullable();
            $table->string('correo')->before('created_at')->nullable();
            $table->string('banco')->before('created_at')->nullable();
            $table->string('no_cuenta')->before('created_at')->nullable();
            $table->string('interbancaria')->before('created_at')->nullable();
            $table->string('folio_ine')->before('created_at')->nullable();
            $table->biginteger('id_especialidad')->nullable();
            $table->string('tipo_honorario')->nullable();
            $table->string('archivo_ine')->nullable();
            $table->string('archivo_domicilio')->nullable();
            $table->string('archivo_curp')->nullable();
            $table->string('archivo_alta')->nullable();
            $table->string('archivo_bancario')->nullable();
            $table->string('archivo_fotografia')->nullable();
            $table->string('archivo_estudios')->nullable();
            $table->string('archivo_otraid')->nullable();
            $table->string('status')->nullable();
            $table->string('rechazo')->nullable();
            $table->string('clave_unidad')->nullable();
            $table->string('motivo')->nullable();
            $table->string('archivo_rfc')->nullable();
            $table->json('unidades_disponible')->nullable();
            $table->boolean('estado')->nullable();
            $table->string('lastUserId')->nullable();
            $table->string('extracurricular')->nullable();
            $table->string('stps')->nullable();
            $table->string('conocer')->nullable();
            $table->biginteger('clave_loc')->nullable();
            $table->string('localidad')->nullable();
            $table->string('tipo_identificacion')->nullable();
            $table->date('expiracion_identificacion')->nullable();
            $table->string('turnado')->nullable();
            $table->string('nrevision')->nullable();
            $table->jsonb('entrevista')->nullable();
            $table->jsonb('exp_laboral')->nullable();
            $table->jsonb('exp_docente')->nullable();
            $table->biginteger('telefono_casa')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->string('entidad_nacimiento')->nullable();
            $table->string('municipio_nacimiento')->nullable();
            $table->string('localidad_nacimiento')->nullable();
            $table->string('clave_loc_nacimiento')->nullable();
            $table->integer('codigo_postal')->nullable();
            $table->string('curriculum')->nullable();
            $table->string('arch_curriculum_personal')->nullable();
            $table->jsonb('data_perfil')->nullable();
            $table->jsonb('data_especialidad')->nullable();
            $table->integer('id_oficial');
            $table->boolean('registro_activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructor', function (Blueprint $table) {
            //
        });
    }
}
