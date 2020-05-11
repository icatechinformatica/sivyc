<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsTblUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_unidades', function (Blueprint $table) {
            // modificacion 2 campos
            $table->string('jcyc', 255)->nullable();
            $table->string('pjcyc', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_unidades', function (Blueprint $table) {
            //
        });
    }
}
