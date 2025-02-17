<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkingHourIdToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->bigInteger('working_hour_id')->nullable();
            $table->unique('working_hour_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('working_hour_id');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropUnique('working_hour_id');
        });
    }
}
