<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCursosTableUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_cursos', function (Blueprint $table) {
            //
            $table->dropColumn('grupo');
            $table->dropColumn('pini');
            $table->dropColumn('pfin');
            $table->dropColumn('hini2');
            $table->dropColumn('hfin2');
            $table->dropColumn('turno');
            $table->dropColumn('dia2');
            $table->string('modinstructor', 255)->nullable();
            $table->string('nmunidad', 255)->nullable();
            $table->string('nmacademico', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('status', 255)->nullable();
            $table->string('realizo', 255)->nullable();
            $table->string('valido', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_cursos', function (Blueprint $table) {
            //
        });
    }
}
