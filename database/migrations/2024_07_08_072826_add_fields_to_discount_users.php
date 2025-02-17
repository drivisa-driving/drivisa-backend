<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDiscountUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('discount_users', function (Blueprint $table) {
           $table->string('type')->nullable();
           $table->unsignedBigInteger('type_id');
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
            $table->dropColumn('type');
            $table->dropColumn('type_id');
        });
    }
}
