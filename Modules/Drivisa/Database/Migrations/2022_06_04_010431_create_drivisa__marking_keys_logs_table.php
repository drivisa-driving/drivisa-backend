<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivisaMarkingKeysLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__marking_keys_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('bde_log_id');
            $table->unsignedInteger('marking_key_id');
            $table->unsignedInteger('mark')->comment("1");
            
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
        Schema::dropIfExists('drivisa__marking_keys_logs');
    }
}
