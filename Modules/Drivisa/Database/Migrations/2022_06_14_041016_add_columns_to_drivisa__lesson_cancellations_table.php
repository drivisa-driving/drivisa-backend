<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDrivisaLessonCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->unsignedInteger('refund_choice')->nullable();
            $table->unsignedInteger('is_refunded')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('refund_choice');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('is_refunded');
        });
    }
}
