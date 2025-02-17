<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentFieldsToDrivisaRentalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->float('additional_tax')->default(0);
            $table->float('additional_cost')->default(0);
            $table->float('additional_km')->default(0);
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
            $table->dropColumn('additional_tax');
        });

        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->dropColumn('additional_cost');
        });

        Schema::table('drivisa__rental_requests', function (Blueprint $table) {
            $table->dropColumn('additional_km');
        });
    }
}
