<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationIdToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->string('trainee_notification_id')->nullable()->comment("One Signal Notification");
            $table->string('instructor_notification_id')->nullable()->comment("One Signal Notification");
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
            $table->dropColumn('trainee_notification_id');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('instructor_notification_id');
        });
    }
}
