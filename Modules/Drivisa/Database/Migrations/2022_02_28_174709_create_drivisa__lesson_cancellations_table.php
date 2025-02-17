<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaLessonCancellationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__lesson_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('drivisa__lessons')->cascadeOnDelete();
            $table->timestamp('cancel_at');
            $table->enum('cancel_by', ['instructor', 'trainee', 'admin']);
            $table->mediumText('reason');
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
        Schema::dropIfExists('lesson_cancellations');
    }
}
