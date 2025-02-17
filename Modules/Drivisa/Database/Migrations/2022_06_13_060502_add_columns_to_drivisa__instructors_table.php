<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDrivisaInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->unsignedInteger('licence_number')->nullable();
            $table->unsignedInteger('di_number')->nullable();
            $table->date('di_end_date')->nullable();
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
            $table->dropColumn('licence_number');
        });

        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->dropColumn('di_number');
        });

        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->dropColumn('di_end_date');
        });
    }
}
