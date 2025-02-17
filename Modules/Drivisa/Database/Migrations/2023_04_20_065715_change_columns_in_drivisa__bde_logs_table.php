<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsInDrivisaBdeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__bde_logs', function (Blueprint $table) {
            $table->text('instructor_sign')->nullable()->change();
        });

        Schema::table('drivisa__bde_logs', function (Blueprint $table) {
            $table->text('trainee_sign')->nullable()->change();
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
            $table->text('instructor_sign');
        });

        Schema::table('drivisa__bde_logs', function (Blueprint $table) {
            $table->text('trainee_sign');
        });
    }
}
