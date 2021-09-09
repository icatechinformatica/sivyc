<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionToTblMunicipiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_municipios', function (Blueprint $table) {
            $table->string('region')->nullable();
        });
        Schema::table('tbl_cursos', function (Blueprint $table) {
            $table->string('region')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_municipios', function (Blueprint $table) {
            $table->dropColumn('region');
        });
        Schema::table('tbl_cursos', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
}
