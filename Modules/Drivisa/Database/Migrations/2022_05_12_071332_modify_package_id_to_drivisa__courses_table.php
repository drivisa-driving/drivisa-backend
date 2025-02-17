<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPackageIdToDrivisaCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__courses', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__courses', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
        });
    }
}
