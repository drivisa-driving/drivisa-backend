<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('drivisa__lessons')->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('drivisa__transactions')->cascadeOnDelete();
            $table->integer('type')->default(1);
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
        Schema::dropIfExists('purchases');
    }
}
