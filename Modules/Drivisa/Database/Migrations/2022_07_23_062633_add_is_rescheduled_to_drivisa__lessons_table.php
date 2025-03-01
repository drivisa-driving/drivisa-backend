<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRescheduledToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->boolean('is_rescheduled')->default(false);
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
            $table->dropColumn('is_rescheduled');
        });
    }
}
