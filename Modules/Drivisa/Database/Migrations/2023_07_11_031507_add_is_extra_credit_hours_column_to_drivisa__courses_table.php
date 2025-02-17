<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsExtraCreditHoursColumnToDrivisaCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__courses', function (Blueprint $table) {
            $table->boolean('is_extra_credit_hours')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__courses', function (Blueprint $table) {
            $table->dropColumn('is_extra_credit_hours');
        });
    }
}
