<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaveOrdenColumnToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['id_padre']);
            $table->dropColumn('id_padre');
            $table->dropColumn('menu');
            $table->string('clave_orden')->nullable()->after('id');
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
            $table->dropColumn('clave_orden');
            $table->boolean('menu')->default(false)->after('clave_orden');
            $table->foreignId('id_padre')->nullable()->constrained('permissions')->onDelete('cascade')->after('clave_orden');
        });
    }
}
