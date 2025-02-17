<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaFinalTestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__final_test_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('final_test_result_id');
            $table->integer('final_test_key_id');
            $table->boolean('mark_first');
            $table->boolean('mark_second');
            $table->boolean('mark_third');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivisa__final_test_logs');
    }
}
