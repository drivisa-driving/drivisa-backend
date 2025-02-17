<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromDrivisaFinalTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__final_test_results', function (Blueprint $table) {
            $table->dropColumn('final_test_key_id');
        });
        Schema::table('drivisa__final_test_results', function (Blueprint $table) {
            $table->dropColumn('mark_first');
        });
        Schema::table('drivisa__final_test_results', function (Blueprint $table) {
            $table->dropColumn('mark_second');
        });
        Schema::table('drivisa__final_test_results', function (Blueprint $table) {
            $table->dropColumn('mark_third');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('drivisa__final_test_results', function (Blueprint $table) {
            $table->unsignedInteger('final_test_key_id');
        });
        Schema::create('drivisa__final_test_results', function (Blueprint $table) {
            $table->boolean('mark_first');
        });
        Schema::create('drivisa__final_test_results', function (Blueprint $table) {
            $table->boolean('mark_second');
        });
        Schema::create('drivisa__final_test_results', function (Blueprint $table) {
            $table->boolean('mark_third');
        });
    }
}
