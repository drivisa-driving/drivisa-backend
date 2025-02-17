<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Drivisa\Entities\Lesson;

class CreateDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__lessons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('no');
            // Start and end times that lesson should be
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            // when the instructor start the lesson
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->tinyInteger('status')->default(Lesson::STATUS['reserved']);
            $table->boolean('is_request')->nullable(); // if the trainee send a request to open a date for reservation
            $table->boolean('confirmed')->default(false); // if the request confirmed by instructor
            $table->float('cost')->nullable();
            $table->float('commission')->nullable(); // Drivisa commission
            $table->float('net_amount')->nullable(); // Instructor payment net amount
            $table->float('tax')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->json('pickup_point')->nullable(); // {latitude: '', longitude: '', city: ''}
            $table->json('dropoff_point')->nullable();
            $table->text('instructor_note')->nullable();
            $table->json('instructor_evaluation')->nullable();
            $table->text('trainee_note')->nullable();
            $table->json('trainee_evaluation')->nullable();

            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('trainee_id');
            $table->unsignedBigInteger('created_by');

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('instructor_id')->references('id')->on('drivisa__instructors')->cascadeOnDelete();
            $table->foreign('trainee_id')->references('id')->on('drivisa__trainees')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivisa__lessons');
    }
}
