<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('isr', function (Blueprint $table) {
            $table->id();
            $table->date('vigencia');
            $table->float('limite_inferior', 10, 2);
            $table->float('limite_superior', 10, 2);
            $table->float('cuota_fija', 10, 2);
            $table->float('porcentaje', 8, 2);
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
        Schema::dropIfExists('isr');
    }
}
