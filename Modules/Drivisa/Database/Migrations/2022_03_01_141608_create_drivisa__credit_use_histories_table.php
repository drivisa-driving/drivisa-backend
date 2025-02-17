<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaCreditUseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__credit_use_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('drivisa__courses')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('drivisa__lessons')->cascadeOnDelete();
            $table->timestamp('used_at');
            $table->decimal('credit_used');
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
        Schema::dropIfExists('drivisa__credit_use_histories');
    }
}
