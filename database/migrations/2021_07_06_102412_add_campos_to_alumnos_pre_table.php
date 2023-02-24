<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            $table->bigInteger('telefono_personal')->nullable();
            $table->bigInteger('telefono_casa')->nullable();
            $table->string('etnia',100)->nullable();
            $table->boolean('indigena')->nullable();
            $table->boolean('inmigrante')->nullable();
            $table->boolean('madre_soltera')->nullable();
            $table->boolean('familia_inmigrante')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->boolean('recibir_publicaciones')->nullable();
            $table->json('requisitos')->nullable();
            $table->bigInteger('id_cerss')->nullable();
            $table->boolean('id_user_created')->nullable();
            $table->boolean('id_user_update_activo')->nullable();
            $table->boolean('empleado')->nullable();
            $table->date('fecha_expedicion_curp')->nullable();
            $table->date('fecha_expedicion_acta_nacimiento')->nullable();
            $table->date('fecha_vigencia_comprobante_migratorio')->nullable();
            //php artisan migrate --path=database/migrations/2021_07_06_102412_add_campos_to_alumnos_pre_table.php
            $table->foreign('id_cerss')
                  ->references('id')->on('cerss')
                  ->onDelete('set null')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            $table->dropColumn('telefono_personal');
            $table->dropColumn('telefono_casa');
            $table->dropColumn('etnia');
            $table->dropColumn('indigena');
            $table->dropColumn('inmigrante');
            $table->dropColumn('madre_soltera');
            $table->dropColumn('familia_inmigrante');
            $table->dropColumn('facebook');
            $table->dropColumn('twitter');
            $table->dropColumn('recibir_publicaciones');
            $table->dropColumn('requisitos');
            $table->dropColumn('id_cerss');
            $table->dropColumn('id_user_created');
            $table->dropColumn('id_user_update_activo');
        });
    }
}
