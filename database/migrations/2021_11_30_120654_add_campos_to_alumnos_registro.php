<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAlumnosRegistro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            $table->date('inicio')->nullable();
            $table->date('termino')->nullable();
            $table->unsignedBigInteger('id_muni')->nullable();
            $table->unsignedBigInteger('clave_localidad')->nullable();
            $table->string('organismo_publico')->nullable();
            $table->unsignedBigInteger('id_organismo')->nullable();
            $table->string('grupo_vulnerable')->nullable();
            $table->unsignedBigInteger('id_vulnerable')->nullable();
            //php artisan migrate --path=database/migrations/2021_11_30_120654_add_campos_to_alumnos_registro.php
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_registro', function (Blueprint $table) {
            $table->dropColumn('inicio');
            $table->dropColumn('termino');
            $table->dropColumn('hini');
            $table->dropColumn('hfin');
            $table->dropColumn('id_muni');
            $table->dropColumn('localidad');
            $table->dropColumn('organismo_publico');
            //
        });
    }
}
