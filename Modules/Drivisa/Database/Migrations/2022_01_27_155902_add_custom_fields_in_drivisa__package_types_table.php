<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomFieldsInDrivisaPackageTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->boolean('instructor')->default(false);
            $table->boolean('drivisa')->default(false);
            $table->boolean('pdio')->default(false);
            $table->boolean('mto')->default(false);
            $table->boolean('instructor_cancel_fee')->default(false);
            $table->boolean('drivisa_cancel_fee')->default(false);
            $table->boolean('pdio_cancel_fee')->default(false);
            $table->boolean('mto_cancel_fee')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('instructor');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('drivisa');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('pdio');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('mto');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('instructor_cancel_fee');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('drivisa_cancel_fee');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('pdio_cancel_fee');
        });

        Schema::table('drivisa__package_types', function (Blueprint $table) {
            $table->dropColumn('mto_cancel_fee');
        });
    }
}
