<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaSavedLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__saved_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')->constrained('drivisa__trainees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('address');
            $table->enum('type', ['pick', 'drop', 'pick-drop']);
            $table->tinyInteger('default')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saved_locations');
    }
}
