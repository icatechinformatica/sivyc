<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTblInscripcion.php extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_inscripcion', function (Blueprint $table) {   
            $table->bigInteger('id_afolios')->default(0)->nullable();
            $table->string('folio', 25)->default(0)->nullable();
            $table->date('fecha_folio')->nullable();            
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
