<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivisaInstructorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__instructors', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('no');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->string('licence_type', 8)->nullable();
            $table->date('licence_start_date')->nullable();
            $table->date('licence_end_date')->nullable();
            $table->boolean('verified')->default(false);
            $table->text('bio')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivisa__instructors');
    }
}
