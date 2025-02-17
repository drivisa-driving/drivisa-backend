<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaFinalTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__final_test_results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('final_test_key_id');
            $table->unsignedInteger('bde_log_id');
            $table->boolean('mark_first');
            $table->boolean('mark_second');
            $table->boolean('mark_third');
            $table->bigInteger('instructor_id');
            $table->text('instructor_sign');
            $table->integer('final_marks');
            $table->boolean('is_pass');
            $table->string('di_number');

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
        Schema::dropIfExists('drivisa__final_test_results');
    }
}
