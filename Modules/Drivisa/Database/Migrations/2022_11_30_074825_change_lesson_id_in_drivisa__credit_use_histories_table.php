<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLessonIdInDrivisaCreditUseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__credit_use_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__credit_use_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable(false)->change();
        });
    }
}
