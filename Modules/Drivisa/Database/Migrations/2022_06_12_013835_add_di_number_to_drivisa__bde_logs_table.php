<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiNumberToDrivisaBdeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__bde_logs', function (Blueprint $table) {
            $table->dropColumn('instructor_initial');
        });

        Schema::table('drivisa__bde_logs', function (Blueprint $table) {
            $table->string('di_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__bde_logs', function (Blueprint $table) {

        });
    }
}
