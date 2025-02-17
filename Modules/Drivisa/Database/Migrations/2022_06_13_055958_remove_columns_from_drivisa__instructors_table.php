<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromDrivisaInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->dropColumn('licence_type');
        });

        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->dropColumn('licence_start_date');
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
            $table->string('licence_type')->nullable();
        });

        Schema::table('drivisa__instructors', function (Blueprint $table) {
            $table->date('licence_start_date')->nullable();
        });
    }
}
