<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterInstructorPerfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructor_perfil', function (Blueprint $table) {
            $table->dropColumn('perfil_profesional');
            $table->bigInteger('critero_pago_id')->nullable();
            $table->foreign('critero_pago_id')->references('id')->on('criterio_pago');


            /**
             * llave foranea
             */
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
