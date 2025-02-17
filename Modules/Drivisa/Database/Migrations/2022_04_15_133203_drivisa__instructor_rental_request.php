<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DrivisaInstructorRentalRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__instructor_rental_request', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignId('instructor_id')->constrained('drivisa__instructors')->onDelete('cascade');
            $table->foreignId('rental_request_id')->constrained('drivisa__rental_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
