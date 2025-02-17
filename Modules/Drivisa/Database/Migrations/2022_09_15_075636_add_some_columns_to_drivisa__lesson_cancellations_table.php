<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToDrivisaLessonCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            if (!Schema::hasColumn('drivisa__lesson_cancellations', 'cancellation_fee')) {
                $table->float('cancellation_fee')->default(0);
            }

            if (!Schema::hasColumn('drivisa__lesson_cancellations', 'refund_amount')) {
                $table->float('refund_amount')->default(0);
            }
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
            if (Schema::hasColumn('drivisa__lesson_cancellations', 'cancellation_fee')) {
                $table->dropColumn('cancellation_fee');
            }
        });

        Schema::table('drivisa__lesson_cancellations', function (Blueprint $table) {
            if (Schema::hasColumn('drivisa__lesson_cancellations', 'refund_amount')) {
                $table->dropColumn('refund_amount');
            }
        });
    }
}
