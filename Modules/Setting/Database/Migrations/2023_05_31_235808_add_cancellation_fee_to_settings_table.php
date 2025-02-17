<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancellationFeeToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_driving_cancellation_fee_after_time',
                'value' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'drivisa_driving_cancellation_fee_after_time',
                'value' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_bde_cancellation_fee_after_time',
                'value' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'drivisa_bde_cancellation_fee_after_time',
                'value' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_driving_cancellation_fee',
                'value' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_bde_cancellation_fee',
                'value' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g_test_cancellation_fee',
                'value' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g2_test_cancellation_fee',
                'value' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g_test_cancellation_fee_after_time',
                'value' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'drivisa_g_test_cancellation_fee_after_time',
                'value' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g2_test_cancellation_fee_after_time',
                'value' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'drivisa_g2_test_cancellation_fee_after_time',
                'value' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_driving_cancellation_fee_after_time')->delete();
        });


        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'drivisa_driving_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_bde_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'drivisa_bde_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_driving_cancellation_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_bde_cancellation_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g_test_cancellation_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g2_test_cancellation_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g_test_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'drivisa_g_test_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g2_test_cancellation_fee_after_time')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'drivisa_g2_test_cancellation_fee_after_time')->delete();
        });
    }
}
