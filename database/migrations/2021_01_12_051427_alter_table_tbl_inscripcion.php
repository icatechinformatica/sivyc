<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTblInscripcion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inscripcion', function (Blueprint $table) {
            $table->bigInteger('id_unidad')->nullable();            
            $table->bigInteger('id_grupo')->nullable();
            $table->bigInteger('id_pre')->nullable();  
            $table->bigInteger('id_cerss')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado_civil', 30)->nullable();
            $table->string('discapacidad', 30)->nullable();
            $table->string('escolaridad', 50)->nullable();
            $table->string('nacionalidad', 50)->nullable();
            $table->string('etnia', 50)->nullable();            
            $table->boolean('indigena')->nullable();
            $table->boolean('inmigrante')->nullable();
            $table->boolean('madre_soltera')->nullable();
            $table->boolean('familia_migrante')->nullable();
             $table->string('calificacion', 15)->nullable();           
            $table->bigInteger('iduser_created')->nullable();
            $table->bigInteger('iduser_updated')->nullable(); 
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
