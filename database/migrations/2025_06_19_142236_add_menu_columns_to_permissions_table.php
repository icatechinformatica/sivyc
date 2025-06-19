<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuColumnsToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('menu')->default(false);
            $table->foreignId('id_padre')->nullable()->constrained('permissions')->onDelete('cascade');
            $table->string('icono')->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('menu');
            $table->dropForeign(['id_padre']);
            $table->dropColumn('id_padre');
            $table->dropColumn('icono');
            $table->dropColumn('activo');
        });
    }
}
