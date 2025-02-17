<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Drivisa\Entities\WorkingHour;

class CreateWorkingHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivisa__working_hours', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->tinyInteger('status')->default(WorkingHour::STATUS['available']);
            $table->time('open_at');
            $table->time('close_at');
            $table->foreignId('working_day_id')->constrained('drivisa__working_days')
                ->onDelete('cascade');
            $table->foreignId('point_id')->constrained('drivisa__points')
                ->onDelete('cascade');
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
        Schema::dropIfExists('drivisa__working_hours');
    }
}
