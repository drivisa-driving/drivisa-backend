<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLessonEndNotificationToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->timestamp('lesson_end_notification_sent_at')->nullable();
            $table->integer('lesson_end_notification_count')->default(0);
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
            $table->dropColumn('lesson_end_notification_sent_at');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('lesson_end_notification_count');
        });
    }
}
