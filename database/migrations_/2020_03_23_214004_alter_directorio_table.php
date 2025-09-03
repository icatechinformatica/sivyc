<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDirectorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directorio', function (Blueprint $table) {
            $table->dropColumn('correo');
            $table->dropColumn('telefono');
            $table->string('numero_enlace');
            $table->string('categoria');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directorio', function (Blueprint $table) {
            //
        });
    }
}
