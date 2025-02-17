<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaInstructorEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__instructor_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('instructor_id');
            $table->tinyInteger('type');
            $table->float('amount')->nullable();
            $table->float('additional_cost')->nullable();
            $table->float('tax')->nullable();
            $table->float('total_amount')->nullable();

            $table->foreign('lesson_id')->references('id')->on('drivisa__lessons')->cascadeOnDelete();
            $table->foreign('instructor_id')->references('id')->on('drivisa__instructors')->cascadeOnDelete();

            $table->softDeletes();
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
        Schema::dropIfExists('drivisa__instructor_earnings');
    }
}
