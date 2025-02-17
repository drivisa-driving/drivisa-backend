<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('drivisa__instructors')
                ->onDelete('cascade');
            $table->string('make');
            $table->string('model')->nullable();
            $table->string('generation')->nullable();
            $table->string('trim')->nullable();
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
        Schema::dropIfExists('drivisa__cars');
    }
}
