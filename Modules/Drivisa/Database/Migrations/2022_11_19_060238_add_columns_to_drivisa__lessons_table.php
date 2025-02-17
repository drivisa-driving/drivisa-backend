<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_use_histories_id')->nullable();
            $table->boolean('is_bonus_credit')->default(false);
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
            $table->dropColumn('credit_use_histories_id');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('is_bonus_credit');
        });
    }
}
