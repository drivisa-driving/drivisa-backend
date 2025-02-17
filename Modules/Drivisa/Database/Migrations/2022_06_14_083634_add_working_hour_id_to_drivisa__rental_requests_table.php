<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkingHourIdToDrivisaRentalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('working_hour_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->dropColumn('working_hour_id');
        });
    }
}
