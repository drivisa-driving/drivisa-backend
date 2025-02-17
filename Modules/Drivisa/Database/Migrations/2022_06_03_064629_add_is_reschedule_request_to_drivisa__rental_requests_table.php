<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRescheduleRequestToDrivisaRentalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->unsignedInteger('is_reschedule_request')->default(0);
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
            $table->dropColumn('is_reschedule_request');
        });
    }
}
