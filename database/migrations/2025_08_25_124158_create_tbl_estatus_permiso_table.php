<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_estatus_permiso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estatus_id')->constrained('tbl_aux_estatus');
            $table->foreignId('permiso_id')->constrained('tblz_permisos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_estatus_permiso');
    }
};
