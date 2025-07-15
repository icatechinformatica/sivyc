<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteColumnZonaToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('especialidad_instructor_curso', function (Blueprint $table) {
            //
            $table->dropForeign('especialidad_instructor_curso_pago_id_foreign');
            $table->dropColumn(['zona', 'pago_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('especialidad_instructor_curso', function (Blueprint $table) {
            //
        });
    }
}
