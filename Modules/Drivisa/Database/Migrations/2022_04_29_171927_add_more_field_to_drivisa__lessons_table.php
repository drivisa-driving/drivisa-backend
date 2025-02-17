<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldToDrivisaLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->float('additional_tax')->default(0)->after('tax');
            $table->float('additional_cost')->default(0)->after('additional_tax');
            $table->float('additional_km')->default(0)->after('additional_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('additional_tax');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('additional_cost');
        });

        Schema::table('drivisa__lessons', function (Blueprint $table) {
            $table->dropColumn('additional_km');
        });
    }
}
