<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToTableTblFuncionariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_funcionarios', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('cargo_id')->nullable()->after('correo_institucional');
            $table->foreign('cargo_id')->references('id')->on('cat_cargos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_funcionarios', function (Blueprint $table) {
            //
            $table->dropForeign(['cargo_id']);
            $table->dropColumn('cargo_id');
        });
    }
}
