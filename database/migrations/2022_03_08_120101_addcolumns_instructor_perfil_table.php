<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD:database/migrations/2022_03_08_120101_addcolumns_instructor_perfil_table.php
class AddcolumnsInstructorPerfilTable extends Migration
=======
class AddpoaTblUnidadesTable extends Migration
>>>>>>> be97eaf4516bbc0c5608b168d03c16b7045b4aeb:database/migrations/2022_06_01_105806_addpoa_tbl_unidades_table.php
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
<<<<<<< HEAD:database/migrations/2022_03_08_120101_addcolumns_instructor_perfil_table.php
        Schema::table('instructor_perfil', function (Blueprint $table) {
            $table->string('status')->nullable();
=======
        Schema::table('tbl_unidades', function (Blueprint $table) {
            $table->integer('order_poa')->nullable();
>>>>>>> be97eaf4516bbc0c5608b168d03c16b7045b4aeb:database/migrations/2022_06_01_105806_addpoa_tbl_unidades_table.php
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD:database/migrations/2022_03_08_120101_addcolumns_instructor_perfil_table.php
        Schema::table('instructor_perfil', function (Blueprint $table) {
=======
        Schema::table('tbl_unidades', function (Blueprint $table) {
>>>>>>> be97eaf4516bbc0c5608b168d03c16b7045b4aeb:database/migrations/2022_06_01_105806_addpoa_tbl_unidades_table.php
            //
        });
    }
}
