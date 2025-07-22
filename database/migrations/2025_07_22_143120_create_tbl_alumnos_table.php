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
        Schema::create('tbl_alumnos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100);
            $table->string('curp', 18)->unique();
            $table->string('matricula', 30)->unique()->nullable();
            $table->date('fecha_nacimiento');

            // Datos de contacto y domicilio
            $table->string('correo', 50)->nullable();
            $table->string('telefono_celular', 20)->nullable();
            $table->string('telefono_casa', 20)->nullable();
            $table->string('domicilio', 255)->nullable();
            $table->string('colonia', 100)->nullable();
            $table->string('cp', 5)->nullable();
            $table->string('clave_localidad', 100)->nullable();
            $table->string('facebook', 100)->nullable();

            $table->string('empresa_trabaja', 100)->nullable();
            $table->string('antiguedad', 100)->nullable();
            $table->string('direccion_empresa', 100)->nullable();
            $table->string('sistema_capacitacion_especificar')->nullable();

            $table->string('medio_entero', 100)->nullable();
            $table->string('medio_confirmacion', 100)->nullable();

            $table->json('archivos_documentos');
            $table->json('cerss')->nullable();
            $table->json('vulnerable')->nullable();
            $table->json('datos_alfa')->nullable();
            $table->json('datos_incorporacion')->nullable();
            $table->jsonb('movimientos')->nullable();

            $table->boolean('recibir_publicaciones')->default(false);
            $table->boolean('empleado')->default(false);
            $table->boolean('curso_extra')->default(false);
            $table->boolean('servidor_publico')->default(false);
            $table->boolean('check_bolsa')->default(false);
            $table->boolean('esta_activo')->default(true);


            $table->foreignId('id_sexo')->nullable()->references('id')->on('tbl_sexo')->onDelete('set null');
            $table->foreignId('id_municipio')->nullable()->references('id')->on('tbl_municipios')->onDelete('set null');
            $table->foreignId('id_estado')->nullable()->references('id')->on('estados')->onDelete('set null');
            $table->foreignId('id_estado_civil')->nullable()->references('id')->on('estado_civil')->onDelete('set null');
            $table->foreignId('id_discapacidad')->nullable()->references('id_discapacidad')->on('tbl_cat_discapacidades')->onDelete('set null');
            $table->foreignId('id_ultimo_grado_estudios')->nullable()->references('id_grado_estudio')->on('tbl_cat_grado_estudios')->onDelete('set null');
            $table->foreignId('id_nacionalidad')->references('id_nacionalidad')->on('tbl_cat_nacionalidades')->onDelete('set null');
            $table->foreignId('id_funcionario_realizo')->nullable()->references('id')->on('tbl_funcionarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_alumnos');
    }
};
