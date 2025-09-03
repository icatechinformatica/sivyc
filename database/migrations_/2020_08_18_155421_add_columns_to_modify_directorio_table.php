<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToModifyDirectorioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('directorio', function (Blueprint $table) {
            // nuevos campos
            $table->integer('area_adscripcion_id')->unsigned()->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('qr_generado')->default(false);

            $table->foreign('area_adscripcion_id')->references('id')->on('area_adscripcion')
            ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('directorio', function (Blueprint $table) {
            //
        });
    }
}
