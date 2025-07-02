<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblWspColaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_wsp_cola', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 20);
            $table->text('mensaje');
            $table->string('estatus', 20)->default('cola'); // pendiente, enviado, error
            $table->timestamp('sent_at');
            $table->id('id_user_sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_wsp_cola');
    }
}
