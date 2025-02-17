<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyLessonIdToDrivisaPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__purchases', function (Blueprint $table) {
            $table->foreignId('lesson_id')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__purchases', function (Blueprint $table) {
            $table->foreignId('lesson_id')
                ->nullable(false)
                ->change();
        });
    }
}
