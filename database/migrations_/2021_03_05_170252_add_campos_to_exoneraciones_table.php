<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToExoneracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exoneraciones', function (Blueprint $table) {
            $table->string('status')->nullable();
            $table->string('activo')->nullable();

            $table->string('grupo_beneficiado')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exoneraciones', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('activo');

            $table->string('grupo_beneficiado')->change();
        });
    }
}
