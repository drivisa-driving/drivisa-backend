<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnToDrivisaComplaintTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->string('name');
        });

        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->string('email');
        });

        Schema::table('drivisa__complaint', function (Blueprint $table) {
            $table->string('phone')->nullable();
        });
    }
}
