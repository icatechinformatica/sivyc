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
        Schema::table('tbl_grupos', function (Blueprint $table) {
            $table->foreignId('id_usuario_captura')->nullable()->constrained('tblz_usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_grupos', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_captura']);
            $table->dropColumn('id_usuario_captura');
        });
    }
};
