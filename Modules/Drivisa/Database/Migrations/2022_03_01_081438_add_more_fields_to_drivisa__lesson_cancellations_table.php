<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToDrivisaLessonCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->string('refund_id')->nullable();
            $table->float('instructor_fee')->nullable();
            $table->float('drivisa_fee')->nullable();
            $table->float('pdio_fee')->nullable();
            $table->float('mto_fee')->nullable();
            $table->string('time_left')->nullable();
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
            $table->dropColumn('refund_id');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('instructor_fee');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('drivisa_fee');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('pdio_fee');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('mto_fee');
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->dropColumn('time_left');
        });
    }
}
