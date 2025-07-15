<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tblz_usuarios', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('registro_id')->nullable()->after('id');
            $table->string('registro_type')->nullable()->after('registro_id');
            $table->date('fecha_caducidad')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tblz_usuarios', function (Blueprint $table) {
            //
        });
    }
};
