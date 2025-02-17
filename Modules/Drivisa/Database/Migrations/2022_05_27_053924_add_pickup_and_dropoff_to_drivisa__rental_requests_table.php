<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickupAndDropoffToDrivisaRentalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->json('pickup_point')->nullable(); // {latitude: '', longitude: '', address: ''}
            $table->json('dropoff_point')->nullable();// {latitude: '', longitude: '', address: ''}
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
            $table->dropColumn('dropoff_point');
        });

        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->dropColumn('pickup_point');
        });
    }
}
