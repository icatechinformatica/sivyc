<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposToAlumnosPreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            $table->boolean('empleado')->nullable();
            $table->date('fecha_expedicion_curp')->nullable();
            $table->date('fecha_expedicion_acta_nacimiento')->nullable();
            $table->date('fecha_vigencia_comprobante_migratorio')->nullable();
            //php artisan migrate --path=database/migrations/2021_07_27_151228_add_campos_to_alumnos_pre_table.php  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_pre', function (Blueprint $table) {
            $table->dropColumn('empleado');
            $table->dropColumn('fecha_expedicion_curp');
            $table->dropColumn('fecha_expedicion_acta_nacimiento');
            $table->dropColumn('fecha_vigencia_comprobante_migratorio');
        });
    }
}
