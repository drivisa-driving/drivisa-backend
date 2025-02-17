<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaBdeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__bde_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trainee_id')->index();
            $table->unsignedInteger('instructor_id')->index();
            $table->integer('lesson_id')->index();
            $table->integer('bde_number');
            $table->text('instructor_initial');
            $table->text('instructor_sign');
            $table->text('trainee_sign');
            $table->text('notes')->nullable();

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
        Schema::dropIfExists('drivisa__bde_logs');
    }
}
