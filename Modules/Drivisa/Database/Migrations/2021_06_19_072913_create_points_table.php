<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__points', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('source_name')->nullable();
            $table->string('source_address')->nullable();
            $table->string('destination_name')->nullable();
            $table->string('destination_address')->nullable();
            $table->double('source_latitude');
            $table->double('source_longitude');
            $table->double('destination_latitude');
            $table->double('destination_longitude');
            $table->boolean('is_active')->default(false);
            $table->foreignId('instructor_id')->constrained('drivisa__instructors');
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
        Schema::dropIfExists('drivisa__points');
    }
}
