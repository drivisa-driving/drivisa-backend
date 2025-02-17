<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaRentalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__rental_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('location');
            $table->string('latitude');
            $table->string('longitude');
            $table->date('booking_date');
            $table->time('booking_time');
            $table->smallInteger('status');
            $table->foreignId('purchase_id')->nullable();
            $table->foreignId('instructor_id')->nullable();
            $table->timestamps();

            $table->foreignId('trainee_id')->constrained('drivisa__trainees')
                ->onDelete('cascade');

            $table->foreignId('package_id')->constrained('drivisa__packages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_requests');
    }
}
