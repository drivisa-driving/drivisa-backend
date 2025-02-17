<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropLessonIdConstraintDrivisaPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__purchases', function (Blueprint $table) {
            $table->morphs('purchaseable');

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
            $table->dropMorphs('purchaseable');
        });
    }
}
