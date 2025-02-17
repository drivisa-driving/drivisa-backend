<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('player_id')->nullable();
            $table->string('identifier')->nullable();
            $table->integer('type')->nullable();
            $table->string('os')->nullable();
            $table->string('model')->nullable();
            $table->string('language')->nullable();
            $table->string('token')->nullable();
            $table->text('tags')->nullable();
            $table->smallInteger('banned')->nullable();
            $table->timestamp('last_active')->nullable();
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
        Schema::dropIfExists('user_devices');
    }
}
