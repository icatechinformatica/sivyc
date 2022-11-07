<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreliminarToTblCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_cursos', function (Blueprint $table) {
            $table->string('num_revision')->nullable();
            $table->string('num_revision_arc02')->nullable();
            $table->string('status_solicitud')->nullable();
            $table->string('mextemporaneo')->nullable();
            $table->text('rextemporaneo')->nullable();
            $table->string('status_solicitud_arc02')->nullable();
            $table->string('mextemporaneo_arc02')->nullable();
            $table->text('rextemporaneo_arc02')->nullable();
            $table->text('obspreliminar')->nullable();
            //php artisan migrate --path=database/migrations/2022_04_06_105439_add_preliminar_to_tbl_cursos_table.php
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
            $table->dropColumn('num_revision');
            $table->dropColumn('num_revision_arc02');
            $table->dropColumn('status_solicitud');
            $table->dropColumn('mextemporaneo');
            $table->dropColumn('rextemporaneo');
            $table->dropColumn('status_solicitud_arc02');
            $table->dropColumn('mextemporaneo_arc02');
            $table->dropColumn('rextemporaneo_arc02');
            $table->dropColumn('obspreliminar');
        });
    }
}
