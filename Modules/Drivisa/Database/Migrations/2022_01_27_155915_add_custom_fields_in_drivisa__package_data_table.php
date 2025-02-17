<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFieldsInDrivisaPackageDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->unsignedInteger('instructor')->default(0);
            $table->unsignedInteger('drivisa')->default(0);
            $table->unsignedInteger('pdio')->default(0);
            $table->unsignedInteger('mto')->default(0);
            $table->unsignedInteger('instructor_cancel_fee')->default(0);
            $table->unsignedInteger('drivisa_cancel_fee')->default(0);
            $table->unsignedInteger('pdio_cancel_fee')->default(0);
            $table->unsignedInteger('mto_cancel_fee')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('instructor');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('drivisa');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            ;
            $table->dropColumn('pdio');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('mto');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('instructor_cancel_fee');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('drivisa_cancel_fee');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('pdio_cancel_fee');
        });

        Schema::table('drivisa__package_data', function (Blueprint $table) {
            $table->dropColumn('mto_cancel_fee');
        });
    }
}
