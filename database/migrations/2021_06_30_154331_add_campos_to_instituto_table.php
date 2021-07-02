<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToInstitutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_instituto', function (Blueprint $table) {
            $table->string('titular')->nullable();
            $table->string('cargo')->nullable();
            $table->string('correo_titular')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_instituto', function (Blueprint $table) {
            $table->dropColumn('titular');
            $table->dropColumn('cargo');
            $table->dropColumn('correo_titular');
        });
    }
}
