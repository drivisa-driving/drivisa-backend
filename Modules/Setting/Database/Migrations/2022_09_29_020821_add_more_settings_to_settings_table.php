<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreSettingsToSettingsTable extends Migration
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
                'name' => 'instructor_driving_fee',
                'value' => 45,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_bde_fee',
                'value' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g_test_fee',
                'value' => 125,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'name' => 'instructor_g2_test_fee',
                'value' => 115,
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
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_driving_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {

            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_bde_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {

            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g_test_fee')->delete();
        });

        Schema::table('settings', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::table('settings')->where('name', 'instructor_g2_test_fee')->delete();
        });
    }
}
