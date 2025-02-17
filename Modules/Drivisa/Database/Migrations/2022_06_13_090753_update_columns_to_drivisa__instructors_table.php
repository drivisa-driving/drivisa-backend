<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnsToDrivisaInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->string('licence_number')->nullable()->change();
            $table->string('di_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->unsignedInteger('licence_number')->nullable();
        });

        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->unsignedInteger('di_number')->nullable();
        });
    }
}
