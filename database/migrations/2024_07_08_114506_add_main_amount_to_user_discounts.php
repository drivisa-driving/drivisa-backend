<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainAmountToUserDiscounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_users', function (Blueprint $table) {
           $table->float('main_amount')->after('discount_type');
           $table->float('after_discount')->after('total_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discount_users', function (Blueprint $table) {
            $table->dropColumn('main_amount');
            $table->dropColumn('after_discount');
        });
    }
}
