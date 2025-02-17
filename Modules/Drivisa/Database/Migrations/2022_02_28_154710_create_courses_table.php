<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('drivisa__packages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->bigInteger('status');
            $table->bigInteger('credit');
            $table->string('payment_intent_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('charge_id')->nullable();
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
        Schema::dropIfExists('drivisa__courses');
    }
}
