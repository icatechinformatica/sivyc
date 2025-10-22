<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrosschexLiveTable extends Migration
{
    public function up()
    {
        Schema::create('crosschex_live', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->jsonb('headers')->nullable();
            $table->jsonb('payload')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestampTz('received_at')->useCurrent();
            $table->timestamps(); // created_at / updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('crosschex_live');
    }
}
